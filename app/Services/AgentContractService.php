<?php

namespace App\Services;

use App\Models\ContractDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Turns an applicant's on-screen agreement into a stored, dated PDF.
 *
 * Two shapes, because a manager may publish a contract either way:
 *  - pasted text  -> the contract itself is rendered, with the signature block
 *                    printed underneath it, so one file holds both.
 *  - uploaded PDF -> we cannot write inside someone else's PDF, so a one-page
 *                    signature certificate is generated instead. It names the
 *                    contract, its version and the SHA-256 of the exact file
 *                    that was shown, which is what ties the two together.
 *
 * Takes any signer model with `id`, `name` and `signature_*` columns —
 * `AgentApplication` (agent flow) and `ReferralAgent` (freelancer flow) both
 * qualify, since they share the same eSIM sub-agent contract (audience
 * 'agent'). The stored path is prefixed generically so both flows' files
 * live side by side without colliding.
 */
class AgentContractService
{
    public function sign(Model $application, ContractDocument $contract): ?string
    {
        $view = $contract->isUpload()
            ? 'contracts.signature-certificate'
            : 'contracts.signed-agreement';

        try {
            $pdf = Pdf::loadView($view, [
                'application' => $application,
                'contract'    => $contract,
            ]);

            $path = 'agents/contracts/' . $application->id . '-v' . $contract->version . '-' . now()->timestamp . '.pdf';
            Storage::disk('public')->put($path, $pdf->output());

            return $path;
        } catch (\Throwable $e) {
            // A failed render must not cost the applicant their registration:
            // the signature itself (name, IP, timestamp, version) is already
            // on the application row, which is the part that binds them.
            Log::error('Agent contract PDF failed', [
                'application_id' => $application->id,
                'contract_id'    => $contract->id,
                'error'          => $e->getMessage(),
            ]);

            return null;
        }
    }

    /** SHA-256 of what a signer is shown, stored with the version. */
    public function hashFor(ContractDocument $contract): ?string
    {
        if (!$contract->isUpload()) {
            return hash('sha256', (string) $contract->body);
        }

        if ($contract->pdf_path && Storage::disk('public')->exists($contract->pdf_path)) {
            return hash('sha256', Storage::disk('public')->get($contract->pdf_path));
        }

        return null;
    }
}
