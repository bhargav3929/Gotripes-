<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Mail\AgentApplicationDecisionMail;
use App\Models\AgentApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Manager-facing review queue for self-registered agent applications.
 * Tenant-scoped automatically via BelongsToCompany's global scope on
 * AgentApplication, same protection ManagerB2bPartnersController gets for
 * B2B partners.
 */
class ManagerAgentApplicationsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = AgentApplication::query();
        $filters = config('agent_application_filters', []);

        foreach ($filters as $key => $definition) {
            $value = trim((string) $request->query($key, ''));
            if ($value === '') {
                continue;
            }

            match ($definition['operator']) {
                'equals' => $query->where($definition['column'], $value),
                'json_contains' => $query->whereJsonContains($definition['column'], $value),
                'search_columns' => $query->where(function ($nested) use ($definition, $value) {
                    foreach ($definition['columns'] as $index => $column) {
                        $method = $index === 0 ? 'where' : 'orWhere';
                        $nested->{$method}($column, 'like', "%{$value}%");
                    }
                }),
                'license_status' => $this->applyLicenseFilter($query, $value),
                'user_active' => $value === 'active'
                    ? $query->whereHas('user', fn ($user) => $user->where('is_active', true))
                    : $query->where(function ($nested) {
                        $nested->whereNull('user_id')
                            ->orWhereHas('user', fn ($user) => $user->where('is_active', false));
                    }),
                default => null,
            };
        }

        $applications = $query->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        foreach ($filters as $key => &$definition) {
            $definition['value'] = (string) $request->query($key, '');
            if (($definition['options_source'] ?? null) === 'agent_services') {
                $definition['options'] = \App\Models\User::AGENT_SERVICES;
            } elseif (($definition['options_source'] ?? null) === 'countries') {
                $definition['options'] = AgentApplication::whereNotNull('country')
                    ->where('country', '<>', '')->distinct()->orderBy('country')->pluck('country', 'country')->all();
            }
        }
        unset($definition);

        return view('manager.agent-applications.index', compact('applications', 'status', 'filters'));
    }

    private function applyLicenseFilter($query, string $value)
    {
        return match ($value) {
            'valid' => $query->whereDate('trade_license_expiry_date', '>', now()->addDays(30)),
            'expiring' => $query->whereBetween('trade_license_expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()]),
            'expired' => $query->whereDate('trade_license_expiry_date', '<', now()->toDateString()),
            'missing' => $query->whereNull('trade_license_expiry_date'),
            default => $query,
        };
    }

    public function show(AgentApplication $application)
    {
        return view('manager.agent-applications.show', compact('application'));
    }

    public function approve(AgentApplication $application)
    {
        if (!$application->isPending()) {
            return redirect()->route('manager.agent-applications.show', $application->id)
                ->with('error', 'This application has already been reviewed.');
        }

        $application->approve(auth()->user());

        $this->sendDecisionMail($application, 'approved');

        return redirect()->route('manager.agent-applications.index')
            ->with('success', "\"{$application->name}\" approved. Their agent account is now active.");
    }

    public function reject(Request $request, AgentApplication $application)
    {
        if (!$application->isPending()) {
            return redirect()->route('manager.agent-applications.show', $application->id)
                ->with('error', 'This application has already been reviewed.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $application->reject(auth()->user(), $validated['reason']);

        $this->sendDecisionMail($application, 'rejected');

        return redirect()->route('manager.agent-applications.index')
            ->with('success', "\"{$application->name}\" rejected.");
    }

    private function sendDecisionMail(AgentApplication $application, string $decision): void
    {
        try {
            Mail::to($application->email)->send(new AgentApplicationDecisionMail($application, $decision));
        } catch (\Throwable $e) {
            Log::error('Agent application decision email failed', [
                'application_id' => $application->id,
                'decision' => $decision,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
