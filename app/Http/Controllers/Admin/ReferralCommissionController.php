<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralAgent;
use App\Models\ReferralTracking;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReferralCommissionController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Display a listing of commissions
     */
    public function index(Request $request)
    {
        $query = ReferralTracking::with('referralAgent');

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter by agent
        if ($agentId = $request->get('agent_id')) {
            $query->where('referral_agent_id', $agentId);
        }

        // Filter by date range
        if ($startDate = $request->get('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Filter by order type
        if ($orderType = $request->get('order_type')) {
            $query->where('order_type', $orderType);
        }

        $commissions = $query->orderBy('created_at', 'desc')->paginate(20);
        $agents = ReferralAgent::orderBy('name')->get();

        // Stats
        $stats = [
            'pending' => ReferralTracking::pending()->sum('commission_amount'),
            'approved' => ReferralTracking::approved()->sum('commission_amount'),
            'paid' => ReferralTracking::paid()->sum('commission_amount'),
            'pending_count' => ReferralTracking::pending()->count(),
            'approved_count' => ReferralTracking::approved()->count(),
        ];

        return view('admin.referrals.commissions.index', compact('commissions', 'agents', 'stats'));
    }

    /**
     * View commission details
     */
    public function show(ReferralTracking $commission)
    {
        $commission->load('referralAgent', 'approvedByUser');
        return view('admin.referrals.commissions.show', compact('commission'));
    }

    /**
     * Approve a commission
     */
    public function approve(Request $request, ReferralTracking $commission)
    {
        if (!$commission->canBeApproved()) {
            return back()->with([
                'message' => 'This commission cannot be approved.',
                'alert-type' => 'danger'
            ]);
        }

        $commission->approve(auth()->id());

        return back()->with([
            'message' => 'Commission approved successfully!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Reject a commission
     */
    public function reject(Request $request, ReferralTracking $commission)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $commission->reject($validated['rejection_reason'], auth()->id());

        return back()->with([
            'message' => 'Commission rejected.',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Mark commission as paid
     */
    public function markPaid(ReferralTracking $commission)
    {
        if (!$commission->canBeMarkedAsPaid()) {
            return back()->with([
                'message' => 'This commission cannot be marked as paid.',
                'alert-type' => 'danger'
            ]);
        }

        $commission->markAsPaid();

        return back()->with([
            'message' => 'Commission marked as paid!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Bulk approve commissions
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:referral_tracking,id'
        ]);

        $approved = $this->referralService->bulkApprove($validated['commission_ids'], auth()->id());

        return back()->with([
            'message' => "{$approved} commissions approved successfully!",
            'alert-type' => 'success'
        ]);
    }

    /**
     * Bulk mark as paid
     */
    public function bulkMarkPaid(Request $request)
    {
        $validated = $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:referral_tracking,id'
        ]);

        $paid = $this->referralService->bulkMarkAsPaid($validated['commission_ids']);

        return back()->with([
            'message' => "{$paid} commissions marked as paid!",
            'alert-type' => 'success'
        ]);
    }

    /**
     * Export commissions to CSV
     */
    public function export(Request $request)
    {
        $query = ReferralTracking::with('referralAgent');

        // Apply filters
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($agentId = $request->get('agent_id')) {
            $query->where('referral_agent_id', $agentId);
        }
        if ($startDate = $request->get('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $commissions = $query->orderBy('created_at', 'desc')->get();

        $filename = 'commissions_export_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($commissions) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID',
                'Order ID',
                'Order Type',
                'Agent Name',
                'Agent Email',
                'Customer Name',
                'Customer Email',
                'Order Amount',
                'Commission Amount',
                'Commission Type',
                'Status',
                'Payment Status',
                'Created At',
                'Approved At',
                'Paid At',
            ]);

            // Data rows
            foreach ($commissions as $commission) {
                fputcsv($file, [
                    $commission->id,
                    $commission->order_id,
                    $commission->order_type,
                    $commission->referralAgent->name ?? 'N/A',
                    $commission->referralAgent->email ?? 'N/A',
                    $commission->customer_name,
                    $commission->customer_email,
                    $commission->order_amount,
                    $commission->commission_amount,
                    $commission->commission_type . ' (' . $commission->commission_value . ($commission->commission_type === 'percentage' ? '%' : ' AED') . ')',
                    ucfirst($commission->status),
                    ucfirst($commission->payment_status),
                    $commission->created_at->format('Y-m-d H:i:s'),
                    $commission->approved_at?->format('Y-m-d H:i:s') ?? '',
                    $commission->paid_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export agents report to CSV
     */
    public function exportAgents(Request $request)
    {
        $agents = ReferralAgent::withCount([
            'referralTracking as total_orders',
            'referralTracking as approved_orders' => function ($query) {
                $query->whereIn('status', ['approved', 'paid']);
            },
            'clicks as total_clicks'
        ])->get();

        $filename = 'agents_report_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($agents) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Referral Code',
                'Commission Type',
                'Commission Value',
                'Status',
                'Total Clicks',
                'Total Orders',
                'Approved Orders',
                'Total Earnings',
                'Pending Earnings',
                'Paid Earnings',
                'Conversion Rate',
                'Created At',
            ]);

            // Data rows
            foreach ($agents as $agent) {
                fputcsv($file, [
                    $agent->id,
                    $agent->name,
                    $agent->email,
                    $agent->referral_code,
                    $agent->commission_type,
                    $agent->commission_value . ($agent->commission_type === 'percentage' ? '%' : ' AED'),
                    ucfirst($agent->status),
                    $agent->total_clicks,
                    $agent->total_orders,
                    $agent->approved_orders,
                    $agent->total_earnings,
                    $agent->pending_earnings,
                    $agent->paid_earnings,
                    $agent->conversion_rate . '%',
                    $agent->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
