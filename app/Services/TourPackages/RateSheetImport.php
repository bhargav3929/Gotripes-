<?php

namespace App\Services\TourPackages;

/**
 * Reads a supplier rate sheet saved as CSV into rate rows.
 *
 * One row per duration and hotel tier, net prices per person for the whole
 * stay. Column names are matched loosely so a sheet exported from Excel
 * works as it is:
 *
 *   days | hotel_stars | adult_net | child_net | infant_net | single_supplement_net
 *   (also: "Days", "Duration", "Stars", "Hotel", "Adult", "Adult price", "Child",
 *    "Infant", "Single", "Single supplement", "SGL supp" ...)
 *
 * Comma- or semicolon-separated; "AED 1,250" style numbers are fine. If any
 * row is wrong nothing is imported and every problem is listed by line.
 */
class RateSheetImport
{
    private const COLUMNS = [
        'days'                  => ['days', 'day', 'duration', 'no of days', 'number of days', 'tour days'],
        'hotel_stars'           => ['hotel_stars', 'stars', 'star', 'hotel', 'hotel category', 'category', 'hotel star'],
        'adult_net'             => ['adult_net', 'adult', 'adults', 'adult price', 'adult rate', 'per adult', 'twin', 'per person'],
        'child_net'             => ['child_net', 'child', 'children', 'child price', 'child rate', 'cwb', 'child with bed'],
        'infant_net'            => ['infant_net', 'infant', 'infants', 'infant price', 'infant rate'],
        'single_supplement_net' => ['single_supplement_net', 'single', 'single supplement', 'sgl', 'sgl supp', 'single supp', 'sgl supplement'],
    ];

    /**
     * @return array{rows: array<int, array>, errors: array<int, string>}
     */
    public static function parse(string $csv): array
    {
        $csv = preg_replace('/^\xEF\xBB\xBF/', '', $csv); // Excel's UTF-8 marker
        $lines = preg_split('/\r\n|\r|\n/', trim($csv));
        if (!$lines || trim($lines[0]) === '') {
            return ['rows' => [], 'errors' => ['The file is empty.']];
        }
        $delimiter = substr_count($lines[0], ';') > substr_count($lines[0], ',') ? ';' : ',';

        $header = array_map(fn ($h) => strtolower(trim(preg_replace('/\s+/', ' ', str_replace(['_', '-', '(', ')', '.'], ' ', (string) $h)))), str_getcsv($lines[0], $delimiter));
        $map = [];
        foreach (self::COLUMNS as $field => $aliases) {
            $aliases = array_map(fn ($a) => str_replace('_', ' ', $a), $aliases);
            foreach ($header as $i => $h) {
                // "Adult (AED)", "Adult net" and "adult_net" all count as "adult".
                $bare = trim(preg_replace('/\s+/', ' ', preg_replace('/\b(net|aed|usd|eur|per pax)\b/', '', $h)));
                if (in_array($h, $aliases, true) || in_array($bare, $aliases, true)) {
                    $map[$field] ??= $i;
                }
            }
        }
        $missing = array_diff(['days', 'hotel_stars', 'adult_net'], array_keys($map));
        if ($missing) {
            return ['rows' => [], 'errors' => ['The first row must name the columns. Missing: ' . implode(', ', $missing)
                . '. Expected: days, hotel_stars, adult_net, child_net, infant_net, single_supplement_net.']];
        }

        $rows = $errors = [];
        foreach (array_slice($lines, 1) as $n => $line) {
            $lineNo = $n + 2;
            if (trim(str_replace($delimiter, '', $line)) === '') {
                continue;
            }
            $cells = str_getcsv($line, $delimiter);
            $row = [];
            foreach ($map as $field => $i) {
                $raw = trim((string) ($cells[$i] ?? ''));
                $num = self::number($raw);
                if ($raw !== '' && $num === null) {
                    $errors[] = "Line {$lineNo}: \"{$raw}\" in {$field} is not a number.";
                }
                $row[$field] = $num;
            }
            if (($row['days'] ?? null) === null || $row['days'] < 1 || $row['days'] > 60 || floor($row['days']) != $row['days']) {
                $errors[] = "Line {$lineNo}: days must be a whole number from 1 to 60.";
            }
            if (($row['hotel_stars'] ?? null) === null || $row['hotel_stars'] < 1 || $row['hotel_stars'] > 5 || floor($row['hotel_stars']) != $row['hotel_stars']) {
                $errors[] = "Line {$lineNo}: hotel_stars must be 1 to 5.";
            }
            if (($row['adult_net'] ?? null) === null || $row['adult_net'] < 0) {
                $errors[] = "Line {$lineNo}: adult_net is required.";
            }
            foreach (['child_net', 'infant_net', 'single_supplement_net'] as $f) {
                $row[$f] = max(0.0, (float) ($row[$f] ?? 0));
            }
            $rows[] = $row;
        }
        if (!$rows && !$errors) {
            $errors[] = 'The sheet has a header row but no rates.';
        }
        return ['rows' => $errors ? [] : $rows, 'errors' => array_slice($errors, 0, 20)];
    }

    /** "AED 1,250.50" → 1250.5; "1.250,50" → 1250.5; "" → null. */
    private static function number(string $raw): ?float
    {
        $s = preg_replace('/[^\d.,\-]/', '', $raw);
        if ($s === '' || $s === '-') {
            return null;
        }
        if (str_contains($s, ',') && str_contains($s, '.')) {
            $s = strrpos($s, ',') > strrpos($s, '.') ? str_replace(['.', ','], ['', '.'], $s) : str_replace(',', '', $s);
        } elseif (str_contains($s, ',')) {
            $s = preg_match('/,\d{3}$/', $s) ? str_replace(',', '', $s) : str_replace(',', '.', $s);
        }
        return is_numeric($s) ? (float) $s : null;
    }
}
