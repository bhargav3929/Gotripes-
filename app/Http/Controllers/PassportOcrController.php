<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
    public function extract(Request $request)
    {
        $request->validate([
            'passport' => 'required|image|mimes:jpeg,png,jpg,webp|max:8192',
        ]);

        $apiKey = config('groq.api_key');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'OCR is not configured.'], 500);
        }

        $file = $request->file('passport');
        $mime = $file->getMimeType() ?: 'image/jpeg';
        $base64 = base64_encode(file_get_contents($file->getRealPath()));

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
