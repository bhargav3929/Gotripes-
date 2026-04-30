<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
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
        ]);

        $allowedFeatures = array_keys(Company::AVAILABLE_FEATURES);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:companies,slug',
            'subdomain' => ['required', 'string', 'min:2', 'max:63', 'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', 'unique:companies,subdomain', 'not_in:'.implode(',', Company::RESERVED_SUBDOMAINS)],
            'domain' => 'nullable|string|max:255|unique:companies,domain',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
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

        // Optionally provision the Hostinger subdomain right now.
        $provisionMessage = null;
        if ($request->boolean('auto_provision', true)) {
            try {
                $result = app(HostingerSubdomainProvisioner::class)->provision($company);
                $provisionMessage = $result['message'];
            } catch (\Throwable $e) {
                $provisionMessage = 'Subdomain provisioning failed: ' . $e->getMessage()
                    . ' — you can retry from the company page.';
            }
        }

        return redirect()
            ->route('superadmin.companies.show', $company)
            ->with('success', "Company '{$company->name}' created with admin user."
                . ($provisionMessage ? ' ' . $provisionMessage : ''));
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

        $company->update($validated);

        return redirect()
            ->route('superadmin.companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
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
        $company->update(['is_active' => !$company->is_active]);

        $status = $company->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Company {$status} successfully.");
    }

    public function impersonate(Company $company)
    {
        // Store original user ID to allow returning
        session(['impersonating_from' => auth()->id()]);
        session(['company_id' => $company->id]);

        // Get company owner or first admin
        $admin = $company->owner ?? $company->admins()->first();

        if ($admin) {
            auth()->login($admin);
            return redirect('/admin')->with('info', "Impersonating {$company->name}");
        }

        return back()->with('error', 'No admin user found for this company.');
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

        $company->update([
            'subscription_ends_at' => $currentEnd->addDays($request->days),
        ]);

        return back()->with('success', "Subscription extended by {$request->days} days.");
    }

    public function changePlan(Request $request, Company $company)
    {
        $request->validate([
            'plan' => 'required|in:trial,basic,pro,enterprise',
        ]);

        $company->update(['plan' => $request->plan]);

        return back()->with('success', "Plan changed to {$request->plan}.");
    }
}
