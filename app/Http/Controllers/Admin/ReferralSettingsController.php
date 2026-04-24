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
            $validated = $request->validate([
                'commission_type'  => 'required|in:percentage,fixed',
                'commission_value' => 'required|numeric|min:0',
            ]);
            $settings->update([
                'commission_type'  => $validated['commission_type'],
                'commission_value' => $validated['commission_value'],
            ]);
            $message = 'Commission structure saved.';
        } else {
            $validated = $request->validate([
                'min_withdrawal_amount' => 'required|numeric|min:0',
                'auto_approve'          => 'boolean',
                'signup_enabled'        => 'boolean',
            ]);
            $settings->update([
                'min_withdrawal_amount' => $validated['min_withdrawal_amount'],
                'auto_approve'          => $request->boolean('auto_approve'),
                'signup_enabled'        => $request->boolean('signup_enabled'),
            ]);
            $message = 'Program settings saved.';
        }

        return back()->with([
            'message'    => $message,
            'alert-type' => 'success',
        ]);
    }
}
