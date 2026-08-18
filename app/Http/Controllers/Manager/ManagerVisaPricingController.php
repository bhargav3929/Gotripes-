<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\EvisaSetting;
use App\Models\UAEVisaMaster;
use App\Models\Emirates;
use App\Models\UAEVisaPackage;
use App\Models\UAEVisaPrice;
use App\Models\UAEVisaPackageDeposit;
use Illuminate\Http\Request;

class ManagerVisaPricingController extends Controller
{
    /**
     * Emirates that carry a refundable security deposit.
     *
     * Sharjah is the only one today, but the client asked to be able to add
     * others without a code change, so the deposit fields are simply revealed
     * for any emirate named here — and a manager can still type a deposit on
     * any package if an emirate starts requiring one mid-season.
     */
    public const DEPOSIT_EMIRATES = ['sharjah'];

    // Mirrors the option lists in the Pricing tab's dropdowns — kept here so
    // storePackage() can pre-generate the full matrix for a new package.
    private const ENTRY_TYPES = ['Single Entry', 'Multiple Entry'];
    private const DURATIONS = ['30 Days', '60 Days'];
    private const TRAVELLER_TYPES = ['Adult', 'Child', 'Infant'];

    public function index()
    {
        $emirates = Emirates::where('isActive', 1)->orderBy('emiratesName')->get();
        $packages = UAEVisaPackage::with(['emirate', 'prices', 'deposits'])->orderBy('name')->get();
        $prices   = UAEVisaPrice::with('package.emirate')->get();

        $company = current_company();
        $hotelFee  = $company?->getSetting('visa_hotel_booking_fee', 25) ?? 25;
        $ticketFee = $company?->getSetting('visa_ticket_booking_fee', 25) ?? 25;

        // Global markup applied to every Fluxir e-Visa (the /e-visa storefront).
        $evisaMarkup = EvisaSetting::markupPercent();

        // Emirate IDs whose packages should reveal the deposit fields.
        $depositEmirateIds = $emirates
            ->filter(fn($e) => in_array(strtolower(trim($e->emiratesName)), self::DEPOSIT_EMIRATES, true))
            ->pluck('emiratesID')
            ->values();

        return view('manager.visa-pricing.index', compact(
            'emirates',
            'packages',
            'prices',
            'hotelFee',
            'ticketFee',
            'evisaMarkup',
            'depositEmirateIds'
        ));
    }

    /** Update the global e-Visa (Fluxir) markup percentage. */
    public function updateEvisaMarkup(Request $request)
    {
        $validated = $request->validate([
            'markup_percent' => 'required|numeric|min:0|max:1000',
        ]);

        EvisaSetting::current()->update(['markup_percent' => $validated['markup_percent']]);

        return back()->with('success', 'e-Visa markup updated to ' . rtrim(rtrim(number_format($validated['markup_percent'], 2), '0'), '.') . '%.');
    }

    /**
     * The two add-on fees that are genuinely global (they apply to every
     * package regardless of emirate). Everything else that used to live on the
     * old "Add-On & Settings" tab is now configured per package.
     */
    public function updateServiceFees(Request $request)
    {
        $validated = $request->validate([
            'visa_hotel_booking_fee'  => 'required|numeric|min:0',
            'visa_ticket_booking_fee' => 'required|numeric|min:0',
        ]);

        $company = current_company();

        $settings = $company->settings ?? [];
        $settings['visa_hotel_booking_fee']  = (float) $validated['visa_hotel_booking_fee'];
        $settings['visa_ticket_booking_fee'] = (float) $validated['visa_ticket_booking_fee'];
        $company->settings = $settings;
        $company->save();

        return redirect()->route('manager.visa-pricing.index')
            ->with('success', 'Add-on service fees updated.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'UAEVisaDuration' => 'required|string|max:100',
            'UAEVPrice'       => 'required|numeric|min:0',
        ]);

        UAEVisaMaster::create([
            'UAEVisaDuration' => $validated['UAEVisaDuration'],
            'UAEVPrice'       => $validated['UAEVPrice'],
            'isActive'        => 1,
            'createdBy'       => auth()->user()?->name ?? 'manager',
            'createdDate'     => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'legacy'])
            ->with('success', 'Visa price added.');
    }

    public function update(Request $request, $id)
    {
        $visa = UAEVisaMaster::where('vID', $id)->where('isActive', 1)->firstOrFail();

        $validated = $request->validate([
            'UAEVisaDuration' => 'required|string|max:100',
            'UAEVPrice'       => 'required|numeric|min:0',
        ]);

        $visa->update([
            'UAEVisaDuration' => $validated['UAEVisaDuration'],
            'UAEVPrice'       => $validated['UAEVPrice'],
            'modifiedBy'      => auth()->user()?->name ?? 'manager',
            'modifiedDate'    => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'legacy'])
            ->with('success', 'Visa price updated.');
    }

    public function destroy($id)
    {
        $visa = UAEVisaMaster::where('vID', $id)->where('isActive', 1)->firstOrFail();

        $visa->update([
            'isActive'     => 0,
            'modifiedBy'   => auth()->user()?->name ?? 'manager',
            'modifiedDate' => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'legacy'])
            ->with('success', 'Visa price removed.');
    }

    // --- Emirates CRUD ---
    public function storeEmirate(Request $request)
    {
        $validated = $request->validate([
            'emiratesName'        => 'required|string|max:100|unique:tbl_emirates,emiratesName',
            'emiratesDescription' => 'nullable|string|max:1000',
            'emiratesImage'       => 'nullable|string|max:255',
        ]);

        Emirates::create([
            'emiratesName'        => $validated['emiratesName'],
            'emiratesDescription' => $validated['emiratesDescription'] ?? '',
            'emiratesImage'       => $validated['emiratesImage'] ?? '',
            'country'             => 'United Arab Emirates',
            'isActive'            => 1,
            'createdBy'           => auth()->user()?->name ?? 'manager',
            'createdDate'         => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'emirates'])
            ->with('success', 'Emirate added successfully.');
    }

    public function updateEmirate(Request $request, $id)
    {
        $emirate = Emirates::findOrFail($id);

        $validated = $request->validate([
            'emiratesName'        => 'required|string|max:100|unique:tbl_emirates,emiratesName,' . $id . ',emiratesID',
            'emiratesDescription' => 'nullable|string|max:1000',
            'emiratesImage'       => 'nullable|string|max:255',
            'isActive'            => 'required|boolean',
        ]);

        $emirate->update([
            'emiratesName'        => $validated['emiratesName'],
            'emiratesDescription' => $validated['emiratesDescription'] ?? '',
            'emiratesImage'       => $validated['emiratesImage'] ?? '',
            'isActive'            => $validated['isActive'],
            'modifiedBy'          => auth()->user()?->name ?? 'manager',
            'modifiedDate'        => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'emirates'])
            ->with('success', 'Emirate updated successfully.');
    }

    public function destroyEmirate($id)
    {
        $emirate = Emirates::findOrFail($id);
        $emirate->update([
            'isActive'     => 0,
            'modifiedBy'   => auth()->user()?->name ?? 'manager',
            'modifiedDate' => now(),
        ]);

        return redirect()->route('manager.visa-pricing.index', ['tab' => 'emirates'])
            ->with('success', 'Emirate deactivated.');
    }

    // --- Packages CRUD ---

    /**
     * Validation shared by create and edit. `emails` allows a comma-separated
     * list so a package can notify two suppliers at once.
     */
    private function packageRules(bool $forUpdate = false): array
    {
        $rules = [
            'emirates_id'       => 'required|exists:tbl_emirates,emiratesID',
            'name'              => 'required|string|max:100',
            // Required once a package exists and is being edited — the update
            // form always sends one. Optional on creation so a bare
            // name+emirate submission still succeeds and defaults to Standard;
            // packageAttributes() below fills the default in.
            'package_type'      => ($forUpdate ? 'required' : 'nullable') . '|in:Standard,Urgent',
            'description'       => 'nullable|string|max:1000',
            'security_deposit'  => 'nullable|numeric|min:0',
            'deposit_admin_fee' => 'nullable|numeric|min:0|lte:security_deposit',
            'supplier_email'    => 'nullable|string|max:255',
            'company_email'     => 'nullable|string|max:255',
        ];

        if ($forUpdate) {
            $rules['isActive'] = 'required|boolean';
        }

        return $rules;
    }

    private function packageMessages(): array
    {
        return [
            'deposit_admin_fee.lte' => 'The processing fee cannot be greater than the security deposit.',
        ];
    }

    /**
     * Normalise the optional package fields: '' becomes null so the model falls
     * back to the company-wide setting, while an explicit 0 is preserved as
     * "this package charges no deposit".
     */
    private function packageAttributes(array $validated): array
    {
        $nullIfBlank = fn($v) => ($v === null || $v === '') ? null : $v;

        return [
            'emirates_id'       => $validated['emirates_id'],
            'name'              => $validated['name'],
            'package_type'      => $validated['package_type'] ?? 'Standard',
            'description'       => $validated['description'] ?? null,
            'security_deposit'  => $nullIfBlank($validated['security_deposit'] ?? null),
            'deposit_admin_fee' => $nullIfBlank($validated['deposit_admin_fee'] ?? null),
            'supplier_email'    => $nullIfBlank(trim((string) ($validated['supplier_email'] ?? ''))),
            'company_email'     => $nullIfBlank(trim((string) ($validated['company_email'] ?? ''))),
        ];
    }

    /**
     * Create a package and pre-fill its full pricing matrix.
     *
     * The client works one visa at a time — "Sharjah, urgent, adult, 30 days,
     * 450 AED, this supplier" — so when the create form carries a specific
     * entry-type/duration/traveller/price combination, that row is created
     * active immediately and the package is live in the same trip. The rest
     * of the matrix (and every row when the form carries no price at all) is
     * pre-filled inactive at AED 0, so the manager is never stuck adding
     * eleven more rows by hand before the Pricing Matrix tab has anything to
     * show — the matrix tab then handles activating and editing the rest.
     */
    public function storePackage(Request $request)
    {
        $validated = $request->validate(
            $this->packageRules() + [
                'entry_type'     => 'nullable|string|max:100',
                'duration'       => 'nullable|string|max:100',
                'traveller_type' => 'nullable|string|max:100',
                'price'          => 'nullable|numeric|min:0',
            ],
            $this->packageMessages()
        );

        $package = UAEVisaPackage::create(
            $this->packageAttributes($validated) + ['isActive' => 1]
        );

        $hasInitialRow = filled($validated['entry_type'] ?? null)
            && filled($validated['duration'] ?? null)
            && filled($validated['traveller_type'] ?? null)
            && isset($validated['price']);

        if ($hasInitialRow) {
            UAEVisaPrice::create([
                'visa_package_id' => $package->id,
                'entry_type'      => $validated['entry_type'],
                'duration'        => $validated['duration'],
                'traveller_type'  => $validated['traveller_type'],
                'price'           => $validated['price'],
                'isActive'        => 1,
            ]);
        }

        // Pre-fill the rest of the matrix so the manager only has to type in
        // prices for the remaining combinations, instead of adding every row
        // by hand. Left inactive at AED 0 until a real price is set and
        // activated — this keeps unpriced rows from ever appearing on the
        // storefront. Skips the combination already created above, if any.
        foreach (self::ENTRY_TYPES as $entryType) {
            foreach (self::DURATIONS as $duration) {
                foreach (self::TRAVELLER_TYPES as $travellerType) {
                    if ($hasInitialRow
                        && $entryType === $validated['entry_type']
                        && $duration === $validated['duration']
                        && $travellerType === $validated['traveller_type']) {
                        continue;
                    }
                    UAEVisaPrice::create([
                        'visa_package_id' => $package->id,
                        'entry_type'      => $entryType,
                        'duration'        => $duration,
                        'traveller_type'  => $travellerType,
                        'price'           => 0,
                        'isActive'        => 0,
                    ]);
                }
            }
        }

        $message = $hasInitialRow
            ? "\"{$package->name}\" created with its first price row, and the rest of the matrix pre-filled below."
            : "\"{$package->name}\" created. Fill in prices below and mark rows Active to publish them.";

        return redirect()->route('manager.visa-pricing.index', ['package' => $package->id])
            ->with('success', $message);
    }

    public function updatePackage(Request $request, $id)
    {
        $package = UAEVisaPackage::findOrFail($id);

        $validated = $request->validate($this->packageRules(true), $this->packageMessages());

        $package->update(
            $this->packageAttributes($validated) + ['isActive' => $validated['isActive']]
        );

        return redirect()->route('manager.visa-pricing.index', ['package' => $package->id])
            ->with('success', 'Visa Package updated successfully.');
    }

    public function destroyPackage($id)
    {
        $package = UAEVisaPackage::findOrFail($id);
        // Cascade-deletes uae_visa_prices rows via the FK; no other table has a
        // hard reference to a package (booking history stores a denormalized
        // name snapshot instead), so this is safe as a real delete.
        $package->delete();

        return redirect()->route('manager.visa-pricing.index')
            ->with('success', 'Visa Package deleted.');
    }

    // --- Prices Matrix CRUD ---
    public function storePriceRow(Request $request)
    {
        $validated = $request->validate([
            'visa_package_id' => 'required|exists:uae_visa_packages,id',
            'entry_type'      => 'required|string|max:100',
            'duration'        => 'required|string|max:100',
            'traveller_type'  => 'required|string|max:100',
            'nationality'     => 'nullable|string|max:100',
            'price'           => 'required|numeric|min:0',
        ]);

        $nationality = $validated['nationality'] ?? null;

        // Every package already gets the full matrix pre-filled on creation,
        // so a manual add here is either a nationality-specific override or a
        // genuine duplicate. Block the duplicate case — two rows for the same
        // combination leaves the storefront with no way to know which price
        // is the real one.
        $duplicate = UAEVisaPrice::where('visa_package_id', $validated['visa_package_id'])
            ->where('entry_type', $validated['entry_type'])
            ->where('duration', $validated['duration'])
            ->where('traveller_type', $validated['traveller_type'])
            ->where('nationality', $nationality)
            ->exists();

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'price' => 'A price row already exists for this package, entry type, duration, traveller type'
                    . ($nationality ? " and nationality ($nationality)" : ' with no nationality set')
                    . '. Edit it in the matrix below instead.',
            ]);
        }

        UAEVisaPrice::create([
            'visa_package_id' => $validated['visa_package_id'],
            'entry_type'      => $validated['entry_type'],
            'duration'        => $validated['duration'],
            'traveller_type'  => $validated['traveller_type'],
            'nationality'     => $nationality,
            'price'           => $validated['price'],
            'isActive'        => 1,
        ]);

        return redirect()->route('manager.visa-pricing.index', ['package' => $validated['visa_package_id']])
            ->with('success', 'Pricing row added successfully.');
    }

    public function updatePriceRow(Request $request, $id)
    {
        $price = UAEVisaPrice::findOrFail($id);

        $validated = $request->validate([
            'visa_package_id' => 'required|exists:uae_visa_packages,id',
            'entry_type'      => 'required|string|max:100',
            'duration'        => 'required|string|max:100',
            'traveller_type'  => 'required|string|max:100',
            'nationality'     => 'nullable|string|max:100',
            'price'           => 'required|numeric|min:0',
            'isActive'        => 'required|boolean',
        ]);

        $price->update($validated);

        return redirect()->route('manager.visa-pricing.index', ['package' => $price->visa_package_id])
            ->with('success', 'Pricing row updated successfully.');
    }

    public function bulkUpdatePriceRow(Request $request)
    {
        $validated = $request->validate([
            'prices' => 'required|array',
            'prices.*.entry_type' => 'required|string|max:100',
            'prices.*.duration' => 'required|string|max:100',
            'prices.*.traveller_type' => 'required|string|max:100',
            'prices.*.nationality' => 'nullable|string|max:100',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.isActive' => 'required|boolean',
        ]);

        $packageId = null;

        foreach ($validated['prices'] as $id => $data) {
            $price = UAEVisaPrice::findOrFail($id);
            $price->update([
                'entry_type'     => $data['entry_type'],
                'duration'       => $data['duration'],
                'traveller_type' => $data['traveller_type'],
                'nationality'    => $data['nationality'] ?? null,
                'price'          => $data['price'],
                'isActive'       => $data['isActive'],
            ]);
            $packageId = $packageId ?? $price->visa_package_id;
        }

        return redirect()->route('manager.visa-pricing.index', array_filter(['package' => $packageId]))
            ->with('success', 'Pricing updated successfully.');
    }

    public function destroyPriceRow($id)
    {
        $price = UAEVisaPrice::findOrFail($id);
        $packageId = $price->visa_package_id;
        $price->delete();

        return redirect()->route('manager.visa-pricing.index', ['package' => $packageId])
            ->with('success', 'Pricing row deleted.');
    }

    // --- Nationality-specific deposits CRUD ---
    //
    // Mirrors the price-row CRUD above: a row with nationality left blank is
    // this package's default deposit, a row with a nationality set overrides
    // it for that nationality only (see UAEVisaPackage::resolveDepositRow()).

    public function storeDeposit(Request $request)
    {
        $validated = $request->validate([
            'visa_package_id'    => 'required|exists:uae_visa_packages,id',
            'nationality'        => 'nullable|string|max:100',
            'security_deposit'   => 'required|numeric|min:0',
            'deposit_admin_fee'  => 'nullable|numeric|min:0|lte:security_deposit',
        ], [
            'deposit_admin_fee.lte' => 'The processing fee cannot be greater than the security deposit.',
        ]);

        $nationality = $validated['nationality'] ?? null;

        $duplicate = UAEVisaPackageDeposit::where('visa_package_id', $validated['visa_package_id'])
            ->where('nationality', $nationality)
            ->exists();

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'security_deposit' => 'A deposit row already exists for this package'
                    . ($nationality ? " and nationality ($nationality)" : ' with no nationality set')
                    . '. Edit it below instead.',
            ]);
        }

        UAEVisaPackageDeposit::create([
            'visa_package_id'   => $validated['visa_package_id'],
            'nationality'       => $nationality,
            'security_deposit'  => $validated['security_deposit'],
            'deposit_admin_fee' => $validated['deposit_admin_fee'] ?? 0,
            'isActive'          => 1,
        ]);

        return redirect()->route('manager.visa-pricing.index', ['package' => $validated['visa_package_id']])
            ->with('success', 'Deposit row added successfully.');
    }

    public function updateDeposit(Request $request, $id)
    {
        $deposit = UAEVisaPackageDeposit::findOrFail($id);

        $validated = $request->validate([
            'nationality'        => 'nullable|string|max:100',
            'security_deposit'   => 'required|numeric|min:0',
            'deposit_admin_fee'  => 'nullable|numeric|min:0|lte:security_deposit',
            'isActive'           => 'required|boolean',
        ], [
            'deposit_admin_fee.lte' => 'The processing fee cannot be greater than the security deposit.',
        ]);

        $deposit->update([
            'nationality'       => $validated['nationality'] ?? null,
            'security_deposit'  => $validated['security_deposit'],
            'deposit_admin_fee' => $validated['deposit_admin_fee'] ?? 0,
            'isActive'          => $validated['isActive'],
        ]);

        return redirect()->route('manager.visa-pricing.index', ['package' => $deposit->visa_package_id])
            ->with('success', 'Deposit row updated successfully.');
    }

    public function destroyDeposit($id)
    {
        $deposit = UAEVisaPackageDeposit::findOrFail($id);
        $packageId = $deposit->visa_package_id;
        $deposit->delete();

        return redirect()->route('manager.visa-pricing.index', ['package' => $packageId])
            ->with('success', 'Deposit row deleted.');
    }
}
