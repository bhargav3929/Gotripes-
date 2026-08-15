<?php

namespace App\Services;

use App\Models\B2bPartner;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

/**
 * Renders the national or international partner contract (currently
 * placeholder/sample content — see resources/views/partners/contracts) to a
 * PDF stamped with the partner's signature block, and stores it.
 */
class B2bPartnerContractService
{
    public function generate(B2bPartner $partner): string
    {
        $view = $partner->contract_type === 'national'
            ? 'partners.contracts.national'
            : 'partners.contracts.international';

        $pdf = Pdf::loadView($view, ['partner' => $partner]);

        $path = 'partners/contracts/' . $partner->id . '-' . now()->timestamp . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
