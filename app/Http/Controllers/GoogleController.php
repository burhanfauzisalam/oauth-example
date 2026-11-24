<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Handle the callback from Google and authenticate the user.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            return redirect()->route('login')
                ->with('error', 'Sesi login kedaluwarsa. Silakan coba lagi.');
        }

        if ($googleUser === null) {
            return redirect()->route('login')
                ->with('error', 'Anda membatalkan login dengan Google.');
        }

        $user = User::firstOrCreate(
            ['google_id' => $googleUser->getId()],
            [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'email_verified_at' => now(),
                'avatar' => $googleUser->getAvatar(),
            ]
        );

        $wasRecentlyCreated = $user->wasRecentlyCreated;

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('status', $wasRecentlyCreated
                ? 'Akun Anda berhasil dibuat.'
                : 'Selamat datang kembali!');

    }
}
