<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaudiVisaType;
use Illuminate\Http\Request;

class SaudiVisaAdminController extends Controller
{
    public function index()
    {
        $visaTypes = SaudiVisaType::orderBy('price', 'asc')->get();
        return view('admin.saudi-visas.index', compact('visaTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        SaudiVisaType::create([
            'company_id' => current_company()?->id,
            'name'       => $validated['name'],
            'price'      => $validated['price'],
            'isActive'   => true,
        ]);

        return redirect()->route('admin.saudi-visas.index')
            ->with('success', 'Saudi Visa Type added successfully.');
    }

    public function update(Request $request, $id)
    {
        $visaType = SaudiVisaType::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'price'    => 'required|numeric|min:0',
            'isActive' => 'required|boolean',
        ]);

        $visaType->update([
            'name'     => $validated['name'],
            'price'    => $validated['price'],
            'isActive' => $validated['isActive'],
        ]);

        return redirect()->route('admin.saudi-visas.index')
            ->with('success', 'Saudi Visa Type updated successfully.');
    }

    public function destroy($id)
    {
        $visaType = SaudiVisaType::findOrFail($id);
        $visaType->update(['isActive' => false]);

        return redirect()->route('admin.saudi-visas.index')
            ->with('success', 'Saudi Visa Type deactivated.');
    }
}
