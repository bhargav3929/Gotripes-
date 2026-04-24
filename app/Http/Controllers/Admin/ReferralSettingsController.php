<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralSetting;
use Illuminate\Http\Request;

class ReferralSettingsController extends Controller
{
    /**
     * Show the referral settings form.
     */
    public function index()
    {
        $settings = ReferralSetting::getSettings();

        return view('admin.referrals.settings', compact('settings'));
    }

    /**
     * Persist updated settings — handles commission and program sections separately.
     */
    public function update(Request $request)
    {
        $settings = ReferralSetting::getSettings();
        $section  = $request->input('form_section', 'commission');

        if ($section === 'commission') {
            $type = $request->input('commission_type', 'percentage');
            $valueField = $type === 'percentage' ? 'commission_percentage' : 'commission_flat';
            $request->validate([
                'commission_type' => 'required|in:percentage,fixed',
                $valueField       => 'required|numeric|min:0',
            ]);
            $settings->update([
                'commission_type'  => $type,
                'commission_value' => $request->input($valueField),
            ]);
            $message = 'Commission structure saved.';
        } else {
            $request->validate([
                'min_withdrawal_amount' => 'required|numeric|min:0',
            ]);
            $settings->update([
                'min_withdrawal_amount' => $request->input('min_withdrawal_amount'),
                'auto_approve'          => $request->boolean('auto_approve_commissions'),
                'signup_enabled'        => $request->boolean('enable_public_signup'),
            ]);
            $message = 'Program settings saved.';
        }

        return back()->with([
            'message'    => $message,
            'alert-type' => 'success',
        ]);
    }
}
