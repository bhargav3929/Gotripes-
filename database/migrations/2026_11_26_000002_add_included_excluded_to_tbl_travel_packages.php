<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * What the price covers and what it doesn't, one item per line, shown to
     * travellers as two lists. Packages made from a supplier form had both
     * pasted into the description as "Included:" / "Not included:" blocks;
     * those move into the new fields and the rest becomes paragraphs.
     */
    public function up(): void
    {
        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            $table->text('included')->nullable()->after('description');
            $table->text('excluded')->nullable()->after('included');
        });

        DB::table('tbl_travel_packages')
            ->where('package_type', 'wizard')
            ->where(fn ($q) => $q->where('description', 'like', "%Included:\n%")->orWhere('description', 'like', "%Not included:\n%"))
            ->orderBy('id')
            ->get(['id', 'description'])
            ->each(function ($p) {
                $text = str_replace("\r\n", "\n", (string) $p->description);
                if (preg_match('/<(p|br|div|ul|li|h\d)\b/i', $text)) {
                    return; // already edited as HTML in the manager: leave it
                }
                $blocks = preg_split("/\n{2,}(?=(?:Included|Not included):\n)/", $text);
                $included = $excluded = null;
                $rest = [];
                foreach ($blocks as $b) {
                    if (str_starts_with($b, "Included:\n")) {
                        $included = trim(substr($b, strlen("Included:\n")));
                    } elseif (str_starts_with($b, "Not included:\n")) {
                        $excluded = trim(substr($b, strlen("Not included:\n")));
                    } else {
                        $rest[] = $b;
                    }
                }
                DB::table('tbl_travel_packages')->where('id', $p->id)->update([
                    'included' => $included,
                    'excluded' => $excluded,
                    'description' => \App\Models\TravelPackage::textToHtml(implode("\n\n", $rest)),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            $table->dropColumn(['included', 'excluded']);
        });
    }
};
