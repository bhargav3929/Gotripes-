<?php

namespace App\Console\Commands;

use App\Services\CommissionService;
use Illuminate\Console\Command;

class ReleaseEligibleCommissions extends Command
{
    protected $signature = 'commissions:release';
    protected $description = 'Flip pending commissions to available once their hold period elapses';

    public function handle(CommissionService $commissions): int
    {
        $released = $commissions->releaseDue();

        $this->info("Released {$released} commission row(s) to 'available'.");
        return self::SUCCESS;
    }
}
