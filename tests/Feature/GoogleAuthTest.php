<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function fakeGoogleUser(array $overrides = []): SocialiteUser
    {
        $user = new SocialiteUser;

        $user->map(array_merge([
            'id' => 'google-12345',
            'nickname' => null,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'avatar' => 'https://lh3.googleusercontent.com/a/avatar.jpg',
        ], $overrides));

        return $user;
    }

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_from_root(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_is_redirected_to_dashboard_from_root(): void
    {
        $user = User::factory()->create(['password' => null]);

        $this->actingAs($user)->get('/')->assertRedirect(route('dashboard'));
    }

    public function test_authenticated_user_can_view_dashboard_with_account_data(): void
    {
        $user = User::factory()->create([
            'google_id' => 'google-12345',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => null,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('John Doe')
            ->assertSee('john@example.com');
    }

    public function test_google_redirect_sends_user_to_google(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('with')->once()->with(['prompt' => 'select_account'])->andReturnSelf();
        $provider->shouldReceive('redirect')->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('auth.google.redirect'))
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_google_callback_creates_a_new_user_and_authenticates(): void
    {
        Http::fake([
            'lh3.googleusercontent.com/*' => Http::response('fake-image-bytes', 200),
        ]);

        Socialite::fake('google', $this->fakeGoogleUser());

        $this->get(route('auth.google.callback'))->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'google_id' => 'google-12345',
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $user = User::where('google_id', 'google-12345')->firstOrFail();
        $this->assertEquals(Auth()->id(), $user->id);

        $this->assertNotNull($user->avatar);
        $this->assertStringStartsWith('avatars/', $user->avatar);
        $this->assertTrue(Storage::disk('public')->exists($user->avatar));
    }

    public function test_google_callback_uses_existing_user_without_overwriting(): void
    {
        $existing = User::factory()->create([
            'google_id' => 'google-12345',
            'email' => 'john@example.com',
            'name' => 'Original Name',
            'password' => null,
        ]);

        Http::fake([
            'lh3.googleusercontent.com/*' => Http::response('fake-image-bytes', 200),
        ]);

        Socialite::fake('google', $this->fakeGoogleUser(['name' => 'New Name']));

        $this->get(route('auth.google.callback'))->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertEquals($existing->id, Auth()->id());
        $this->assertSame('Original Name', $existing->fresh()->name);
    }

    public function test_google_callback_redirects_back_when_user_cancels(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn(null);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');
    }

    public function test_google_callback_redirects_back_on_invalid_state(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andThrow(new InvalidStateException);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');
    }
}
