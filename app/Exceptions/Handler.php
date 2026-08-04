<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // A stale CSRF token used to surface to customers as a bare
        // "CSRF token mismatch" / 419 page part-way through a visa application,
        // with their form data gone and no idea what to do. The visa form is
        // long — several applicants, passport scans — so a session can lapse
        // while it is being filled in.
        //
        // AJAX submits (the visa and eSIM forms both post this way) now get a
        // JSON body carrying a fresh token, so the page can retry silently
        // instead of dead-ending. Normal page posts bounce back to the form
        // with an explanation rather than a stack-trace page.
        //
        // Matched on the 419 HttpException rather than TokenMismatchException:
        // Laravel 10 runs prepareException() before the renderable callbacks,
        // so by this point the token exception has already been converted and a
        // TokenMismatchException type hint would never fire.
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'   => false,
                    'csrf'      => true,
                    'csrf_token' => csrf_token(),
                    'message'   => 'Your session timed out. Please try again — your details are still on the page.',
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except($this->dontFlash))
                ->with('error', 'Your session timed out before the form was submitted. Please check your details and submit again.');
        });
    }
}
