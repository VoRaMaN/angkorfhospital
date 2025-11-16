<?php

namespace App\Http\Controllers;

use App\Models\MedicalService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicalServiceController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', MedicalService::class);

        $medicalServices = MedicalService::all();

        return Inertia::render('MedicalOrders/MedicalService', [
            'medicalServices' => $medicalServices,
        ]);
    }

    public function create()
    {
        $this->authorize('create', MedicalService::class);

        return Inertia::render('MedicalOrders/MedicalService/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', MedicalService::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:procedure,imaging,consultation,therapy',
            'price' => 'required|numeric|min:0',
        ]);

        $medicalService = MedicalService::create($validated);

        if ($request->expectsJson()) {
            return response()->json($medicalService);
        }

        return redirect()->route('medical-services.index')->with('success', 'Medical service created successfully.');
    }

    public function show(MedicalService $medicalService)
    {
        $this->authorize('view', $medicalService);

        return Inertia::render('MedicalOrders/MedicalService/Show', [
            'medicalService' => $medicalService,
        ]);
    }

    public function edit(MedicalService $medicalService)
    {
        $this->authorize('update', $medicalService);

        return Inertia::render('MedicalOrders/MedicalService/Edit', [
            'medicalService' => $medicalService,
        ]);
    }

    public function update(Request $request, MedicalService $medicalService)
    {
        $this->authorize('update', $medicalService);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:procedure,imaging,consultation,therapy',
            'price' => 'required|numeric|min:0',
        ]);

        $medicalService->update($validated);

        if ($request->expectsJson()) {
            return response()->json($medicalService);
        }

        return redirect()->route('medical-services.index')->with('success', 'Medical service updated successfully.');
    }

    public function destroy(MedicalService $medicalService)
    {
        $this->authorize('delete', $medicalService);

        $medicalService->delete();

        return redirect()->route('medical-services.index')->with('success', 'Medical service deleted successfully.');
    }
}
