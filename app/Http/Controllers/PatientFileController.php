<?php

namespace App\Http\Controllers;

use App\Enums\PatientFileTypeEnum;
use App\Models\File;
use App\Models\Patient;
use App\Models\PatientFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PatientFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $patientId = $request->query('patient_id');

        $query = PatientFile::with('file', 'patient');

        if ($patientId) {
            $query->where('patient_id', $patientId);
        }

        $patientFiles = $query->get();

        return Inertia::render('Files/IndexPatientFile', [
            'items' => $patientFiles,
            'title' => 'Patient Files',
            'createRoute' => route('patient-files.create'),
            'showRoute' => route('patient-files.show', ':id'),
            'editRoute' => route('patient-files.edit', ':id'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Files/Create', [
            'title' => 'Patient Files',
            'indexRoute' => route('patient-files.index'),
            'patients' => Patient::all(),
            'typeOptions' => PatientFileTypeEnum::options(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', File::class);

        $request->validate([
            'file' => 'required|file|max:10240',
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|string|in:'.implode(',', array_map(fn ($case) => $case->value, PatientFileTypeEnum::cases())),
        ]);

        $file = $request->file('file');
        // Files are stored locally for confidentiality
        $path = $file->store('patient_files');

        $fileModel = File::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        PatientFile::create([
            'patient_id' => $request->patient_id,
            'file_id' => $fileModel->id,
            'type' => $request->type,
        ]);

        return redirect()->route('patient-files.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(PatientFile $patientFile)
    {
        $this->authorize('view', $patientFile->file);

        return response()->download(storage_path('app/'.$patientFile->file->path));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PatientFile $patientFile)
    {
        return Inertia::render('Files/Edit', [
            'title' => 'Patient Files',
            'indexRoute' => route('patient-files.index'),
            'item' => $patientFile->load('file', 'patient'),
            'patients' => Patient::all(),
            'typeOptions' => PatientFileTypeEnum::options(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PatientFile $patientFile)
    {
        $this->authorize('update', $patientFile->file);

        $request->validate([
            'file' => 'sometimes|file|max:10240',
            'patient_id' => 'sometimes|exists:patients,id',
            'type' => 'sometimes|string|in:'.implode(',', array_map(fn ($case) => $case->value, PatientFileTypeEnum::cases())),
        ]);

        if ($request->hasFile('file')) {
            Storage::delete($patientFile->file->path);
            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('patient_files');
            $patientFile->file->update([
                'name' => $uploadedFile->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $uploadedFile->getMimeType(),
                'size' => $uploadedFile->getSize(),
            ]);
        }

        if ($request->has('patient_id')) {
            $patientFile->update([
                'patient_id' => $request->patient_id,
            ]);
        }

        if ($request->has('type')) {
            $patientFile->update([
                'type' => $request->type,
            ]);
        }

        return redirect()->route('patient-files.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PatientFile $patientFile)
    {
        $this->authorize('delete', $patientFile->file);

        Storage::delete($patientFile->file->path);
        $patientFile->file->delete();
        $patientFile->delete();

        return redirect()->route('patient-files.index');
    }
}
