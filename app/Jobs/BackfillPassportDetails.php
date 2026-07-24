<?php

namespace App\Jobs;

use App\Scopes\CompanyScope;
use App\Services\PassportOcrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Fill an application's passport columns from its uploaded passport copy.
 *
 * The visa forms no longer ask customers to type passport details — the browser
 * scans the passport as it is uploaded and posts the result in hidden fields.
 * That covers the common case, but not every one: the customer may upload a PDF,
 * the scan may fail or still be running when they hit submit, or they may be on
 * a browser where the fetch never completed. This job closes that gap on the
 * server so the operations team is not left with a blank applicant row.
 *
 * Dispatched with `afterResponse()`, so checkout is never held up waiting on OCR.
 * It only ever writes columns that are currently blank — a value the customer or
 * the browser scan already supplied always wins.
 */
class BackfillPassportDetails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    /**
     * @param  class-string  $modelClass  Application model to update.
     * @param  int|string    $modelId     Primary key of the row.
     * @param  string        $path        Stored path of the passport copy.
     * @param  array<string, string> $columns  Canonical OCR key => table column.
     * @param  string        $disk        Disk the passport copy lives on.
     */
    public function __construct(
        private string $modelClass,
        private int|string $modelId,
        private string $path,
        private array $columns,
        private string $disk = 'public',
    ) {
    }

    public function handle(PassportOcrService $ocr): void
    {
        if (!$ocr->isConfigured() || $this->path === '' || $this->columns === []) {
            return;
        }

        // Look the row up by primary key with the tenant scope lifted. That scope
        // fails closed when there is no tenant context — which is exactly the
        // situation a job runs in — so leaving it on would silently find nothing
        // and the backfill would never write anything. Addressing one known id is
        // not a cross-tenant read: the id came from the request that created it.
        /** @var \Illuminate\Database\Eloquent\Model|null $record */
        $record = $this->modelClass::query()
            ->withoutGlobalScope(CompanyScope::class)
            ->find($this->modelId);

        if (!$record) {
            return;
        }

        // Nothing to gain if the browser scan already filled everything in.
        if ($this->blankColumns($record) === []) {
            return;
        }

        [$fields, $error] = $ocr->extractFromStoredPath($this->path, $this->disk);

        if ($error !== null) {
            // Expected for PDF copies and unreadable photos — the passport file is
            // still on record for an agent to read, so this is info, not an error.
            Log::info('Passport backfill skipped', [
                'model'  => $this->modelClass,
                'id'     => $this->modelId,
                'reason' => $error,
            ]);
            return;
        }

        $values = $this->mapFields($fields);
        $updates = [];

        foreach ($this->blankColumns($record) as $key => $column) {
            if (!empty($values[$key])) {
                $updates[$column] = $values[$key];
            }
        }

        if ($updates === []) {
            return;
        }

        $record->forceFill($updates)->save();

        Log::info('Passport details backfilled from upload', [
            'model'   => $this->modelClass,
            'id'      => $this->modelId,
            'columns' => array_keys($updates),
        ]);
    }

    /**
     * The subset of the configured columns that currently hold no value.
     *
     * @return array<string, string>
     */
    private function blankColumns(\Illuminate\Database\Eloquent\Model $record): array
    {
        return array_filter(
            $this->columns,
            fn (string $column) => blank($record->getAttribute($column))
        );
    }

    /**
     * Translate a raw OCR payload into storable column values.
     *
     * @return array<string, string|null>
     */
    private function mapFields(array $fields): array
    {
        [$first, $last] = PassportOcrService::splitName($fields);

        return [
            'first_name'      => $first ?: null,
            'last_name'       => $last ?: null,
            'passport_number' => trim((string) ($fields['passport_number'] ?? '')) ?: null,
            'nationality'     => trim((string) ($fields['nationality'] ?? '')) ?: null,
            'dob'             => PassportOcrService::normaliseDate($fields['date_of_birth'] ?? null),
            'passport_expiry' => PassportOcrService::normaliseDate($fields['date_of_expiry'] ?? null),
            'gender'          => PassportOcrService::normaliseGender($fields['sex'] ?? null),
        ];
    }
}
