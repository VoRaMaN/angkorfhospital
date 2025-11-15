<?php

namespace App\Http\Controllers;

use App\Enums\StaffFileTypeEnum;
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

        return Inertia::render('Files/IndexStaffFiles', [
            'items' => $staffFiles,
            'title' => 'Staff Files',
            'createRoute' => route('staff-files.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user()->load('staff');

        return Inertia::render('Files/Create', [
            'title' => 'Staff Files',
            'indexRoute' => route('staff-files.index'),
            'staff' => Staff::all(),
            'typeOptions' => StaffFileTypeEnum::options(),
            'currentStaff' => $user->staff,
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
            'type' => 'required|string|in:'.implode(',', array_map(fn ($case) => $case->value, StaffFileTypeEnum::cases())),
        ]);

        $file = $request->file('file');
        // Files are stored locally for confidentiality
        $originalName = $file->getClientOriginalName();
        $uniqueName = time().'_'.$originalName;
        $path = $file->storeAs('staff_files', $uniqueName);

        $fileModel = File::create([
            'name' => $originalName,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        StaffFile::create([
            'staff_id' => $request->staff_id,
            'file_id' => $fileModel->id,
            'type' => $request->type,
        ]);

        return redirect()->route('staff-files.index');
    }

    /**
     * Download the specified resource.
     */
    public function download(StaffFile $staffFile)
    {
        $this->authorize('view', $staffFile->file);

        if (! Storage::exists($staffFile->file->path)) {
            abort(404, 'File not found');
        }

        if (request()->has('inline')) {
            return Storage::response($staffFile->file->path, $staffFile->file->name, [
                'Content-Disposition' => 'inline; filename="'.$staffFile->file->name.'"',
            ]);
        }

        return Storage::download($staffFile->file->path, $staffFile->file->name);
    }

    /**
     * Display the specified resource.
     */
    public function show(StaffFile $staffFile)
    {
        $this->authorize('view', $staffFile->file);

        return Inertia::render('Files/Show', [
            'title' => 'Staff Files',
            'indexRoute' => route('staff-files.index'),
            'item' => $staffFile->load('file', 'staff'),
            'isPatient' => false,
            'fileExists' => Storage::exists($staffFile->file->path),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaffFile $staffFile)
    {
        $user = auth()->user()->load('staff');

        return Inertia::render('Files/Edit', [
            'title' => 'Staff Files',
            'indexRoute' => route('staff-files.index'),
            'item' => $staffFile->load('file', 'staff'),
            'staff' => Staff::all(),
            'typeOptions' => StaffFileTypeEnum::options(),
            'fileExists' => Storage::exists($staffFile->file->path),
            'currentStaff' => $user->staff,
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
            'type' => 'sometimes|string|in:'.implode(',', array_map(fn ($case) => $case->value, StaffFileTypeEnum::cases())),
        ]);

        if ($request->hasFile('file')) {
            Storage::delete($staffFile->file->path);
            $uploadedFile = $request->file('file');
            $originalName = $uploadedFile->getClientOriginalName();
            $uniqueName = time().'_'.$originalName;
            $path = $uploadedFile->storeAs('staff_files', $uniqueName);
            $staffFile->file->update([
                'name' => $originalName,
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

        if ($request->has('type')) {
            $staffFile->update([
                'type' => $request->type,
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
