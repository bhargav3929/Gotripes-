<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Reads passport details out of an image with Groq's vision model.
 *
 * The visa forms no longer ask customers to type their passport details, so this
 * is the only place those values come from. It is used twice per application:
 * once from the browser (PassportOcrController, for instant pre-fill) and once
 * on the server (BackfillPassportDetails, for the uploads the browser scan
 * missed — PDFs, failed scans, slow connections that submitted early).
 */
class PassportOcrService
{
    /** Upload ceiling in kilobytes. Modern phone cameras routinely produce 8-12 MB. */
    public const MAX_UPLOAD_KB = 15360; // 15 MB

    /** Formats the vision model accepts directly. */
    private const SUPPORTED_MIMES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

    public function isConfigured(): bool
    {
        return !empty(config('groq.api_key'));
    }

    /**
     * Extract passport fields from a freshly uploaded file.
     *
     * @return array{0: ?array, 1: ?string} [fields, error] — exactly one is non-null.
     */
    public function extractFromUpload(UploadedFile $file): array
    {
        [$binary, $mime, $error] = $this->readImage(
            $file->getRealPath(),
            $file->getMimeType() ?: '',
            $file->getClientOriginalExtension()
        );

        if ($error !== null) {
            return [null, $error];
        }

        return $this->callVisionModel($binary, $mime);
    }

    /**
     * Extract passport fields from a file already stored on a disk.
     *
     * Used by the background backfill, where the only handle on the document is
     * the path persisted against the application row.
     *
     * @return array{0: ?array, 1: ?string} [fields, error]
     */
    public function extractFromStoredPath(string $path, string $disk = 'public'): array
    {
        $storage = Storage::disk($disk);

        if (!$storage->exists($path)) {
            return [null, 'Stored passport file is missing.'];
        }

        // A PDF is a valid passport *copy* but the vision model cannot read it.
        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf') {
            return [null, 'PDF passport copies cannot be scanned.'];
        }

        $fullPath = $storage->path($path);

        [$binary, $mime, $error] = $this->readImage(
            $fullPath,
            $storage->mimeType($path) ?: '',
            pathinfo($path, PATHINFO_EXTENSION)
        );

        if ($error !== null) {
            return [null, $error];
        }

        return $this->callVisionModel($binary, $mime);
    }

    /**
     * Send the image to Groq and return the parsed fields.
     *
     * @return array{0: ?array, 1: ?string} [fields, error]
     */
    private function callVisionModel(string $binary, string $mime): array
    {
        $apiKey = config('groq.api_key');
        if (empty($apiKey)) {
            return [null, 'OCR is not configured.'];
        }

        $prompt = <<<TXT
You are reading a passport. Extract the holder's details from the image.
Return ONLY a JSON object with exactly these keys (use an empty string if a value
is not visible):
{
  "full_name": "",
  "surname": "",
  "given_names": "",
  "passport_number": "",
  "nationality": "",
  "date_of_birth": "",
  "sex": "",
  "place_of_birth": "",
  "date_of_issue": "",
  "date_of_expiry": "",
  "issuing_country": ""
}
Use ISO date format YYYY-MM-DD for all dates. Do not add any text outside the JSON.
TXT;

        try {
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->post(rtrim(config('groq.base_url'), '/') . '/chat/completions', [
                    'model'       => config('groq.vision_model'),
                    'temperature' => 0,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [[
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            ['type' => 'image_url', 'image_url' => ['url' => "data:{$mime};base64," . base64_encode($binary)]],
                        ],
                    ]],
                ]);

            if (!$response->successful()) {
                Log::error('Groq passport OCR failed', ['status' => $response->status(), 'body' => $response->body()]);
                return [null, 'Could not read the passport. Please try a clearer photo.'];
            }

            $fields = $this->parseJson($response->json('choices.0.message.content', ''));

            if ($fields === null) {
                return [null, 'Could not read the passport details. Please try a clearer photo.'];
            }

            return [$fields, null];

        } catch (\Throwable $e) {
            Log::error('Passport OCR exception', ['error' => $e->getMessage()]);
            return [null, 'Passport scan service is unavailable.'];
        }
    }

    /**
     * Read a passport photo into raw bytes the vision model can accept.
     *
     * iPhones shoot HEIC. Safari normally transcodes to JPEG when a file input
     * uses a broad `accept`, but that does not always happen — files shared from
     * elsewhere, or picked through some in-app browsers, still arrive as HEIC. The
     * model cannot read HEIC, so convert it when the server is able to and
     * otherwise say plainly what to upload instead.
     *
     * @return array{0: ?string, 1: ?string, 2: ?string} [binary, mime, error]
     */
    private function readImage(string $path, string $mime, string $extension): array
    {
        $mime = strtolower($mime);
        $ext  = strtolower($extension);

        $isHeic = in_array($ext, ['heic', 'heif'], true)
            || str_contains($mime, 'heic')
            || str_contains($mime, 'heif');

        if ($isHeic) {
            $jpeg = $this->heicToJpeg($path);

            if ($jpeg === null) {
                // No HEIC support on this server — tell the customer what to do
                // rather than failing with a vague "could not read" message.
                return [null, null, 'Please upload a JPG or PNG image.'];
            }

            return [$jpeg, 'image/jpeg', null];
        }

        if (!in_array($mime, self::SUPPORTED_MIMES, true)) {
            return [null, null, 'Please upload a JPG or PNG image.'];
        }

        $binary = @file_get_contents($path);
        if ($binary === false || $binary === '') {
            return [null, null, 'That file could not be read. Please try another photo.'];
        }

        return [$binary, $mime, null];
    }

    /**
     * Convert HEIC/HEIF to JPEG, or return null when this server cannot.
     *
     * Requires Imagick built against libheif — GD cannot decode HEIC at all, and
     * plenty of shared hosts ship neither, hence the capability check rather than
     * an assumption.
     */
    private function heicToJpeg(string $path): ?string
    {
        if (!class_exists(\Imagick::class)) {
            return null;
        }

        try {
            if (empty(\Imagick::queryFormats('HEIC')) && empty(\Imagick::queryFormats('HEIF'))) {
                return null;
            }

            $img = new \Imagick($path);
            $img->setImageFormat('jpeg');
            $img->setImageCompressionQuality(90);
            // Strip EXIF so an orientation tag the model cannot honour is applied
            // to the pixels first, then discarded.
            $img->autoOrient();
            $img->stripImage();
            $blob = $img->getImageBlob();
            $img->clear();
            $img->destroy();

            return $blob ?: null;
        } catch (\Throwable $e) {
            Log::warning('HEIC conversion failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Defensively decode the model's JSON (handles stray markdown fences).
     */
    private function parseJson(string $content): ?array
    {
        $content = trim($content);
        $content = preg_replace('/^```(?:json)?|```$/m', '', $content);
        $data = json_decode(trim($content), true);
        if (is_array($data)) {
            return $data;
        }
        // Fallback: grab the first {...} block.
        if (preg_match('/\{.*\}/s', $content, $m)) {
            $data = json_decode($m[0], true);
            return is_array($data) ? $data : null;
        }
        return null;
    }

    /**
     * Split an OCR result into the first/last name pair the visa tables store.
     *
     * `given_names` and `surname` are preferred; `full_name` is only split when
     * the model could not separate them itself.
     *
     * @return array{0: string, 1: string} [first, last]
     */
    public static function splitName(array $fields): array
    {
        $first = trim((string) ($fields['given_names'] ?? ''));
        $last  = trim((string) ($fields['surname'] ?? ''));

        if ($first !== '' || $last !== '') {
            // A single-word legal name is valid; mirror it rather than fabricate.
            return [$first ?: $last, $last ?: $first];
        }

        return self::splitFullName((string) ($fields['full_name'] ?? ''));
    }

    /**
     * Split a free-text full name into first/last.
     *
     * @return array{0: string, 1: string} [first, last]
     */
    public static function splitFullName(string $fullName): array
    {
        $fullName = trim(preg_replace('/\s+/', ' ', $fullName));
        if ($fullName === '') {
            return ['', ''];
        }

        $parts = explode(' ', $fullName, 2);

        return [$parts[0], $parts[1] ?? $parts[0]];
    }

    /**
     * Normalise the model's free-text sex value to the stored Male/Female.
     */
    public static function normaliseGender(?string $sex): ?string
    {
        $sex = strtolower(trim((string) $sex));
        if ($sex === '') {
            return null;
        }
        if (str_starts_with($sex, 'm')) {
            return 'Male';
        }
        if (str_starts_with($sex, 'f')) {
            return 'Female';
        }
        return null;
    }

    /**
     * Normalise a model-supplied date to Y-m-d, or null when it is unusable.
     *
     * The prompt asks for ISO dates but the model occasionally echoes the
     * passport's own format ("15 MAY 2030", "15/05/2030"), and storing those in a
     * date column throws.
     */
    public static function normaliseDate(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
