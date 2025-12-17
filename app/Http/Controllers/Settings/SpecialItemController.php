<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpecialItemRequest;
use App\Models\SpecialItem;
use Illuminate\Http\RedirectResponse;

class SpecialItemController extends Controller
{
    public function store(StoreSpecialItemRequest $request): RedirectResponse
    {
        SpecialItem::create($request->validated());

        return back()->with('success', 'Special item created');
    }

    public function update(StoreSpecialItemRequest $request, SpecialItem $specialItem): RedirectResponse
    {
        $specialItem->update($request->validated());

        return back()->with('success', 'Special item updated');
    }

    public function destroy(SpecialItem $specialItem): RedirectResponse
    {
        $specialItem->delete();

        return back()->with('success', 'Special item deleted');
    }
}
