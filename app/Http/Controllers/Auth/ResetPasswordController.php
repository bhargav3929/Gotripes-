<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\PasswordChanged;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = RouteServiceProvider::HOME;

    /** Password rules shared with the in-portal change-password form. */
    public static function passwordRule(): Password
    {
        return Password::min(8)->letters()->numbers();
    }

    protected function rules()
    {
        return [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => ['required', 'confirmed', self::passwordRule()],
        ];
    }

    /**
     * Framework behaviour plus: every other session of this account is
     * signed out, the account holder is told by email that the password
     * changed, and the event is logged.
     */
    protected function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));

        $this->guard()->login($user);
        Auth::logoutOtherDevices($password);

        try {
            $user->notify(new PasswordChanged(request()->ip()));
        } catch (\Throwable $e) {
            report($e);
        }

        Log::info('password.reset.completed', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'ip'      => request()->ip(),
            'host'    => request()->getHost(),
        ]);
    }

    /**
     * The reset signs the person in, so land them where they work: tenant
     * managers on their dashboard, customer care on the support queue,
     * customers on the storefront, admins where they went before.
     */
    public function redirectPath()
    {
        $user = auth()->user();
        if (!$user) {
            return $this->redirectTo;
        }

        return match ($user->role) {
            'company_owner', 'company_admin' => route('manager.dashboard'),
            'customer_care'                  => route('manager.support.index'),
            'customer'                       => url('/'),
            default                          => $this->redirectTo,
        };
    }
}
