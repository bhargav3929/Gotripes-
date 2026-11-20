<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\SupplierInvite;
use App\Models\SupplierSubmission;
use App\Models\TourPackageAddon;
use App\Models\TourPackageHotel;
use App\Models\TourPackageLandRate;
use App\Models\TravelPackage;
use App\Services\TourPackages\SupplierRateImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Supplier forms: staff create access codes for suppliers, read what they
 * send, download their photos, and apply their rates to a tour package.
 */
class ManagerSupplierFormsController extends Controller
{
    private function wizardPackages()
    {
        return TravelPackage::where('isActive', 1)->where('package_type', 'wizard')->orderBy('title')->get(['id', 'title', 'country']);
    }

    public function index(): View
    {
        return view('manager.supplier-forms.index', [
            'invites'     => SupplierInvite::with('package:id,title')->withCount('submissions')->latest('id')->get(),
            'submissions' => SupplierSubmission::with('invite:id,supplier_name', 'appliedPackage:id,title')->latest('id')->limit(100)->get(),
            'packages'    => $this->wizardPackages(),
        ]);
    }

    public function storeInvite(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_name' => 'required|string|max:190',
            'country'       => 'nullable|string|max:100',
            'package_id'    => 'nullable|integer',
            'expires_on'    => 'nullable|date|after:today',
        ], [], ['supplier_name' => 'supplier name']);
        if (!empty($data['package_id']) && !$this->wizardPackages()->contains('id', (int) $data['package_id'])) {
            throw ValidationException::withMessages(['package_id' => 'Pick one of your booking-wizard packages.']);
        }
        $invite = SupplierInvite::create($data + ['code' => SupplierInvite::newCode(), 'created_by' => auth()->user()?->name]);
        return back()->with('success', 'Code ' . $invite->display_code . ' created for ' . $invite->supplier_name . '. Copy the message below and send it to them.')
            ->with('new_invite', $invite->id);
    }

    /** Turn a code off (the supplier can no longer open the form) or back on. */
    public function toggleInvite(int $id): RedirectResponse
    {
        $invite = SupplierInvite::findOrFail($id);
        $invite->update(['revoked_at' => $invite->revoked_at ? null : now()]);
        return back()->with('success', 'Code ' . $invite->display_code . ($invite->revoked_at ? ' turned off.' : ' turned back on.'));
    }

    public function show(int $id): View
    {
        $submission = SupplierSubmission::with('invite.package', 'appliedPackage')->findOrFail($id);
        $previous = SupplierSubmission::where('supplier_invite_id', $submission->supplier_invite_id)->where('id', '<', $submission->id)->latest('id')->first();
        $currency = $submission->get('currency', 'AED');

        return view('manager.supplier-forms.show', [
            'submission' => $submission,
            'previous'   => $previous,
            'packages'   => $this->wizardPackages(),
            'target'     => $submission->applied_package_id ?? $submission->invite?->package_id,
            'currency'   => $currency,
            'rate'       => SupplierSubmission::AED_RATES[$currency] ?? 1,
            'sections'   => SupplierRateImporter::SECTIONS,
            'available'  => SupplierRateImporter::available($submission),
            'mealPlans'  => TourPackageHotel::MEAL_PLANS,
            'groups'     => TourPackageLandRate::GROUPS,
            'categories' => SupplierSubmission::EXTRA_CATEGORIES,
            'bases'      => TourPackageAddon::BASES,
        ]);
    }

    public function photo(int $id, int $index): BinaryFileResponse
    {
        $photo = $this->photoFile($id, $index);
        return request()->boolean('download')
            ? response()->download(storage_path('app/' . $photo['path']), $photo['name'] ?: basename($photo['path']))
            : response()->file(storage_path('app/' . $photo['path']));
    }

    public function photosZip(int $id): BinaryFileResponse
    {
        $submission = SupplierSubmission::with('invite')->findOrFail($id);
        $zipPath = tempnam(sys_get_temp_dir(), 'sup') . '.zip';
        $zip = new \ZipArchive();
        abort_unless($zip->open($zipPath, \ZipArchive::CREATE) === true, 500);
        $names = [];
        foreach ((array) $submission->photos as $i => $p) {
            if (is_file(storage_path('app/' . $p['path']))) {
                $name = ($i + 1) . '-' . preg_replace('/[^A-Za-z0-9._-]+/', '-', $p['name'] ?: basename($p['path']));
                $zip->addFile(storage_path('app/' . $p['path']), $names[] = $name);
            }
        }
        $zip->close();
        abort_if(!$names, 404);
        $label = \Illuminate\Support\Str::slug(($submission->invite?->supplier_name ?? 'supplier') . ' ' . $submission->get('package.name'));
        return response()->download($zipPath, $label . '-photos.zip')->deleteFileAfterSend();
    }

    public function apply(Request $request, int $id): RedirectResponse
    {
        $submission = SupplierSubmission::findOrFail($id);
        $available = array_keys(SupplierRateImporter::available($submission));
        $request->validate([
            'target'     => 'required',
            'rate'       => 'required|numeric|gt:0|max:1000',
            'sections'   => 'required|array|min:1',
            'sections.*' => 'in:' . implode(',', $available ?: ['none']),
        ], ['sections.required' => 'Tick at least one part to fill in.', 'rate.gt' => 'Enter the exchange rate to AED.']);

        if ($request->input('target') === 'new') {
            $package = SupplierRateImporter::createPackage($submission, auth()->user()?->name);
        } else {
            $package = TravelPackage::where('isActive', 1)->where('package_type', 'wizard')->find((int) $request->input('target'));
            if (!$package) {
                throw ValidationException::withMessages(['target' => 'Pick the package these rates are for.']);
            }
        }

        $done = SupplierRateImporter::apply($submission, $package, (float) $request->input('rate'), (array) $request->input('sections'));
        $submission->update(['status' => 'applied', 'applied_package_id' => $package->id, 'applied_at' => now(), 'applied_by' => auth()->user()?->name]);
        if (!$submission->invite?->package_id) {
            $submission->invite?->update(['package_id' => $package->id]); // their next update goes to the same package
        }

        return redirect()->route('manager.packages.booking-setup', $package->id)
            ->with('success', 'Supplier rates filled in: ' . implode(', ', $done) . '. Check the Price check table, then the package is ready.');
    }

    private function photoFile(int $id, int $index): array
    {
        $submission = SupplierSubmission::findOrFail($id);
        $photo = ((array) $submission->photos)[$index] ?? null;
        abort_unless($photo && is_file(storage_path('app/' . $photo['path'])), 404);
        return $photo;
    }
}
