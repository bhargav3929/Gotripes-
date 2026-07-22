<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PassportOcrController extends Controller
{
    /**
     * Show the passport scan / pre-fill page.
     */
    public function show()
    {
        return view('passport-scan');
    }

    /**
     * Accept a passport image, send it to Groq's vision model, and return the
     * extracted passport fields as JSON for pre-filling a form.
     */
    /** Upload ceiling in kilobytes. Modern phone cameras routinely produce 8-12 MB. */
    private const MAX_UPLOAD_KB = 15360; // 15 MB

    public function extract(Request $request)
    {
        // Validated by hand rather than with the `image` rule: that rule leans on
        // getimagesize(), which does not understand HEIC, so an iPhone photo would
        // be rejected as "not an image" before we ever get the chance to convert it.
        // Messages are returned as JSON `message` because that is the only field the
        // front-end reads — Laravel's default 422 body would surface as the generic
        // "could not read the passport" text and hide the real reason.
        $validator = Validator::make($request->all(), [
            'passport' => 'required|file|max:' . self::MAX_UPLOAD_KB,
        ], [
            'passport.required' => 'Please choose a passport photo to scan.',
            'passport.file'     => 'Please upload a JPG or PNG image.',
            'passport.max'      => 'That image is too large. Please upload a photo under 15 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('passport'),
            ], 422);
        }

        $apiKey = config('groq.api_key');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'OCR is not configured.'], 500);
        }

        $file = $request->file('passport');

        [$binary, $mime, $error] = $this->readImage($file);
        if ($error !== null) {
            return response()->json(['success' => false, 'message' => $error], 422);
        }

        $base64 = base64_encode($binary);

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
                            ['type' => 'image_url', 'image_url' => ['url' => "data:{$mime};base64,{$base64}"]],
                        ],
                    ]],
                ]);

            if (!$response->successful()) {
                Log::error('Groq passport OCR failed', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Could not read the passport. Please try a clearer photo.',
                ], 502);
            }

            $content = $response->json('choices.0.message.content', '');
            $fields = $this->parseJson($content);

            if ($fields === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not read the passport details. Please try a clearer photo.',
                ], 422);
            }

            return response()->json(['success' => true, 'fields' => $fields]);

        } catch (\Throwable $e) {
            Log::error('Passport OCR exception', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Passport scan service is unavailable. Please enter details manually.',
            ], 500);
        }
    }

    /**
     * Read an uploaded passport photo into raw bytes the vision model can accept.
     *
     * iPhones shoot HEIC. Safari normally transcodes to JPEG when a file input
     * uses a broad `accept`, but that does not always happen — files shared from
     * elsewhere, or picked through some in-app browsers, still arrive as HEIC. The
     * model cannot read HEIC, so convert it when the server is able to and
     * otherwise say plainly what to upload instead.
     *
     * @return array{0: ?string, 1: ?string, 2: ?string} [binary, mime, error]
     */
    private function readImage(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $mime = strtolower($file->getMimeType() ?: '');
        $ext  = strtolower($file->getClientOriginalExtension());

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

        $supported = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($mime, $supported, true)) {
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
}
