<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Prevent session fixation attacks
        $request->session()->regenerate();

        $user = Auth::user();

        Log::info('=== 2FA Process Started ===', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Generate 6-digit 2FA code
        $code = rand(100000, 999999);
        Log::info('Generated 2FA code', [
            'user_id' => $user->id,
            'code_length' => strlen((string) $code),
        ]);

        // Save code to database
        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        Log::info('2FA code saved to database', [
            'user_id' => $user->id,
            'expires_at' => $user->two_factor_expires_at,
        ]);

        // Attempt to send 2FA email

        try {
            Mail::to($user->email)->queue(new TwoFactorCodeMail($code));

            Log::info('✅ 2FA email queued successfully!', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            // Track in Sentry
            if (function_exists('\\Sentry\\captureMessage')) {
                \Sentry\captureMessage('2FA email queued for: '.$user->email, \Sentry\Severity::info());
            }

        } catch (TransportException $e) {
            // Specific: Mail transport/connection errors (SMTP, API failures)
            Log::error('❌ Mail transport error during 2FA email', [

                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            // Report to Sentry
            if (function_exists('\\Sentry\\captureException')) {
                \Sentry\captureException($e);
            }

            // User-friendly error message
            return back()->withErrors([
                'email' => 'Unable to send verification code. Please check your email settings or try again later.',
            ]);

        }

        Log::info('=== Redirecting to 2FA verification page ===', [
            'user_id' => $user->id,
        ]);

        return redirect()->route('2fa.show');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
