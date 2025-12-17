<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicineRequest;
use App\Models\Medicine;
use Illuminate\Http\RedirectResponse;

class MedicineController extends Controller
{
    public function store(StoreMedicineRequest $request): RedirectResponse
    {
        Medicine::create($request->validated());

        return back()->with('success', 'Medicine created');
    }

    public function update(StoreMedicineRequest $request, Medicine $medicine): RedirectResponse
    {
        $medicine->update($request->validated());

        return back()->with('success', 'Medicine updated');
    }

    public function destroy(Medicine $medicine): RedirectResponse
    {
        $medicine->delete();

        return back()->with('success', 'Medicine deleted');
    }
}
