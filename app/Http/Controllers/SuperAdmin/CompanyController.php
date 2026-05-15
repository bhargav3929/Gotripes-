<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SuperAdminAuditLog;
use App\Models\User;
use App\Services\HostingerSubdomainProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::with('owner');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('domain', 'like', "%{$search}%")
                  ->orWhere('subdomain', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        $companies = $query->latest()->paginate(20);

        return view('superadmin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('superadmin.companies.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'subdomain' => Company::normalizeSubdomain($request->input('subdomain'))
                ?? Company::normalizeSubdomain($request->input('slug'))
                ?? Company::normalizeSubdomain($request->input('name')),
            'slug' => $request->filled('slug') ? Str::slug($request->input('slug')) : Str::slug($request->input('name')),
            'phone' => $this->composePhone($request),
        ]);

        $allowedFeatures = array_keys(Company::AVAILABLE_FEATURES);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:companies,slug',
            'subdomain' => ['required', 'string', 'min:2', 'max:63', 'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', 'unique:companies,subdomain', 'not_in:'.implode(',', Company::RESERVED_SUBDOMAINS)],
            'domain' => 'nullable|string|max:255|unique:companies,domain',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'plan' => 'required|in:trial,basic,pro,enterprise',
            'type' => 'nullable|in:agency,freelancer,corporate',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'markup_percentage' => 'nullable|numeric|min:0|max:100',
            'commission_type' => 'nullable|in:percentage,flat',
            'commission_value' => 'nullable|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string|in:'.implode(',', $allowedFeatures),
            'auto_provision' => 'nullable|boolean',
            // Admin user
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ], [
            'subdomain.regex' => 'Subdomain must be lowercase letters, numbers, and hyphens only (no leading/trailing hyphen).',
            'subdomain.not_in' => 'That subdomain is reserved. Please choose another.',
        ]);

        // Normalize features to a clean array of allowed slugs.
        $features = isset($validated['features']) && is_array($validated['features'])
            ? array_values(array_intersect($allowedFeatures, $validated['features']))
            : $allowedFeatures; // default: full access

        // Create company
        $companyData = collect($validated)->except([
            'admin_name', 'admin_email', 'admin_password', 'logo', 'features', 'auto_provision'
        ])->toArray();
        $companyData['features'] = $features;
        $companyData['type'] = $validated['type'] ?? 'agency';

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $companyData['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        // Set trial end date if trial plan; otherwise give a 1-year subscription window
        if ($companyData['plan'] === 'trial') {
            $companyData['trial_ends_at'] = now()->addDays(14);
        } else {
            $companyData['subscription_ends_at'] = now()->addYear();
        }

        $company = Company::create($companyData);

        // Create admin user
        $admin = User::create([
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'company_id' => $company->id,
            'role' => 'company_owner',
        ]);

        SuperAdminAuditLog::log('company.create', $company, [
            'plan'         => $company->plan,
            'features'     => $features,
            'admin_email'  => $admin->email,
        ]);

        // Optionally provision the Hostinger subdomain right now.
        // Surface the actual outcome instead of always flashing 'success'.
        $provisionMessage = null;
        $provisionOk = true;
        if ($request->boolean('auto_provision', true)) {
            try {
                $result = app(HostingerSubdomainProvisioner::class)->provision($company);
                $provisionMessage = $result['message'];
                $provisionOk = (bool) ($result['ok'] ?? false);
                if (!$provisionOk) {
                    \Illuminate\Support\Facades\Log::warning('Subdomain provisioning returned !ok', [
                        'company_id' => $company->id,
                        'subdomain'  => $company->subdomain,
                        'step'       => $result['step'] ?? 'unknown',
                        'message'    => $provisionMessage,
                    ]);
                }
            } catch (\Throwable $e) {
                $provisionOk = false;
                $provisionMessage = 'Subdomain provisioning failed: ' . $e->getMessage()
                    . ' — you can retry from the company page.';
                \Illuminate\Support\Facades\Log::error('Subdomain provisioning threw exception', [
                    'company_id' => $company->id,
                    'subdomain'  => $company->subdomain,
                    'error'      => $e->getMessage(),
                    'trace'      => $e->getTraceAsString(),
                ]);
            }
        }

        $redirect = redirect()->route('superadmin.companies.show', $company)
            ->with('success', "Company '{$company->name}' created with admin user.");

        if ($provisionMessage) {
            // Use 'warning' for provisioning failure so it's visually distinct
            // from the green-success of the company creation itself.
            $redirect->with($provisionOk ? 'info' : 'warning', $provisionMessage);
        }

        return $redirect;
    }

    /**
     * Manually trigger subdomain provisioning for an existing company.
     */
    public function provisionSubdomain(Company $company)
    {
        $result = app(HostingerSubdomainProvisioner::class)->provision($company);
        $flash = $result['ok'] ? 'success' : 'error';
        return back()->with($flash, $result['message']);
    }

    public function show(Company $company)
    {
        $company->load(['owner', 'admins', 'users']);

        $stats = [
            'total_users' => $company->users()->count(),
            'total_orders' => $company->total_orders,
            'total_revenue' => $company->total_revenue,
            'referral_agents' => $company->referralAgents()->count(),
        ];

        return view('superadmin.companies.show', compact('company', 'stats'));
    }

    public function edit(Company $company)
    {
        return view('superadmin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        if ($request->filled('subdomain')) {
            $request->merge(['subdomain' => Company::normalizeSubdomain($request->input('subdomain'))]);
        }

        $request->merge(['phone' => $this->composePhone($request)]);

        $allowedFeatures = array_keys(Company::AVAILABLE_FEATURES);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:companies,slug,' . $company->id,
            'subdomain' => ['required', 'string', 'min:2', 'max:63', 'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', 'unique:companies,subdomain,' . $company->id, 'not_in:'.implode(',', Company::RESERVED_SUBDOMAINS)],
            'domain' => 'nullable|string|max:255|unique:companies,domain,' . $company->id,
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'plan' => 'required|in:trial,basic,pro,enterprise',
            'type' => 'nullable|in:agency,freelancer,corporate',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:512',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'bg_color' => 'nullable|string|max:7',
            'markup_percentage' => 'nullable|numeric|min:0|max:100',
            'commission_type' => 'nullable|in:percentage,flat',
            'commission_value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|in:'.implode(',', $allowedFeatures),
        ]);

        if (array_key_exists('features', $validated)) {
            $validated['features'] = is_array($validated['features'])
                ? array_values(array_intersect($allowedFeatures, $validated['features']))
                : [];
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($company->favicon) {
                Storage::disk('public')->delete($company->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('companies/favicons', 'public');
        }

        $original = $company->only(array_keys($validated));
        $company->update($validated);
        $diff = collect($validated)
            ->mapWithKeys(fn($v, $k) => [$k => ['from' => $original[$k] ?? null, 'to' => $v]])
            ->filter(fn($pair) => $pair['from'] !== $pair['to'])
            ->all();

        SuperAdminAuditLog::log('company.update', $company, $diff);

        return redirect()
            ->route('superadmin.companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        SuperAdminAuditLog::log('company.delete', $company, [
            'subdomain' => $company->subdomain,
            'plan'      => $company->plan,
        ]);

        // Delete logo and favicon
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }
        if ($company->favicon) {
            Storage::disk('public')->delete($company->favicon);
        }

        $company->delete();

        return redirect()
            ->route('superadmin.companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    public function toggleStatus(Company $company)
    {
        $was = (bool) $company->is_active;
        $company->update(['is_active' => !$was]);

        SuperAdminAuditLog::log('company.toggle_status', $company, [
            'is_active' => ['from' => $was, 'to' => !$was],
        ]);

        $status = $company->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Company {$status} successfully.");
    }

    public function impersonate(Company $company)
    {
        // Find a target user before mutating any state
        $admin = $company->owner ?? $company->admins()->first();

        if (!$admin) {
            return back()->with('error', 'No admin user found for this company.');
        }

        SuperAdminAuditLog::log('company.impersonate', $company, [
            'impersonated_user_id' => $admin->id,
            'impersonated_email'   => $admin->email,
        ]);

        // Remember the SA's original ID so they can return later
        session(['impersonating_from' => auth()->id()]);
        // NOTE: we do NOT write session('company_id'). Tenant identity is now
        // host-derived (see IdentifyTenant middleware). The session key is
        // ignored and would only confuse future readers.

        auth()->login($admin);

        return redirect()->route('manager.dashboard')
            ->with('info', "Impersonating {$company->name}");
    }

    public function extendSubscription(Request $request, Company $company)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $currentEnd = $company->subscription_ends_at ?? now();
        if ($currentEnd->isPast()) {
            $currentEnd = now();
        }

        $newEnd = $currentEnd->addDays($request->days);
        $company->update([
            'subscription_ends_at' => $newEnd,
        ]);

        SuperAdminAuditLog::log('company.extend_subscription', $company, [
            'days'     => (int) $request->days,
            'new_end'  => $newEnd->toDateTimeString(),
        ]);

        return back()->with('success', "Subscription extended by {$request->days} days.");
    }

    public function changePlan(Request $request, Company $company)
    {
        $request->validate([
            'plan' => 'required|in:trial,basic,pro,enterprise',
        ]);

        $oldPlan        = $company->plan;
        $oldFeatures    = $company->features;
        $oldCommission  = $company->commission_value;

        // Pull the defaults for the target plan. Falls back to plan name
        // only if config is missing.
        $planConfig = config("plans.{$request->plan}");

        $updates = ['plan' => $request->plan];
        if (is_array($planConfig)) {
            if (isset($planConfig['features']) && is_array($planConfig['features'])) {
                $updates['features'] = $planConfig['features'];
            }
            if (isset($planConfig['commission_pct'])) {
                $updates['commission_value'] = (float) $planConfig['commission_pct'];
                $updates['commission_type']  = 'percentage';
            }
        }

        $company->update($updates);

        SuperAdminAuditLog::log('company.change_plan', $company, [
            'plan'             => ['from' => $oldPlan,        'to' => $request->plan],
            'features'         => ['from' => $oldFeatures,    'to' => $updates['features']         ?? null],
            'commission_value' => ['from' => $oldCommission,  'to' => $updates['commission_value'] ?? null],
        ]);

        $msg = "Plan changed to {$request->plan}.";
        if (isset($planConfig['name'])) {
            $msg .= " Features and commission rate updated to {$planConfig['name']} defaults.";
        }
        return back()->with('success', $msg);
    }

    /**
     * Combine country dial code + local number into a single E.164-ish string
     * for storage in the `phone` column. Returns null when no number was entered,
     * so empty phones stay empty (not "+971").
     */
    private function composePhone(Request $request): ?string
    {
        $code   = trim((string) $request->input('phone_country_code', ''));
        $number = trim((string) $request->input('phone_number', ''));

        // Strip everything except digits and the leading + from the dial code.
        $code = $code !== '' ? '+' . preg_replace('/\D+/', '', $code) : '';
        // Keep digits, spaces, and dashes in the local number — drop the rest.
        $number = preg_replace('/[^\d\s\-]/', '', $number);
        $number = trim(preg_replace('/\s+/', ' ', $number));

        if ($number === '') {
            // Fall back to whatever was posted as `phone` (e.g. older clients) or null.
            $legacy = trim((string) $request->input('phone', ''));
            return $legacy !== '' ? $legacy : null;
        }

        return $code !== '+' && $code !== '' ? trim($code . ' ' . $number) : $number;
    }
}
