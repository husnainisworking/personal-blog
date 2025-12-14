<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    /*
     * Show the 2FA verification page
     * This method runs when user visits /2fa/verify
     */
    public function show()
    {
        // Check if user is logged in
        // If not logged in, redirect them back to login page
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // User is logged in, show the 2FA verification form
        return view('auth.two-factor');
    }
    /*
     * Verify the 2FA code that user entered.
     * This method runs when user submits the 2FA form
     */

    public function verify(Request $request)
    {
        // Validate the input
        // Make sure 'code' field exists, is required, and has exactly 6 digits
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        // Rate Limiting: max 5 attempts per minute per IP
        // Example : Creating a unique key using the user's IP address.
        // verify-2fa-192.168.1.10
        $key = 'verify-2fa-'. $request->ip();

        if(RateLimiter::tooManyAttempts($key, 5)){
            // checks if this IP tried more than 5 times in 1 minute.
            $seconds = RateLimiter::avaiableIn($key);
            // tells how many seconds until they can try again.
            throw ValidationException::withMessages([
                // stops execution and shows an error message
               'code' => "Too many attempts. PLease try again in {$seconds} seconds." ,
            ]);
        }

        // Get the currently logged-in user from the session
        $user = Auth::user();

        // Safety check: if somehow user is not logged in, redirect to login
        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['code' => 'Please login first.']);
        }

        // Check TWO things:
        // 1. Does the entered code match the code we saved in database?
        // 2. Is the code still valid (not expired)?
        if ($user->two_factor_code && 
	 $request->code &&
	    $user->two_factor_code == $request->code &&

            now()->lessThan($user->two_factor_expires_at)) {

            RateLimiter::clear($key);

            // SUCCESS! Code is correct and not expired

            // Clear the 2FA code from database (so it can't be reused)
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
            $user->save();

            //User is already logged in from the first step
            //Just redirect them to the dashboard
            return redirect()->route('dashboard');
        }

        //FAILURE - Increment attempt counter
        // Add 1 failed attempt, block for 60 seconds after 5 failures.
        RateLimiter::hit($key, 60); //Block for 60 seconds after 5 failures

        // Failure ! Code is wrong or expired

        // Log them out for security (they have to start over)
        Auth::logout();

        // Redirect back to login with error message
        return redirect()->route('login')->withErrors([
            'email' => 'Invalid or expired verification code.',
        ]);
    }

    /**
     *  Resend the code with rate limiting
     */
    public function resend(Request $request)
    {
        // Rate limiting: max 3 resends per 5 minutes
        $key = 'resend-2fa-' . $request->ip();

        if(RateLimiter::tooManyAttempts($key, 3)) {
            $minutes = ceil(RateLimiter::availableIn($key, 3) /60);

            return back()->withErrors([
               'code' => "Too many resend attempts. Please wait {$minutes} minutes.",
            ]);
        }

        //Get the logged-in user
        $user = Auth::user();

        if(!$user) {
            return redirect()->route('login');
        }

        //Generate a new code
        $code = rand(100000, 999999);

        // Save to database with new expiry time
        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        //Send new code via email
        Mail::to($user->email)->queue(new TwoFactorCodeMail($code));

        // Increment resend counter
        RateLimiter::hit($key, 300); // 300 seconds = 5 minutes

        //Redirect back with success message
        return back()->with('status', 'New verification code sent to your email!');
    }
}
























