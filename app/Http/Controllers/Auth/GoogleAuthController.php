<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['hd' => 'staff.msu.ac.zw'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google and authenticate registered staff.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            Log::warning('Google OAuth callback error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Google authentication failed or was cancelled. Please try again.');
        }

        $email = strtolower(trim((string) $googleUser->getEmail()));

        // 1. Enforce domain restriction: only @staff.msu.ac.zw emails allowed
        if (!str_ends_with($email, '@staff.msu.ac.zw')) {
            return redirect()->route('login')->with(
                'error',
                'Access restricted: Only official staff Google accounts (@staff.msu.ac.zw) are permitted to access this system. Provided: ' . $email
            );
        }

        // 2. Enforce pre-registered staff rule: user must already exist in database
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('login')->with(
                'error',
                'Access denied: The staff account (' . $email . ') is not registered in the Internal Audit Management System. Please contact the administrator.'
            );
        }

        // 3. Link Google account and update avatar if available
        $user->google_id = $googleUser->getId();
        if ($googleUser->getAvatar()) {
            $user->avatar = $googleUser->getAvatar();
        }
        $user->save();

        // 4. Log in user and regenerate session
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
