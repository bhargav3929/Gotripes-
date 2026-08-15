<?php

namespace App\Http\Controllers\B2bPartner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class B2bPartnerDashboardController extends Controller
{
    public function dashboard()
    {
        $partner = Auth::guard('b2b_partner')->user();

        $daysUntilExpiry = null;
        if ($partner->isUae() && $partner->trade_license_expiry_date) {
            $daysUntilExpiry = now()->diffInDays($partner->trade_license_expiry_date, false);
        }

        return view('b2b-partner.dashboard', compact('partner', 'daysUntilExpiry'));
    }

    public function downloadContract()
    {
        $partner = Auth::guard('b2b_partner')->user();

        if (!$partner->contract_pdf_path || !Storage::disk('public')->exists($partner->contract_pdf_path)) {
            abort(404, 'Contract not found.');
        }

        return Storage::disk('public')->download(
            $partner->contract_pdf_path,
            $partner->company_name . ' - Partner Agreement.pdf'
        );
    }
}
