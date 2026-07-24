<?php

namespace App\Http\Controllers;

use App\Services\PassportOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PassportOcrController extends Controller
{
    public function __construct(private PassportOcrService $ocr)
    {
    }

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
        // Validated by hand rather than with the `image` rule: that rule leans on
        // getimagesize(), which does not understand HEIC, so an iPhone photo would
        // be rejected as "not an image" before we ever get the chance to convert it.
        // Messages are returned as JSON `message` because that is the only field the
        // front-end reads — Laravel's default 422 body would surface as the generic
        // "could not read the passport" text and hide the real reason.
        $validator = Validator::make($request->all(), [
            'passport' => 'required|file|max:' . PassportOcrService::MAX_UPLOAD_KB,
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

        if (!$this->ocr->isConfigured()) {
            return response()->json(['success' => false, 'message' => 'OCR is not configured.'], 500);
        }

        [$fields, $error] = $this->ocr->extractFromUpload($request->file('passport'));

        if ($error !== null) {
            return response()->json(['success' => false, 'message' => $error], 422);
        }

        return response()->json(['success' => true, 'fields' => $fields]);
    }
}
