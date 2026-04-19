<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NomodTransaction;
use App\Models\EsimOrder;
use App\Models\ReferralAgent;
use App\Models\ReferralTracking;

echo "=== Simulating Successful Payment ===\n\n";

// Find the transaction
$transaction = NomodTransaction::where('order_id', 'ORDESIM32')->first();
if ($transaction) {
    $transaction->update(['status' => 'paid']);
    echo "Transaction updated to: paid\n";
} else {
    echo "Transaction not found\n";
}

// Find the eSIM order
$esimOrder = EsimOrder::find(32);
if ($esimOrder) {
    $esimOrder->update(['payment_status' => 'paid']);
    echo "eSIM Order: {$esimOrder->order_reference}\n";
    echo "Customer: {$esimOrder->customer_name} ({$esimOrder->customer_email})\n";
    echo "Amount: AED {$esimOrder->selling_price}\n\n";
} else {
    echo "eSIM order not found\n";
    exit;
}

// Find the referral agent
$agent = ReferralAgent::where('referral_code', 'pragathiga2h7')->first();
if (!$agent) {
    echo "Referral agent not found!\n";
    exit;
}

echo "Referral Agent: {$agent->name}\n";
echo "Commission: {$agent->commission_value}" . ($agent->commission_type === 'percentage' ? '%' : ' AED') . "\n\n";

// Calculate commission
$commission = $agent->calculateCommission((float) $esimOrder->selling_price);
echo "Calculated Commission: AED {$commission}\n\n";

// Check if tracking already exists
$existing = ReferralTracking::where('order_id', 'ORDESIM32')->first();
if ($existing) {
    echo "Referral tracking already exists for this order.\n";
    exit;
}

// Create referral tracking
$tracking = ReferralTracking::create([
    'referral_agent_id' => $agent->id,
    'order_id' => 'ORDESIM32',
    'order_type' => 'esim',
    'customer_email' => $esimOrder->customer_email,
    'customer_name' => $esimOrder->customer_name,
    'order_amount' => $esimOrder->selling_price,
    'currency' => 'AED',
    'commission_amount' => $commission,
    'commission_type' => $agent->commission_type,
    'commission_value' => $agent->commission_value,
    'status' => 'pending',
    'payment_status' => 'completed',
]);

// Update agent stats
$agent->updateStats();

echo "=== SUCCESS ===\n";
echo "Referral tracking created!\n";
echo "Commission: AED {$commission} (Pending)\n";
echo "\nCheck admin panel: /admin/referrals/commissions\n";
