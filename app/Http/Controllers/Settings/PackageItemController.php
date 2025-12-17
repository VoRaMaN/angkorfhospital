<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackageItemRequest;
use App\Models\PackageItem;
use Illuminate\Http\RedirectResponse;

class PackageItemController extends Controller
{
    public function store(StorePackageItemRequest $request): RedirectResponse
    {
        PackageItem::create($request->validated());

        return back()->with('success', 'Package item created');
    }

    public function update(StorePackageItemRequest $request, PackageItem $packageItem): RedirectResponse
    {
        $packageItem->update($request->validated());

        return back()->with('success', 'Package item updated');
    }

    public function destroy(PackageItem $packageItem): RedirectResponse
    {
        $packageItem->delete();

        return back()->with('success', 'Package item deleted');
    }
}
