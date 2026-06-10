<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\EsimOrder;
use Illuminate\Http\Request;

/**
 * Read-only eSIM order list for agents granted the `esim` service.
 * Tenant scoping comes from BelongsToCompany on EsimOrder.
 */
class AgentEsimController extends Controller
{
    public function index(Request $request)
    {
        $orders = EsimOrder::query()
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_email', 'like', "%{$s}%")
                  ->orWhere('order_reference', 'like', "%{$s}%");
            }))
            ->when($request->status, fn ($q, $s) => $q->where('payment_status', $s))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('agent.esim.index', compact('orders'));
    }
}
