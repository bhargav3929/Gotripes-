<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\EsimOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from) : now()->subMonth();
        $to = $request->to ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        $ordersQuery = EsimOrder::whereBetween('created_at', [$from, $to]);
        $usersQuery = User::whereBetween('created_at', [$from, $to]);
        $companiesQuery = Company::whereBetween('created_at', [$from, $to]);

        if ($request->company_id) {
            $ordersQuery->where('company_id', $request->company_id);
            $usersQuery->where('company_id', $request->company_id);
        }

        $stats = [
            'total_revenue' => (clone $ordersQuery)->where('payment_status', 'paid')->sum('selling_price'),
            'total_orders' => (clone $ordersQuery)->count(),
            'new_users' => $usersQuery->count(),
            'new_companies' => $companiesQuery->count(),
        ];

        $revenueByCompany = Company::select('companies.id', 'companies.name')
            ->selectRaw('COALESCE(SUM(esim_orders.selling_price), 0) as revenue')
            ->selectRaw('COUNT(esim_orders.id) as orders_count')
            ->leftJoin('esim_orders', function ($join) use ($from, $to) {
                $join->on('companies.id', '=', 'esim_orders.company_id')
                     ->where('esim_orders.payment_status', '=', 'paid')
                     ->whereBetween('esim_orders.created_at', [$from, $to]);
            })
            ->groupBy('companies.id', 'companies.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $ordersByStatus = EsimOrder::whereBetween('created_at', [$from, $to])
            ->select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status')
            ->toArray();

        $monthlyTrend = EsimOrder::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN selling_price ELSE 0 END) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->whereBetween('created_at', [now()->subYear(), now()])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $companies = Company::orderBy('name')->get();

        return view('superadmin.reports.index', compact(
            'stats', 'revenueByCompany', 'ordersByStatus', 'monthlyTrend', 'companies'
        ));
    }

    public function export(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from) : now()->subMonth();
        $to = $request->to ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        $orders = EsimOrder::with('company', 'user')
            ->whereBetween('created_at', [$from, $to])
            ->when($request->company_id, fn($q) => $q->where('company_id', $request->company_id))
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'orders_report_' . $from->format('Y-m-d') . '_to_' . $to->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order ID', 'Company', 'Customer', 'Email', 'Amount', 'Status', 'Date']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->company->name ?? '-',
                    $order->user->name ?? $order->customer_name ?? '-',
                    $order->user->email ?? $order->customer_email ?? '-',
                    $order->selling_price,
                    $order->payment_status,
                    $order->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
