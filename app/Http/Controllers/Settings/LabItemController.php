<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabItemRequest;
use App\Models\LabItem;
use Illuminate\Http\RedirectResponse;

class LabItemController extends Controller
{
    public function store(StoreLabItemRequest $request): RedirectResponse
    {
        LabItem::create($request->validated());

        return back()->with('success', 'Lab item created');
    }

    public function update(StoreLabItemRequest $request, LabItem $labItem): RedirectResponse
    {
        $labItem->update($request->validated());

        return back()->with('success', 'Lab item updated');
    }

    public function destroy(LabItem $labItem): RedirectResponse
    {
        $labItem->delete();

        return back()->with('success', 'Lab item deleted');
    }
}
