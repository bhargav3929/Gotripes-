<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Until now, Company::hasFeature() returned TRUE on an empty/null
     * features array (security hole — see audit finding #12). The fix is to
     * make it fail-closed.
     *
     * To avoid locking out existing companies that have NULL features (and
     * were implicitly enjoying full access), backfill them with every
     * available feature explicitly.
     *
     * After this migration, "no access" must be expressed as an empty array.
     * NULL or missing column never grants access.
     */
    public function up(): void
    {
        $allFeatures = array_keys(Company::AVAILABLE_FEATURES);

        $touched = DB::table('companies')
            ->whereNull('features')
            ->orWhere('features', '')
            ->orWhere('features', '[]')
            ->update(['features' => json_encode($allFeatures)]);

        if ($touched > 0) {
            echo "  backfilled features on {$touched} company rows (preserved full-access)\n";
        }
    }

    public function down(): void
    {
        // No-op — we cannot tell which rows were originally null vs explicitly
        // populated. Leaving the data as-is on rollback is safer.
    }
};
