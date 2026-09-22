<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ContractDocument;
use App\Services\AgentContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Load the B2B contract into the portal, so agents sign it at registration and
 * a manager never needs a developer to change the wording (asked for 22 Sep
 * 2026). Publishing writes a new version rather than editing the old one:
 * whoever signed version 2 stays bound to version 2.
 *
 * Owner/admin only — a customer_care login never reaches this group anyway,
 * but the guard is explicit, as on the support settings.
 */
class ManagerContractsController extends Controller
{
    public function __construct(private AgentContractService $contracts)
    {
    }

    public function index()
    {
        $this->authorizeAdmin();

        $versions = ContractDocument::where('audience', ContractDocument::AUDIENCE_AGENT)
            ->with('author')
            ->orderByDesc('version')
            ->get();

        return view('manager.contracts.index', [
            'versions' => $versions,
            'current'  => $versions->firstWhere('is_current', true),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'title'  => 'required|string|max:200',
            'source' => 'required|in:text,upload',
            'body'   => 'required_if:source,text|nullable|string|max:200000',
            'file'   => 'required_if:source,upload|nullable|file|mimes:pdf|max:10240',
            'make_current' => 'nullable|boolean',
        ], [
            'body.required_if' => 'Paste the contract text, or switch to uploading a PDF.',
            'file.required_if' => 'Choose the contract PDF, or switch to pasting the text.',
            'file.mimes'       => 'The contract must be a PDF.',
        ]);

        $isUpload = $validated['source'] === ContractDocument::SOURCE_UPLOAD;

        $contract = new ContractDocument([
            'audience'  => ContractDocument::AUDIENCE_AGENT,
            'title'     => $validated['title'],
            'version'   => $this->nextVersion(),
            'source'    => $validated['source'],
            'body'      => $isUpload ? null : $validated['body'],
            'pdf_path'  => $isUpload ? $request->file('file')->store('contracts', 'public') : null,
            'file_name' => $isUpload ? $request->file('file')->getClientOriginalName() : null,
            'created_by' => Auth::id(),
        ]);
        $contract->save();

        // Hash what a signer will actually be shown, now that it is stored.
        $contract->update(['content_hash' => $this->contracts->hashFor($contract)]);

        if ($request->boolean('make_current', true)) {
            $contract->makeCurrent();
        }

        return redirect()->route('manager.contracts.index')
            ->with('success', "Version {$contract->version} saved" .
                ($contract->is_current ? ' and is now the contract agents sign.' : '.'));
    }

    /** Switch which published version new applicants are asked to sign. */
    public function activate(ContractDocument $contract)
    {
        $this->authorizeAdmin();
        $contract->makeCurrent();

        return back()->with('success', "Version {$contract->version} is now the contract agents sign.");
    }

    /** Stop asking for a signature at all (back to the pre-contract behaviour). */
    public function deactivate(ContractDocument $contract)
    {
        $this->authorizeAdmin();
        $contract->update(['is_current' => false]);

        return back()->with('success', 'Registration will not ask for a signature until you publish a version.');
    }

    public function show(ContractDocument $contract)
    {
        $this->authorizeAdmin();

        if ($contract->isUpload()) {
            abort_unless($contract->pdf_path && Storage::disk('public')->exists($contract->pdf_path), 404);

            return response()->file(Storage::disk('public')->path($contract->pdf_path));
        }

        return view('manager.contracts.show', compact('contract'));
    }

    public function destroy(ContractDocument $contract)
    {
        $this->authorizeAdmin();

        // A signed version is evidence — it stays, whatever the manager clicks.
        if ($contract->signatureCount() > 0) {
            return back()->withErrors([
                'contract' => 'Version ' . $contract->version . ' has been signed by ' .
                    $contract->signatureCount() . ' applicant(s), so it cannot be deleted.',
            ]);
        }

        $contract->deleteFile();
        $contract->delete();

        return back()->with('success', 'Draft version deleted.');
    }

    private function nextVersion(): int
    {
        return (int) ContractDocument::where('audience', ContractDocument::AUDIENCE_AGENT)->max('version') + 1;
    }

    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        abort_unless($user && ($user->is_super_admin || $user->isSuperAdmin() || $user->isCompanyAdmin()), 403);
    }
}
