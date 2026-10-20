<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\TenantResetPassword;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Shown for every outcome (sent, unknown email, throttled), so the form
     * cannot be used to find out which emails have an account.
     */
    public const GENERIC_STATUS = 'If an account exists for that email, a reset link is on its way. Check your spam folder if it has not arrived in a few minutes.';

    public function __construct()
    {
        // Five requests a minute per IP on top of the per-email throttle the
        // broker already applies (auth.passwords.users.throttle).
        $this->middleware('throttle:5,1')->only('sendResetLinkEmail');
    }

    /**
     * Same flow as the framework trait, except: the email is handed to the
     * mailer after the response has gone out (the SMTP round trip no longer
     * holds the page), every outcome gets the same neutral message, and the
     * request is written to the log for auditing.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $status = $this->broker()->sendResetLink(
            $this->credentials($request),
            function ($user, $token) use ($request) {
                $ip = $request->ip();
                app()->terminating(function () use ($user, $token, $ip) {
                    try {
                        $user->notify(new TenantResetPassword($token, $ip));
                    } catch (\Throwable $e) {
                        report($e);
                    }
                });

                return Password::RESET_LINK_SENT;
            }
        );

        Log::info('password.reset.requested', [
            'email'  => $request->input('email'),
            'ip'     => $request->ip(),
            'host'   => $request->getHost(),
            'result' => $status,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => self::GENERIC_STATUS]);
        }

        return back()->with('status', self::GENERIC_STATUS);
    }
}
