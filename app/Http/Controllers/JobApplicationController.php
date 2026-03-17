<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Log;
use App\Mail\JobApplicationMail;
use App\Models\LFJprofile;

class JobApplicationController extends Controller
{
    /**
     * Handle Job Application form submission
     */
    public function submit(Request $request)
    {
        // Log::info('Job Application form submission started', [
        //     'timestamp' => now()->toDateTimeString(),
        //     'ip' => $request->ip(),
        //     'input' => $request->all()
        // ]);

        // Validate inputs
        $validated = $request->validate([
        'job_status'           => 'required|string', // This was 'status' before, but log shows 'job_status'
        'location_status'      => 'required|string|max:100', // ADD THIS NEW VALIDATION
        'name'                 => 'required|string|max:255',
        'age'                  => 'required|numeric',
        'nationality'          => 'required|string|max:100',
        'mobile'               => 'required|string|max:20',
        'email'                => 'required|email',
        'profession'           => 'required|string|max:255',
        'experience'           => 'required|numeric',
        'visa_status'          => 'required|string|max:255',
        'expected_salary'      => 'required|string|max:255',
        'last_company'         => 'required|string|max:255',
        'last_location'        => 'required|string|max:255',
        'preferred_location'   => 'required|string|max:255',
        'notice_period'        => 'required|string|max:255',
        'reference_name'       => 'required|string|max:255',
        'reference_position'   => 'required|string|max:255',
        'reference_mobile'     => 'required|string|max:20',
        'resume'               => 'required|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:2048',
        'passport'             => 'required|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:2048',
    ]);

        // Log::info('Validated job application input', [
        //     'validated' => $validated,
        //     'timestamp' => now()->toDateTimeString()
        // ]);

        $fromEmail = config('mail.from.address');
        $toEmail = config('mail.from.address');
        $userEmail = $validated['email'];

        // $additionalEmail = 'Saideepak.c@vizcheck.com';
        $recipients = array_unique([$toEmail, $userEmail]);

        if (empty($toEmail)) {
            // Log::error('MAIL_USERNAME is not set in config/mail.php or .env');
            $toEmail = 'amer@aynalamirtourism.com'; // fallback
            $recipients = [$toEmail];
        }

        // Store resume file if uploaded
        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
            // Log::info('Resume file uploaded', [
            //     'resume_path' => $resumePath,
            //     'timestamp' => now()->toDateTimeString(),
            // ]);
        }

        // Handle Passport file upload
        $passportPath = null;
        if ($request->hasFile('passport')) {
            $passportPath = $request->file('passport')->store('passports', 'public');
            // Log::info('Passport file uploaded', [
            //     'passport_path' => $passportPath,
            //     'timestamp' => now()->toDateTimeString(),
            // ]);
        }

        // Store the form in the DB (each field in its respective column)
        $LFJprofile = new LFJprofile();

    $LFJprofile->LFJCreatedBy = $request->input('name');
    $LFJprofile->LFJCreatedDate = now();
    $LFJprofile->LFJisActive = true;
    $LFJprofile->LFJModifiedBy = null;
    $LFJprofile->LFJModifiedDate = null;

    $LFJprofile->LFJStatus = $request->input('job_status'); // Updated from 'status' to 'job_status'
    $LFJprofile->LFJLocationStatus = $request->input('location_status'); // ADD THIS NEW ASSIGNMENT
    $LFJprofile->LFJName = $request->input('name');
    $LFJprofile->LFJAge = $request->input('age');
    $LFJprofile->LFJNationality = $request->input('nationality');
    $LFJprofile->LFJMobile = $request->input('mobile');
    $LFJprofile->LFJEmail = $request->input('email');
    $LFJprofile->LFJProfession = $request->input('profession');
    $LFJprofile->LFJExperience = $request->input('experience');
    $LFJprofile->LFJVisaStatus = $request->input('visa_status');
    $LFJprofile->LFJExpectedSalary = $request->input('expected_salary');
    $LFJprofile->LFJLastCompany = $request->input('last_company');
    $LFJprofile->LFJLastLocation = $request->input('last_location');
    $LFJprofile->LFJPreferredLocation = $request->input('preferred_location');
    $LFJprofile->LFJNoticePeriod = $request->input('notice_period');
    $LFJprofile->LFJReferenceName = $request->input('reference_name');
    $LFJprofile->LFJReferencePosition = $request->input('reference_position');
    $LFJprofile->LFJReferenceMobile = $request->input('reference_mobile');
    $LFJprofile->LFJResume = $resumePath;
    $LFJprofile->LFJPassport = $passportPath;

    $LFJprofile->save();

        // Prepare data for Mailable
        $data = $validated;
        $data['resume_path'] = $resumePath;
        $data['passport_path'] = $passportPath;

        try {
        // Send email to applicant (with thank you message, no buttons)
        Mail::to($userEmail)->send(new JobApplicationMail($data, $fromEmail, true));

        // Send email to admins (with buttons and full details)
        $adminRecipients = array_unique([$toEmail]);
        Mail::to($adminRecipients)->send(new JobApplicationMail($data, $fromEmail, false));

        // Log::info('Job Application emails sent successfully', [
        //     'applicant' => $userEmail,
        //     'admins' => $adminRecipients,
        //     'timestamp' => now()->toDateTimeString()
        // ]);

        return redirect()->back()->with('success', 'Your job application has been sent successfully!');
    } catch (\Exception $e) {
            // Log::error('Failed to send Job Application email', [
            //     'to'        => $recipients,
            //     'from'      => $data['email'],
            //     'error'     => $e->getMessage(),
            //     'timestamp' => now()->toDateTimeString(),
            //     'data'      => $data
            // ]);

            return redirect()->back()->with('error', 'Failed to send your job application. Please try again later.');
        }
    }
}
