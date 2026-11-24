<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            ]
        );

        $this->persistGoogleAvatar($user, $googleUser->getAvatar());

        $wasRecentlyCreated = $user->wasRecentlyCreated;

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('status', $wasRecentlyCreated
                ? 'Akun Anda berhasil dibuat.'
                : 'Selamat datang kembali!');
    }

    /**
     * Download the Google avatar into local storage to avoid the
     * rate limits Google applies to its avatar CDN
     * (https://lh3.googleusercontent.com).
     */
    private function persistGoogleAvatar(User $user, ?string $avatarUrl): void
    {
        if ($avatarUrl === null) {
            return;
        }

        // Jika avatar sudah berupa path lokal, jangan unduh ulang.
        if ($user->avatar !== null && ! str_starts_with($user->avatar, 'http')) {
            return;
        }

        try {
            $response = Http::timeout(8)->get($avatarUrl);

            if ($response->successful()) {
                $path = 'avatars/'.$user->id.'-'.Str::random(16).'.jpg';

                Storage::disk('public')->put($path, $response->body());

                $user->forceFill(['avatar' => $path])->save();
            }
        } catch (\Throwable) {
            // Biarkan avatar tetap apa adanya (null atau URL lama).
        }
    }
}
