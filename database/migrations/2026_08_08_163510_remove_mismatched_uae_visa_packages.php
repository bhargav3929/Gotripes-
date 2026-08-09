<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // A past reseed of tbl_emirates (UAEEmiratesSeeder truncates and reinserts,
        // which reassigns auto-increment IDs) left older uae_visa_packages rows
        // pointing at IDs that now belong to a *different* emirate — e.g. a package
        // literally named "Sharjah Entry Visa" ended up linked to Ajman. The visa
        // form correctly gates Sharjah-only behaviour (refund bank details, deposit)
        // on the actually-selected emirate, so anyone who picked the mismatched
        // emirate saw a "Sharjah"-labelled package with no bank fields and no
        // deposit line. Remove any package whose name names an emirate other than
        // the one it's actually linked to — a correctly-linked package already
        // exists for the real emirate in every case this produced.
        $emirates = DB::table('tbl_emirates')->pluck('emiratesName', 'emiratesID');

        $staleIds = DB::table('uae_visa_packages')
            ->select('id', 'name', 'emirates_id')
            ->get()
            ->filter(function ($package) use ($emirates) {
                $linkedName = $emirates[$package->emirates_id] ?? null;

                foreach ($emirates as $emirateName) {
                    if ($emirateName !== $linkedName && stripos($package->name, $emirateName) !== false) {
                        return true;
                    }
                }

                return false;
            })
            ->pluck('id');

        if ($staleIds->isNotEmpty()) {
            DB::table('uae_visa_prices')->whereIn('visa_package_id', $staleIds)->delete();
            DB::table('uae_visa_packages')->whereIn('id', $staleIds)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible — the removed rows were orphaned, mismatched data left
        // over from a prior reseed, not structural schema. Nothing to restore.
    }
};
