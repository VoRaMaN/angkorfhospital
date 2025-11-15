<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Staff;
use App\Models\StaffFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StaffFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $staffId = $request->query('staff_id');

        $query = StaffFile::with('file', 'staff');

        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        $staffFiles = $query->get();

        return Inertia::render('StaffFiles/Index', [
            'staffFiles' => $staffFiles,
            'staff' => Staff::all(), // for selection
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('StaffFiles/Create', [
            'staff' => Staff::all(),
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
            'staff_id' => 'required|exists:staff,id',
        ]);

        $file = $request->file('file');
        // Files are stored locally for confidentiality
        $path = $file->store('staff_files');

        $fileModel = File::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        StaffFile::create([
            'staff_id' => $request->staff_id,
            'file_id' => $fileModel->id,
        ]);

        return redirect()->route('staff-files.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(StaffFile $staffFile)
    {
        $this->authorize('view', $staffFile->file);

        return response()->download(storage_path('app/'.$staffFile->file->path));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaffFile $staffFile)
    {
        return Inertia::render('StaffFiles/Edit', [
            'staffFile' => $staffFile->load('file', 'staff'),
            'staff' => Staff::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StaffFile $staffFile)
    {
        $this->authorize('update', $staffFile->file);

        $request->validate([
            'file' => 'sometimes|file|max:10240',
            'staff_id' => 'sometimes|exists:staff,id',
        ]);

        if ($request->hasFile('file')) {
            Storage::delete($staffFile->file->path);
            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('staff_files');
            $staffFile->file->update([
                'name' => $uploadedFile->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $uploadedFile->getMimeType(),
                'size' => $uploadedFile->getSize(),
            ]);
        }

        if ($request->has('staff_id')) {
            $staffFile->update([
                'staff_id' => $request->staff_id,
            ]);
        }

        return redirect()->route('staff-files.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaffFile $staffFile)
    {
        $this->authorize('delete', $staffFile->file);

        Storage::delete($staffFile->file->path);
        $staffFile->file->delete();
        $staffFile->delete();

        return redirect()->route('staff-files.index');
    }
}
