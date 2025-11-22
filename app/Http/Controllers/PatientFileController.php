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
        $originalName = $file->getClientOriginalName();
        $uniqueName = time().'_'.$originalName;
        $path = $file->storeAs('patient_files', $uniqueName);

        $fileModel = File::create([
            'name' => $originalName,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        PatientFile::create([
            'patient_id' => $request->patient_id,
            'file_id' => $fileModel->id,
            'type' => $request->type,
        ]);

        return redirect()->back();
    }

    /**
     * Download the specified resource.
     */
    public function download(PatientFile $patientFile)
    {
        $this->authorize('view', $patientFile->file);

        if (! Storage::exists($patientFile->file->path)) {
            abort(404, 'File not found');
        }

        if (request()->has('inline')) {
            return Storage::response($patientFile->file->path, $patientFile->file->name, [
                'Content-Disposition' => 'inline; filename="'.$patientFile->file->name.'"',
            ]);
        }

        return Storage::download($patientFile->file->path, $patientFile->file->name);
    }

    /**
     * Display the specified resource.
     */
    public function show(PatientFile $patientFile)
    {
        $this->authorize('view', $patientFile->file);

        return Inertia::render('Files/Show', [
            'title' => 'Patient Files',
            'indexRoute' => route('patient-files.index'),
            'item' => $patientFile->load('file', 'patient'),
            'isPatient' => true,
            'fileExists' => Storage::exists($patientFile->file->path),
        ]);
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
            'fileExists' => Storage::exists($patientFile->file->path),
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
            $originalName = $uploadedFile->getClientOriginalName();
            $uniqueName = time().'_'.$originalName;
            $path = $uploadedFile->storeAs('patient_files', $uniqueName);
            $patientFile->file->update([
                'name' => $originalName,
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

        return redirect()->back();
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

        return redirect()->back();
    }
}
