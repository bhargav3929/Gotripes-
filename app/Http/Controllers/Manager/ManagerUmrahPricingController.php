<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\UmrahPackage;
use Illuminate\Http\Request;

class ManagerUmrahPricingController extends Controller
{
    public function index()
    {
        $packages = UmrahPackage::orderBy('createdDate', 'desc')->get();
        return view('manager.umrah.pricing.index', compact('packages'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'prices' => 'required|array',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.discount_price' => 'nullable|numeric|min:0',
            'prices.*.adult_price' => 'nullable|numeric|min:0',
            'prices.*.child_price' => 'nullable|numeric|min:0',
            'prices.*.infant_price' => 'nullable|numeric|min:0',
            'prices.*.currency' => 'required|string|max:10',
        ]);

        foreach ($request->prices as $id => $data) {
            $package = UmrahPackage::find($id);
            if ($package) {
                $package->update([
                    'price' => $data['price'],
                    'discount_price' => $data['discount_price'] ?? null,
                    'adult_price' => $data['adult_price'] ?? null,
                    'child_price' => $data['child_price'] ?? null,
                    'infant_price' => $data['infant_price'] ?? null,
                    'currency' => $data['currency'] ?? 'AED',
                ]);
            }
        }

        return redirect()->route('manager.umrah.pricing.index')->with('success', 'Pricing updated successfully.');
    }
}
