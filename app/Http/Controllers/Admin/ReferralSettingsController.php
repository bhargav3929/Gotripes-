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
     * Persist updated settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'commission_type'       => 'required|in:percentage,fixed',
            'commission_value'      => 'required|numeric|min:0',
            'auto_approve'          => 'boolean',
            'min_withdrawal_amount' => 'required|numeric|min:0',
            'signup_enabled'        => 'boolean',
        ]);

        $settings = ReferralSetting::getSettings();
        $settings->update([
            'commission_type'       => $validated['commission_type'],
            'commission_value'      => $validated['commission_value'],
            'auto_approve'          => $request->boolean('auto_approve'),
            'min_withdrawal_amount' => $validated['min_withdrawal_amount'],
            'signup_enabled'        => $request->boolean('signup_enabled'),
        ]);

        return back()->with([
            'message'    => 'Referral settings updated successfully.',
            'alert-type' => 'success',
        ]);
    }
}
