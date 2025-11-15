<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = File::all();

        return Inertia::render('Files/Index', [
            'files' => $files,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Files/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', File::class);

        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        // Files are stored locally for confidentiality
        $path = $file->store('files');

        File::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return redirect()->route('files.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        $this->authorize('view', $file);

        return response()->download(storage_path('app/'.$file->path));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(File $file)
    {
        return Inertia::render('Files/Edit', [
            'file' => $file,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, File $file)
    {
        $this->authorize('update', $file);

        // Perhaps update name or something, but for now, maybe not needed.
        // Or re-upload.

        $request->validate([
            'file' => 'sometimes|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            Storage::delete($file->path);
            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('files');
            $file->update([
                'name' => $uploadedFile->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $uploadedFile->getMimeType(),
                'size' => $uploadedFile->getSize(),
            ]);
        }

        return redirect()->route('files.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        $this->authorize('delete', $file);

        Storage::delete($file->path);
        $file->delete();

        return redirect()->route('files.index');
    }
}
