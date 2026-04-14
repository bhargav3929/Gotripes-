<?php

namespace App\Services;

use App\Http\Middleware\ReferralTrackingMiddleware;
use App\Models\ReferralAgent;
use App\Models\ReferralClick;
use App\Models\ReferralTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Process referral for a completed order
     */
    public function processOrderReferral(
        string $orderId,
        string $orderType,
        float $orderAmount,
        string $customerEmail,
        string $customerName = null,
        array $orderDetails = [],
        Request $request = null
    ): ?ReferralTracking {
        try {
            // Get referral code from cookie
            $referralCode = $request
                ? ReferralTrackingMiddleware::getReferralCode($request)
                : request()->cookie(ReferralTrackingMiddleware::REFERRAL_COOKIE_NAME);

            if (!$referralCode) {
                return null;
            }

            // Get the referral agent
            $agent = ReferralAgent::where('referral_code', $referralCode)
                ->where('status', 'active')
                ->first();

            if (!$agent) {
                Log::info("Referral agent not found or inactive for code: {$referralCode}");
                return null;
            }

            // Prevent self-referrals
            if ($this->isSelfReferral($agent, $customerEmail)) {
                Log::info("Self-referral prevented for agent: {$agent->id}, customer: {$customerEmail}");
                return null;
            }

            // Check for duplicate order
            if ($this->isDuplicateOrder($orderId)) {
                Log::info("Duplicate order detected: {$orderId}");
                return null;
            }

            // Calculate commission
            $commissionAmount = $agent->calculateCommission($orderAmount);

            // Create tracking record
            $tracking = ReferralTracking::create([
                'referral_agent_id' => $agent->id,
                'order_id' => $orderId,
                'order_type' => $orderType,
                'customer_email' => $customerEmail,
                'customer_name' => $customerName,
                'order_amount' => $orderAmount,
                'currency' => 'AED',
                'commission_amount' => $commissionAmount,
                'commission_type' => $agent->commission_type,
                'commission_value' => $agent->commission_value,
                'status' => 'pending',
                'payment_status' => 'completed',
                'order_details' => $orderDetails,
                'ip_address' => $request ? $request->ip() : request()->ip(),
                'user_agent' => $request ? $request->userAgent() : request()->userAgent(),
            ]);

            // Mark recent click as converted
            $this->markClickAsConverted($agent->id);

            // Update agent stats
            $agent->updateStats();

            Log::info("Referral tracking created for order: {$orderId}, agent: {$agent->id}, commission: {$commissionAmount}");

            return $tracking;

        } catch (\Exception $e) {
            Log::error("Error processing referral: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if this is a self-referral
     */
    protected function isSelfReferral(ReferralAgent $agent, string $customerEmail): bool
    {
        return strtolower($agent->email) === strtolower($customerEmail);
    }

    /**
     * Check if order already has a referral tracking record
     */
    protected function isDuplicateOrder(string $orderId): bool
    {
        return ReferralTracking::where('order_id', $orderId)->exists();
    }

    /**
     * Mark the most recent click from this agent as converted
     */
    protected function markClickAsConverted(int $agentId): void
    {
        ReferralClick::where('referral_agent_id', $agentId)
            ->where('converted', false)
            ->orderBy('created_at', 'desc')
            ->first()
            ?->markAsConverted();
    }

    /**
     * Process refund for an order
     */
    public function processRefund(string $orderId, float $refundAmount = null): bool
    {
        $tracking = ReferralTracking::where('order_id', $orderId)->first();

        if (!$tracking) {
            return false;
        }

        return $tracking->processRefund($refundAmount);
    }

    /**
     * Bulk approve commissions
     */
    public function bulkApprove(array $trackingIds, int $userId): int
    {
        $approved = 0;

        DB::transaction(function () use ($trackingIds, $userId, &$approved) {
            $trackings = ReferralTracking::whereIn('id', $trackingIds)
                ->where('status', 'pending')
                ->get();

            foreach ($trackings as $tracking) {
                if ($tracking->canBeApproved()) {
                    $tracking->approve($userId);
                    $approved++;
                }
            }
        });

        return $approved;
    }

    /**
     * Bulk mark as paid
     */
    public function bulkMarkAsPaid(array $trackingIds): int
    {
        $paid = 0;

        DB::transaction(function () use ($trackingIds, &$paid) {
            $trackings = ReferralTracking::whereIn('id', $trackingIds)
                ->where('status', 'approved')
                ->get();

            foreach ($trackings as $tracking) {
                if ($tracking->canBeMarkedAsPaid()) {
                    $tracking->markAsPaid();
                    $paid++;
                }
            }
        });

        return $paid;
    }

    /**
     * Get agent statistics
     */
    public function getAgentStats(ReferralAgent $agent, string $period = 'all'): array
    {
        $query = $agent->referralTracking();

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        $stats = [
            'total_orders' => (clone $query)->count(),
            'total_sales' => (clone $query)->whereIn('status', ['approved', 'paid'])->count(),
            'total_revenue' => (clone $query)->whereIn('status', ['approved', 'paid'])->sum('order_amount'),
            'total_commission' => (clone $query)->whereIn('status', ['approved', 'paid'])->sum('commission_amount'),
            'pending_commission' => (clone $query)->where('status', 'pending')->sum('commission_amount'),
            'paid_commission' => (clone $query)->where('status', 'paid')->sum('commission_amount'),
        ];

        // Click stats
        $clickQuery = $agent->clicks();
        switch ($period) {
            case 'today':
                $clickQuery->today();
                break;
            case 'week':
                $clickQuery->thisWeek();
                break;
            case 'month':
                $clickQuery->thisMonth();
                break;
        }

        $stats['total_clicks'] = $clickQuery->count();
        $stats['converted_clicks'] = (clone $clickQuery)->converted()->count();
        $stats['conversion_rate'] = $stats['total_clicks'] > 0
            ? round(($stats['total_sales'] / $stats['total_clicks']) * 100, 2)
            : 0;

        return $stats;
    }

    /**
     * Get dashboard overview stats for admin
     */
    public function getAdminDashboardStats(): array
    {
        return [
            'total_agents' => ReferralAgent::count(),
            'active_agents' => ReferralAgent::active()->count(),
            'total_orders' => ReferralTracking::count(),
            'pending_commissions' => ReferralTracking::pending()->sum('commission_amount'),
            'approved_commissions' => ReferralTracking::approved()->sum('commission_amount'),
            'paid_commissions' => ReferralTracking::paid()->sum('commission_amount'),
            'total_clicks' => ReferralClick::count(),
            'total_conversions' => ReferralClick::converted()->count(),
            'today_orders' => ReferralTracking::whereDate('created_at', today())->count(),
            'today_clicks' => ReferralClick::today()->count(),
            'this_month_revenue' => ReferralTracking::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->whereIn('status', ['approved', 'paid'])
                ->sum('order_amount'),
        ];
    }

    /**
     * Get top performing agents
     */
    public function getTopAgents(int $limit = 10, string $period = 'month'): \Illuminate\Support\Collection
    {
        $query = ReferralTracking::selectRaw('referral_agent_id, COUNT(*) as total_orders, SUM(commission_amount) as total_commission')
            ->whereIn('status', ['approved', 'paid']);

        switch ($period) {
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        return $query->groupBy('referral_agent_id')
            ->orderByDesc('total_commission')
            ->limit($limit)
            ->with('referralAgent')
            ->get()
            ->map(function ($item) {
                return [
                    'agent' => $item->referralAgent,
                    'total_orders' => $item->total_orders,
                    'total_commission' => $item->total_commission,
                ];
            });
    }
}
