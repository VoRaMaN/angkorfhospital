<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Models\Staff;
// Note: the application's domain `Role` model is separate from Spatie's Role model.
// Spatie Role is not imported here; we simply use the role name when assigning using Spatie's trait
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Patient::class);

        $query = Patient::with('user');

        // Search functionality
        $search = request('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                // Search by patient ID
                $q->where('id', 'like', '%'.$search.'%')
                    // Search by name fields
                    ->orWhere('name', 'like', '%'.$search.'%')
                    ->orWhere('surname', 'like', '%'.$search.'%')
                    ->orWhere('khmer_china_name', 'like', '%'.$search.'%')
                    ->orWhere('khmer_china_surname', 'like', '%'.$search.'%')
                    // Search by contact info
                    ->orWhere('mobile_phone', 'like', '%'.$search.'%')
                    ->orWhere('home_phone', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    // Search by user account (name and email)
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        $patients = $query->paginate(15)->withQueryString();

        return Inertia::render('Patients/Index', [
            'patients' => $patients,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Patient::class);

        return Inertia::render('Patients/Create', [
            'doctors' => Staff::whereHas('role', fn ($q) => $q->where('name', 'Doctor'))->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Create patient record without user account
        $patient = Patient::create($data);

        return redirect()->route('patients.index')
            ->with('success', 'Patient created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(): Response
    {
        $patient = Patient::findOrFail(request('patient'));
        $this->authorize('view', $patient);

        $patient->load([
            'user',
            'appointments.staff',
            'appointments.medicalRecord',
            'appointments.visits.medicalRecord',
            'patientFiles.file',
            'medicalOrders.staff',
            'staff',
        ]);

        // Get medical records from appointments and visits
        $medicalRecords = collect();
        $medicalRecords = $medicalRecords->merge($patient->appointments->pluck('medicalRecord')->filter());
        $medicalRecords = $medicalRecords->merge($patient->appointments->pluck('visits')->flatten()->pluck('medicalRecord')->filter());
        $uniqueRecords = $medicalRecords->unique('id');

        // Get medical orders data with staff information
        $medicalOrdersData = $patient->medicalOrders->map(function ($order) {
            return [
                'id' => $order->id,
                'type' => $order->type,
                'order_details' => $order->order_details,
                'status' => $order->status,
                'priority' => $order->priority,
                'ordered_at' => $order->ordered_at,
                'completed_at' => $order->completed_at,
                'staff_name' => $order->staff?->name,
                'notes' => $order->notes,
            ];
        });

        return Inertia::render('Patients/Show', [
            'patient' => array_merge($patient->toArray(), [
                'medical_records' => $uniqueRecords->toArray(),
                'medical_orders_data' => $medicalOrdersData->toArray(),
            ]),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(): Response
    {
        $patient = Patient::findOrFail(request('patient'));
        $this->authorize('update', $patient);

        $patient->load(['user', 'patientFiles.file', 'staff']);

        return Inertia::render('Patients/Edit', [
            'patient' => $patient,
            'doctors' => Staff::whereHas('role', fn ($q) => $q->where('name', 'Doctor'))->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request): RedirectResponse
    {
        $patient = Patient::findOrFail(request('patient'));
        $this->authorize('update', $patient);

        $data = $request->validated();

        // Handle user account creation (only for patients without existing user accounts)
        if ($request->boolean('create_user_account') && ! $patient->user_id) {
            $user = User::create([
                'name' => trim($data['name'].' '.$data['surname']),
                'email' => $data['email'],
                'password' => bcrypt('password'), // Default password, should be changed by user
            ]);

            // Assign patient role
            // `App\Models\Role` is a domain model (staff roles). Spatie uses its
            // own Role model for permission checks. When creating a user account
            // for a patient we must assign the Spatie role (not the domain Role
            // model). Use the role name string so `assignRole` will work whether
            // the Spatie role exists as a model or only as a string.
            $user->assignRole('Patient');

            $patient->user_id = $user->id;
            $patient->save();

            // Remove user account fields from patient data
            unset($data['create_user_account'], $data['name'], $data['surname'], $data['email']);
        }

        $patient->update($data);

        return redirect()->route('patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): RedirectResponse
    {
        $patient = Patient::findOrFail(request('patient'));
        $this->authorize('delete', $patient);

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    /**
     * Generate a comprehensive patient report as PDF.
     */
    public function generateReport(): Response
    {
        $patient = Patient::findOrFail(request('patient'));
        $this->authorize('view', $patient);

        $patient->load(['user', 'appointments.staff', 'medicalOrders', 'visits', 'staff']);

        return Inertia::render('Patients/Report', [
            'patient' => $patient,
        ]);
    }

    /**
     * Generate a printable patient sticker as PDF.
     */
    public function generateSticker()
    {
        try {
            $patient = Patient::findOrFail(request('patient'));
            $this->authorize('view', $patient);

            // Format the sticker HTML for 12 labels in 3x4 grid
            $dob = $patient->date_of_birth_month.'/'.$patient->date_of_birth_day.'/'.$patient->date_of_birth_year;
            $gender = strtoupper(substr($patient->gender, 0, 1)); // M or F

            $stickerHtml = '
                <div style="border: 2px solid #000; border-radius: 8px; padding: 8px; text-align: center; font-size: 9px; height: 70px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); display: flex; flex-direction: column; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <div style="font-size: 11px; font-weight: bold; color: #000; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Medical Record Sticker</div>
                    <div style="margin: 3px 0; color: #374151;">
                        <strong style="color: #1f2937;">PATIENT ID:</strong><br>
                        <span style="font-family: monospace; font-size: 10px; color: #dc2626;">'.$patient->id.'</span>
                    </div>
                    <div style="margin: 3px 0; color: #374151;">
                        <strong style="color: #1f2937;">DOB:</strong><br>
                        <span style="font-size: 10px;">'.$dob.' <strong>('.$gender.')</strong></span>
                    </div>
                </div>
            ';

            // Create 12 copies in a 3x4 grid
            $html = '
            <html>
            <head>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 0; 
                        padding: 5mm; 
                        background: #f9fafb;
                    }
                    table { 
                        width: 100%; 
                        border-collapse: separate; 
                        border-spacing: 3mm;
                        page-break-inside: avoid;
                    }
                    td { 
                        padding: 0; 
                        border: none;
                        page-break-inside: avoid;
                    }
                    .sticker-container {
                        page-break-inside: avoid;
                        height: 100%;
                    }
                    @page {
                        size: A4;
                        margin: 5mm;
                    }
                </style>
            </head>
            <body>
                <div class="sticker-container">
                    <table>
                        <tr>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                        </tr>
                        <tr>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                        </tr>
                        <tr>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                        </tr>
                        <tr>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                        </tr>
                                                <tr>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                        </tr>
                        <tr>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                        </tr>
                        <tr>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                        </tr>
                        <tr>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                        </tr>
                                                <tr>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                        </tr>
                        <tr>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                            <td>'.$stickerHtml.'</td>
                        </tr>
                    </table>
                </div>
            </body>
            </html>
            ';

            $dompdf = new Dompdf;
            $dompdf->loadHtml($html);
            $dompdf->setPaper('a4');

            // Ensure single page output
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isRemoteEnabled', false);
            $dompdf->set_option('defaultFont', 'Arial');

            $dompdf->render();

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="patient_sticker.pdf"',
                'Cache-Control' => 'no-cache',
            ]);
        } catch (\Exception $e) {
            return response('Error: '.$e->getMessage(), 500);
        }
    }
}
