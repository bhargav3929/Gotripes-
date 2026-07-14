<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\SaudiVisaType;
use Illuminate\Http\Request;

class ManagerSaudiVisaController extends Controller
{
    public function index()
    {
        $visaTypes = SaudiVisaType::orderBy('price', 'asc')->get();
        return view('manager.saudi-visas.index', compact('visaTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'price'              => 'required|numeric|min:0',
            'description'        => 'nullable|string|max:1000',
            'required_documents' => 'nullable|string',
            'processing_days'    => 'nullable|integer|min:1|max:90',
        ]);

        SaudiVisaType::create([
            'company_id'          => current_company()?->id,
            'name'                => $validated['name'],
            'price'               => $validated['price'],
            'description'         => $validated['description'] ?? null,
            'required_documents'  => $this->parseDocuments($request->required_documents),
            'processing_days'     => $validated['processing_days'] ?? 3,
            'isActive'            => true,
        ]);

        return redirect()->route('manager.saudi-visas.index')
            ->with('success', 'Saudi Visa Type added successfully.');
    }

    public function update(Request $request, $id)
    {
        $visaType = SaudiVisaType::findOrFail($id);

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'price'              => 'required|numeric|min:0',
            'isActive'           => 'required|boolean',
            'description'        => 'nullable|string|max:1000',
            'required_documents' => 'nullable|string',
            'processing_days'    => 'nullable|integer|min:1|max:90',
        ]);

        $visaType->update([
            'name'               => $validated['name'],
            'price'              => $validated['price'],
            'isActive'           => $validated['isActive'],
            'description'        => $validated['description'] ?? null,
            'required_documents' => $this->parseDocuments($request->required_documents),
            'processing_days'    => $validated['processing_days'] ?? 3,
        ]);

        return redirect()->route('manager.saudi-visas.index')
            ->with('success', 'Saudi Visa Type updated successfully.');
    }

    public function destroy($id)
    {
        $visaType = SaudiVisaType::findOrFail($id);
        $visaType->update(['isActive' => false]);

        return redirect()->route('manager.saudi-visas.index')
            ->with('success', 'Saudi Visa Type deactivated.');
    }

    private function parseDocuments(?string $raw): array
    {
        if (empty($raw)) return [];
        return array_values(array_filter(
            array_map('trim', explode("\n", $raw)),
            fn($l) => $l !== ''
        ));
    }
}
