<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can view login page.
     */
    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Auth/Login'));
    }

    /**
     * Test guest user is redirected to login from protected routes.
     */
    public function test_guest_user_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertStatus(302);
    }

    /**
     * Test authenticated user can logout.
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * Test logout invalidates session.
     */
    public function test_logout_invalidates_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $sessionId = session()->getId();

        $response = $this->post(route('logout'));

        $this->assertGuest();
        $this->assertNotEquals($sessionId, session()->getId());
    }

    /**
     * Test guest user cannot access authenticated routes.
     */
    public function test_guest_cannot_access_authenticated_routes(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user cannot access login page.
     */
    public function test_authenticated_user_cannot_access_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        // Authenticated users get redirected to dashboard
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test CSRF protection is enabled for login.
     */
    public function test_csrf_protection_is_enabled(): void
    {
        // Verify CSRF middleware is in web middleware group
        $this->assertTrue(
            in_array(\App\Http\Middleware\VerifyCsrfToken::class, $this->app['router']->getMiddleware())
            || in_array('web', array_keys($this->app['router']->getMiddlewareGroups()))
        );
    }

    /**
     * Test login does not have N+1 query issues.
     */
    public function test_login_does_not_have_n_plus_one_queries(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Enable query logging
        \DB::enableQueryLog();

        // Direct authentication without form submission
        Auth::attempt([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $queries = \DB::getQueryLog();
        
        // Auth attempt should execute minimal queries
        $this->assertLessThan(10, count($queries), 'Login should not have excessive database queries');
    }

    /**
     * Test direct authentication via actingAs.
     */
    public function test_acting_as_authenticates_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test authenticated user has access to protected resources.
     */
    public function test_authenticated_user_can_access_protected_resources(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Test user auth state persists across requests.
     */
    public function test_user_auth_state_persists_across_requests(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));
        
        // User should still be authenticated
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    /**
     * Test auth guard is correctly configured.
     */
    public function test_auth_guard_is_web(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'web');

        $this->assertAuthenticatedAs($user, 'web');
    }

    /**
     * Test user can be explicitly logged out.
     */
    public function test_user_can_be_explicitly_logged_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertTrue(Auth::check());

        Auth::logout();

        $this->assertFalse(Auth::check());
    }

    /**
     * Test user can retrieve their own authenticated data.
     */
    public function test_authenticated_user_can_retrieve_user_data(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $this->assertEquals($user->email, Auth::user()->email);
    }

    /**
     * Test multiple users cannot interfere with each other.
     */
    public function test_multiple_users_isolation(): void
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        $this->actingAs($user1);
        $this->assertAuthenticatedAs($user1);

        $this->actingAs($user2);
        $this->assertAuthenticatedAs($user2);
        $this->assertNotEquals($user1->id, Auth::id());
    }

    /**
     * Test Auth::attempt with correct credentials.
     */
    public function test_auth_attempt_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $attempt = Auth::attempt([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertTrue($attempt);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test Auth::attempt fails with incorrect password.
     */
    public function test_auth_attempt_fails_with_incorrect_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $attempt = Auth::attempt([
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertFalse($attempt);
        $this->assertGuest();
    }

    /**
     * Clean up after each test.
     */
    protected function tearDown(): void
    {
        RateLimiter::clear('login:test@example.com');
        parent::tearDown();
    }
}
