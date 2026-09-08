<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_route_redirects_to_google()
    {
        $response = $this->get(route('auth.google.redirect'));

        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    public function test_google_callback_rejects_unauthorized_domain()
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-id-123');
        $abstractUser->shouldReceive('getEmail')->andReturn('user@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('External User');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://avatar.url');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_google_callback_rejects_unregistered_staff_account()
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-id-123');
        $abstractUser->shouldReceive('getEmail')->andReturn('unregistered@staff.msu.ac.zw');
        $abstractUser->shouldReceive('getName')->andReturn('Unregistered Staff');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://avatar.url');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_google_callback_authenticates_registered_staff_account()
    {
        $user = User::factory()->create([
            'email' => 'auditor@staff.msu.ac.zw',
            'name' => 'Auditor Staff',
        ]);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-id-12345');
        $abstractUser->shouldReceive('getEmail')->andReturn('auditor@staff.msu.ac.zw');
        $abstractUser->shouldReceive('getName')->andReturn('Auditor Staff');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar');

        $provider = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertEquals('google-id-12345', $user->google_id);
    }
}
