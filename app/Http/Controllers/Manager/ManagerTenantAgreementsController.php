<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\B2bTenantAgreement;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * B2B Tenant Portal Agreement for partner companies that get their own
 * GoTrips portal. Staff type the partner's details, download a letterheaded
 * PDF and send it on WhatsApp. Monthly rent and the percentage on sales are
 * printed as blank lines on purpose — Amer fills them in by hand, case by
 * case (26 Nov 2026), so there is no input for either.
 *
 * Platform only: a white-label tenant never offers portals to other
 * companies, so on any tenant host this 404s. Owner/admin only, as on
 * Agent Contract.
 */
class ManagerTenantAgreementsController extends Controller
{
    public const TERMS = [6 => '6 months', 12 => '12 months', 24 => '24 months', 36 => '36 months'];

    public function index()
    {
        $this->authorizePlatformAdmin();

        $agreements = B2bTenantAgreement::with('author')->latest('id')->limit(100)->get();
        $generated = session('generated_agreement')
            ? $agreements->firstWhere('id', session('generated_agreement'))
            : null;

        return view('manager.tenant-agreements.index', [
            'agreements' => $agreements,
            'generated'  => $generated,
            'terms'      => self::TERMS,
            'modules'    => Company::AVAILABLE_FEATURES,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizePlatformAdmin();

        $validated = $request->validate([
            'legal_name'     => 'required|string|max:200',
            'trade_licence'  => 'nullable|string|max:100',
            'address'        => 'required|string|max:500',
            'country'        => 'required|string|max:100',
            'contact_name'   => 'required|string|max:150',
            'contact_title'  => 'nullable|string|max:100',
            'email'          => 'required|email|max:190',
            // WhatsApp needs the full international number to open a chat.
            'phone'          => ['required', 'string', 'max:30', 'regex:/^\+?[0-9][0-9\s\-().]{6,}$/'],
            'portal_domain'  => 'nullable|string|max:190',
            'start_date'     => 'required|date',
            'term_months'    => 'required|integer|in:' . implode(',', array_keys(self::TERMS)),
            'modules'        => 'nullable|array',
            'modules.*'      => 'string|in:' . implode(',', array_keys(Company::AVAILABLE_FEATURES)),
        ], [
            'phone.regex' => 'Enter the WhatsApp number with its country code, e.g. +971 50 123 4567.',
        ]);

        $validated['modules'] = array_values(array_intersect(
            array_keys(Company::AVAILABLE_FEATURES),
            $validated['modules'] ?? []
        ));

        $agreement = DB::transaction(fn () => B2bTenantAgreement::create([
            'reference'    => B2bTenantAgreement::nextReference(),
            'partner_name' => $validated['legal_name'],
            'partner'      => $validated,
            'created_by'   => Auth::id(),
        ]));

        return redirect()->route('manager.tenant-agreements.index')
            ->with('generated_agreement', $agreement->id)
            ->with('success', "Agreement {$agreement->reference} is ready. The PDF is downloading.");
    }

    public function download(B2bTenantAgreement $agreement)
    {
        $this->authorizePlatformAdmin();

        return $this->pdf($agreement)->download($agreement->fileName());
    }

    /** Rendered from the stored partner details every time; nothing on disk. */
    private function pdf(B2bTenantAgreement $agreement)
    {
        $platform = current_company();

        return Pdf::loadView('contracts.b2b-tenant-agreement', [
            'agreement' => $agreement,
            'platform'  => $platform,
            'modules'   => Company::AVAILABLE_FEATURES,
            'terms'     => self::TERMS,
        ])->setPaper('a4');
    }

    private function authorizePlatformAdmin(): void
    {
        abort_unless(current_company()?->slug === 'gotrips', 404);

        $user = Auth::user();
        abort_unless($user && ($user->is_super_admin || $user->isSuperAdmin() || $user->isCompanyAdmin()), 403);
    }
}
