<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $user = Auth::user();

        Log::info('=== 2FA Process Started ===');
        Log::info('User email: ' . $user->email);

        $code = rand(100000, 999999);
        Log::info('Generated code: ' . $code);

        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();
        Log::info('Code saved to database');

        Log::info('Attempting to send email to: ' . $user->email);
        try {
            Mail::to($user->email)->queue(new TwoFactorCodeMail($code));
            Log::info('✅ Email queued successfully!');
            
            // Track in Sentry
            \Sentry\captureMessage('2FA email queued for: ' . $user->email, \Sentry\Severity::info());
        } catch (Exception $e) {
            Log::error('❌ Email FAILED: ' . $e->getMessage());
            Log::error('Exception: ' . $e);
            
            // Report to Sentry
            \Sentry\captureException($e);
        }

        Log::info('=== Redirecting to 2FA page ===');
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
