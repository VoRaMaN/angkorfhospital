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
use Illuminate\Support\Facades\Storage;
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

        // Show all patients or only active patients (with recent activity)
        $showAll = request('show_all');
        $search = request('search');

        // When searching, always search all patients regardless of show_all toggle
        if (! $showAll && ! $search) {
            // Only show patients with appointments or visits TODAY, or created today (after midnight, list resets)
            $query->where(function ($q) {
                $q->whereHas('appointments', function ($appointmentQuery) {
                    $appointmentQuery->whereDate('appointment_date_time', '>=', now()->startOfDay());
                })->orWhereHas('appointments.visits', function ($visitQuery) {
                    $visitQuery->whereDate('visit_date_time', '>=', now()->startOfDay());
                })->orWhereDate('created_at', now()->toDateString());
            });
        }

        // Search functionality
        if ($search) {
            $searchLower = mb_strtolower(trim($search));

            // Build a LIKE pattern that matches characters even with gaps (e.g. "heis" matches "HEISENBERG")
            $wildcardPattern = '%'.implode('%', mb_str_split($searchLower)).'%';
            $substringPattern = '%'.$searchLower.'%';

            $query->where(function ($q) use ($searchLower, $substringPattern, $wildcardPattern) {
                // Search by patient ID
                $q->where('id', 'like', $substringPattern)
                    // Search by name fields - substring match
                    ->orWhereRaw('LOWER(name) LIKE ?', [$substringPattern])
                    ->orWhereRaw('LOWER(surname) LIKE ?', [$substringPattern])
                    ->orWhereRaw("LOWER(CONCAT(COALESCE(name, ''), ' ', COALESCE(surname, ''))) LIKE ?", [$substringPattern])
                    ->orWhereRaw('LOWER(COALESCE(khmer_china_name, \'\')) LIKE ?', [$substringPattern])
                    ->orWhereRaw('LOWER(COALESCE(khmer_china_surname, \'\')) LIKE ?', [$substringPattern])
                    // Search by name fields - fuzzy/incomplete character match
                    ->orWhereRaw('LOWER(name) LIKE ?', [$wildcardPattern])
                    ->orWhereRaw('LOWER(surname) LIKE ?', [$wildcardPattern])
                    ->orWhereRaw("LOWER(CONCAT(COALESCE(name, ''), ' ', COALESCE(surname, ''))) LIKE ?", [$wildcardPattern])
                    // Phonetic matching for misspelled names (e.g. "herisen" finds "HEISENBERG")
                    ->orWhereRaw('SOUNDEX(name) = SOUNDEX(?)', [$searchLower])
                    ->orWhereRaw('SOUNDEX(surname) = SOUNDEX(?)', [$searchLower])
                    ->orWhereRaw("SOUNDEX(CONCAT(COALESCE(name, ''), ' ', COALESCE(surname, ''))) = SOUNDEX(?)", [$searchLower])
                    // Search by contact info
                    ->orWhere('mobile_phone', 'like', $substringPattern)
                    ->orWhere('home_phone', 'like', $substringPattern)
                    ->orWhereRaw('LOWER(COALESCE(email, \'\')) LIKE ?', [$substringPattern])
                    // Search by user account (name and email)
                    ->orWhereHas('user', function ($userQuery) use ($searchLower, $substringPattern, $wildcardPattern) {
                        $userQuery->whereRaw('LOWER(name) LIKE ?', [$substringPattern])
                            ->orWhereRaw('LOWER(name) LIKE ?', [$wildcardPattern])
                            ->orWhereRaw('SOUNDEX(name) = SOUNDEX(?)', [$searchLower])
                            ->orWhereRaw('LOWER(COALESCE(email, \'\')) LIKE ?', [$substringPattern]);
                    });
            });
        }

        $patients = $query->paginate(15)->withQueryString();

        return Inertia::render('Patients/Index', [
            'patients' => $patients,
            'filters' => [
                'search' => $search,
                'show_all' => (bool) $showAll,
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
        unset($data['photo']);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('patient_photos');
        }

        // Create patient record without user account
        $patient = Patient::create($data);

        return redirect()->route('patients.show', ['patient' => $patient->id])
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
            'visits.staff',
            'visits.doctor',
            'visits.medicalRecord',
            'visits.medicalOrders.orderItems',
            'visits.billings',
            'patientFiles' => function ($query) {
                $query->with('file')->orderByDesc('created_at');
            },
            'medicalOrders.staff',
            'medicalOrders.orderItems',
            'staff',
        ]);

        // Get medical records from appointments and visits
        $medicalRecords = collect();
        $medicalRecords = $medicalRecords->merge($patient->appointments->pluck('medicalRecord')->filter());
        $medicalRecords = $medicalRecords->merge($patient->visits->pluck('medicalRecord')->filter());
        $uniqueRecords = $medicalRecords->unique('id');

        // Get visit history with complete details
        $visitHistory = $patient->visits->map(function ($visit) {
            // Get medical orders for this visit
            $medicalOrders = $visit->medicalOrders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_details' => $order->order_details,
                    'status' => $order->status,
                    'priority' => $order->priority,
                    'ordered_at' => $order->ordered_at,
                    'items' => $order->orderItems->map(function ($item) {
                        return [
                            'item_type' => $item->item_type,
                            'item_name' => $item->item_name,
                            'quantity' => $item->quantity_required,
                            'unit_price' => $item->unit_price,
                            'selling_price' => $item->selling_price,
                            'status' => $item->status,
                            'details' => $item->details,
                            'notes' => $item->notes,
                        ];
                    }),
                ];
            });

            // Get billings for this visit
            $billings = $visit->billings->map(function ($billing) {
                return [
                    'id' => $billing->id,
                    'amount' => $billing->amount,
                    'status' => $billing->status,
                    'billing_date' => $billing->billing_date,
                ];
            });

            return [
                'id' => $visit->id,
                'visit_date_time' => $visit->visit_date_time,
                'status' => $visit->status,
                'notes' => $visit->notes,
                'staff_name' => $visit->staff?->user?->name,
                'medical_orders' => $medicalOrders,
                'billings' => $billings,
            ];
        })->sortByDesc('visit_date_time')->values();

        // Get medical orders data with staff information and order items
        $medicalOrdersData = $patient->medicalOrders->sortByDesc('ordered_at')->map(function ($order) {
            // Group order items by type
            $services = $order->orderItems->where('item_type', 'procedure')->values();
            $labs = $order->orderItems->whereIn('item_type', ['lab'])->values();
            $medications = $order->orderItems->where('item_type', 'rx_medicine')->values();

            return [
                'id' => $order->id,
                'order_details' => $order->order_details,
                'status' => $order->status,
                'priority' => $order->priority,
                'ordered_at' => $order->ordered_at,
                'completed_at' => $order->completed_at,
                'staff_name' => $order->staff?->name,
                'notes' => $order->notes,
                'services' => $services->map(fn ($item) => [
                    'name' => $item->item_name,
                    'details' => $item->details,
                    'status' => $item->status,
                    'completed_at' => $item->completed_at,
                ]),
                'labs' => $labs->map(fn ($item) => [
                    'name' => $item->item_name,
                    'details' => $item->details,
                    'status' => $item->status,
                    'completed_at' => $item->completed_at,
                    'result_value' => $item->result_value,
                    'result_unit' => $item->result_unit,
                    'result_notes' => $item->result_notes,
                ]),
                'medications' => $medications->map(fn ($item) => [
                    'name' => $item->item_name,
                    'dosage' => $item->dosage,
                    'frequency' => $item->frequency,
                    'route' => $item->route,
                    'quantity' => $item->quantity_required,
                    'details' => $item->details,
                    'status' => $item->status,
                ]),
            ];
        });

        return Inertia::render('Patients/Show', [
            'patient' => array_merge($patient->toArray(), [
                'medical_records' => $uniqueRecords->toArray(),
                'medical_orders_data' => $medicalOrdersData->toArray(),
                'visit_history' => $visitHistory->toArray(),
            ]),
        ]);
    }

    /**
     * Stream the patient's profile photo inline.
     */
    public function photo()
    {
        $patient = Patient::findOrFail(request('patient'));
        $this->authorize('view', $patient);

        if (! $patient->photo_path || ! Storage::exists($patient->photo_path)) {
            abort(404, 'Photo not found');
        }

        return Storage::response($patient->photo_path, null, [
            'Content-Disposition' => 'inline',
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
        $removePhoto = $request->boolean('remove_photo');
        unset($data['photo'], $data['remove_photo']);

        if ($request->hasFile('photo')) {
            if ($patient->photo_path) {
                Storage::delete($patient->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('patient_photos');
        } elseif ($removePhoto && $patient->photo_path) {
            Storage::delete($patient->photo_path);
            $data['photo_path'] = null;
        }

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
            // model). Role names are case-sensitive: the seeded role is 'patient'.
            $user->assignRole('patient');

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
     * Download patient report as PDF.
     */
    public function downloadReport(): \Illuminate\Http\Response
    {
        $patient = Patient::findOrFail(request('patient'));
        $this->authorize('view', $patient);

        $patient->load(['user', 'appointments.staff', 'appointments.medicalRecord', 'patientFiles.file', 'medicalOrders.staff']);

        // Build patient_info array expected by patient-report blade.
        // Build date of birth string only when components are present
        $dobYear = (int) ($patient->date_of_birth_year ?? 0);
        $dobMonth = (int) ($patient->date_of_birth_month ?? 0);
        $dobDay = (int) ($patient->date_of_birth_day ?? 0);
        $dateOfBirthStr = null;
        if ($dobYear > 0 && $dobMonth > 0 && $dobDay > 0) {
            $dateOfBirthStr = sprintf('%04d-%02d-%02d', $dobYear, $dobMonth, $dobDay);
        }

        $patientInfo = [
            'id' => $patient->id,
            'title' => $patient->title,
            'first_name' => $patient->name,
            'last_name' => $patient->surname,
            // Full name, used by blade title and other places
            'name' => trim(($patient->title ? $patient->title.' ' : '').($patient->name ?? '').' '.($patient->surname ?? '')),
            'native_name' => trim(($patient->khmer_china_name ?? '').' '.($patient->khmer_china_surname ?? '')) ?: null,
            'date_of_birth' => $dateOfBirthStr,
            'gender' => $patient->gender,
            'email' => $patient->email ?? $patient->user?->email,
            'mobile_phone' => $patient->mobile_phone,
            'home_phone' => $patient->home_phone,
            'address' => $patient->address,
            'city' => $patient->sub_district,
            'province' => $patient->province,
            'postal_code' => $patient->zip_code,
            'country' => $patient->nationality,
            'occupation' => $patient->occupation,
            'employer' => $patient->company_name,
            'emergency_contact_name' => $patient->emergency_contact_name,
            'emergency_contact_relationship' => $patient->emergency_contact_relationship,
        ];
        // Additional convenience fields used in templates
        $patientInfo['name'] = $patientInfo['name'] ?? trim(($patientInfo['title'] ? $patientInfo['title'].' ' : '').$patientInfo['first_name'].' '.$patientInfo['last_name']);
        $patientInfo['phone_number'] = $patientInfo['mobile_phone'] ?? $patientInfo['home_phone'] ?? null;
        $patientInfo['insurance_provider'] = $patient->insurance_name ?? null;
        $patientInfo['insurance_policy_number'] = $patient->contract_name ?? null;
        $patientInfo['insurance_plan_name'] = null;
        $patientInfo['insurance_info'] = $patient->insurance_name ?? $patient->contract_name ?? null;

        // Files
        $patientFiles = $patient->patientFiles->map(function ($pf) {
            return [
                'file_type' => $pf->type ?? null,
                'filename' => $pf->file?->name ?? null,
                'uploaded_at' => $pf->file?->created_at ?? null,
                'notes' => null,
            ];
        })->toArray();

        // Medical records
        $medicalRecords = collect();
        $medicalRecords = $medicalRecords->merge($patient->appointments->pluck('medicalRecord')->filter());
        $medicalRecords = $medicalRecords->merge($patient->appointments->pluck('visits')->flatten()->pluck('medicalRecord')->filter());
        $uniqueRecords = $medicalRecords->unique('id')->map(function ($record) {
            return [
                'id' => $record->id,
                'diagnosis' => $record->diagnosis,
                'treatment' => $record->treatment,
                'notes' => $record->notes,
                'date_of_service' => $record->date_of_service,
                'created_at' => $record->created_at,
            ];
        })->toArray();

        // Medical orders
        $medicalOrders = $patient->medicalOrders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_type' => $order->type ?? $order->order_type ?? null,
                'order_details' => $order->order_details,
                'status' => $order->status,
                'priority' => $order->priority,
                'ordered_at' => $order->ordered_at,
                'completed_at' => $order->completed_at,
                'staff_name' => $order->staff?->name,
                'notes' => $order->notes,
            ];
        })->toArray();

        // Appointments
        $appointments = $patient->appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'appointment_date_time' => $appointment->appointment_date_time?->setTimezone('Asia/Phnom_Penh')->format('d/m/y H:i'),
                'staff_name' => $appointment->staff?->user?->name ?? $appointment->staff?->name ?? 'N/A',
                'reason_for_visit' => $appointment->reason_for_visit ?? null,
                'status' => $appointment->status ?? null,
            ];
        })->toArray();

        // Visits from appointments
        $visits = $patient->appointments->pluck('visits')->flatten()->map(function ($visit) {
            return [
                'id' => $visit->id,
                'visit_date_time' => $visit->visit_date_time?->toDateTimeString(),
                'staff_name' => $visit->staff?->user?->name ?? $visit->staff?->name ?? 'N/A',
                'status' => $visit->status ?? null,
                'notes' => $visit->notes ?? null,
            ];
        })->toArray();

        $report = [
            'patient_info' => $patientInfo,
            'appointments' => $appointments,
            'visits' => $visits,
            'medical_records' => $uniqueRecords,
            'medical_orders' => $medicalOrders,
            'patient_files' => $patientFiles,
        ];

        ini_set('memory_limit', '512M');
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('patient-report', compact('report', 'patient'));
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isPhpEnabled' => true,
        ], true);

        $filename = 'patient-report-'.str_replace('/', '-', $patient->id).'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
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

    /**
     * Generate a single patient label sized for small label printers (e.g. 4" x 2").
     */
    public function generateLabel(): \Illuminate\Http\Response
    {
        try {
            $patient = Patient::findOrFail(request('patient'));
            $this->authorize('view', $patient);
            // Build full name safely, filtering out empty pieces and casting values to string
            $nameParts = array_filter([
                (string) ($patient->title ?? ''),
                (string) ($patient->name ?? ''),
                (string) ($patient->surname ?? ''),
            ]);
            $fullName = trim(implode(' ', $nameParts));

            $dob = sprintf('%02d/%02d/%04d', $patient->date_of_birth_day, $patient->date_of_birth_month, $patient->date_of_birth_year);
            $gender = strtoupper(substr($patient->gender, 0, 1)); // M or F

            $age = null;
            if (! empty($patient->date_of_birth_year) && ! empty($patient->date_of_birth_month) && ! empty($patient->date_of_birth_day)) {
                $birthDate = \Carbon\Carbon::createFromDate(
                    (int) $patient->date_of_birth_year,
                    (int) $patient->date_of_birth_month,
                    (int) $patient->date_of_birth_day
                );

                $now = \Carbon\Carbon::now();
                $interval = $birthDate->diff($now); // DateInterval

                // Format as YY.MM.DD (years.months.days), padded to 2 digits for months/days
                $age = sprintf(
                    '%02d.%02d.%02d',
                    $interval->y,
                    $interval->m,
                    $interval->d
                );
            }

            // Single label markup (fills the cell, centered)
            $stickerHtml = '
                <div style="
                    box-sizing: border-box;
                    width: 57mm; height: 30mm;
                    border: none;
                    display: flex; flex-direction: column;
                    align-items: center; justify-content: center;
                    text-align: center; font-size: 12pt;
                    line-height: 1.6;       /* added vertical spacing */
                    letter-spacing: 0.5px;  /* added character spacing */">
                    <div style="margin-bottom: 4px;"> '.htmlspecialchars((string) ($fullName ?? ''), ENT_QUOTES, 'UTF-8').'</div>
                    <div style="margin-bottom: 4px;">HN: '.htmlspecialchars((string) $patient->id, ENT_QUOTES, 'UTF-8').'</div>
                    <div style="font-size: 10pt;">DOB: '.htmlspecialchars($dob, ENT_QUOTES, 'UTF-8').' ('.htmlspecialchars($age, ENT_QUOTES, 'UTF-8').')</div>
                </div>
            ';

            // Build 9 rows × 3 columns = 27 labels
            $rows = '';
            for ($r = 0; $r < 9; $r++) {
                $rows .= '<tr>';
                for ($c = 0; $c < 3; $c++) {
                    $rows .= '<td>'.$stickerHtml.'</td>';
                }
                $rows .= '</tr>';
            }

            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    @page { size: A4; margin: 0; }
                    html, body {
                        width: 210mm; height: 297mm;
                        margin: 0; padding: 0; background: #fff;
                        font-family: Arial, sans-serif;
                    }
                    .grid {
                        position: absolute;
                        top: 15mm;
                        left: 15mm;
                        width: 181.5mm;
                        height: 270mm;
                    }
                    table {
                        width: 100%;
                        height: 100%;
                        border-collapse: separate;
                        border-spacing: 0;
                        table-layout: fixed;
                    }
                    tr { height: 30mm; }
                    td {
                        width: 57mm; height: 30mm;
                        padding: 0 0;
                        vertical-align: middle;
                        text-align: center;
                    }
                    .label {
                        width: 100%;
                        height: 100%;
                        box-sizing: border-box;
                        border: none;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        font-size: 12pt;
                        line-height: 1.6;       /* added vertical spacing */
                        letter-spacing: 0.5px;  /* added character spacing */
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class="grid">
                    <table>'.$rows.'</table>
                </div>
            </body>
            </html>
            ';

            $dompdf = new Dompdf;
            $dompdf->loadHtml($html);
            $dompdf->setPaper('a4');
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
