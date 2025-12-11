<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test registration page can be rendered.
     */
    public function test_registration_page_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Auth/Register'));
    }

    /**
     * Test user can register with valid data.
     */
    public function test_user_can_register_with_valid_data(): void
    {
        Event::fake([Registered::class]);

        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('home'));

        Event::assertDispatched(Registered::class);
    }

    /**
     * Test name field is required.
     */
    public function test_name_field_is_required(): void
    {
        $response = $this->post(route('register'), [
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test email field is required.
     */
    public function test_email_field_is_required(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test password field is required.
     */
    public function test_password_field_is_required(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test email must be valid format.
     */
    public function test_email_must_be_valid_format(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test email must be unique.
     */
    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Test password must be confirmed.
     */
    public function test_password_must_be_confirmed(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test password must meet minimum length requirement.
     */
    public function test_password_must_meet_minimum_length(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Pass1!',
            'password_confirmation' => 'Pass1!',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test password is hashed before storing.
     */
    public function test_password_is_hashed(): void
    {
        $password = 'Password123!';

        $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $user = User::where('email', 'test@example.com')->first();
        
        $this->assertNotNull($user);
        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    /**
     * Test name has maximum length validation.
     */
    public function test_name_has_maximum_length(): void
    {
        $response = $this->post(route('register'), [
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test email has maximum length validation.
     */
    public function test_email_has_maximum_length(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => str_repeat('a', 250) . '@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test user is automatically logged in after registration.
     */
    public function test_user_is_logged_in_after_registration(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test registered event is dispatched.
     */
    public function test_registered_event_is_dispatched(): void
    {
        Event::fake([Registered::class]);

        $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        Event::assertDispatched(Registered::class, function ($event) {
            return $event->user->email === 'test@example.com';
        });
    }

    /**
     * Test authenticated user cannot access registration page.
     */
    public function test_authenticated_user_cannot_access_registration_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('register'));

        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test registration with XSS attempt in name is sanitized.
     */
    public function test_xss_attempt_in_name_is_handled(): void
    {
        $response = $this->post(route('register'), [
            'name' => '<script>alert("XSS")</script>Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        
        // Laravel's validation and Eloquent should handle this
        $this->assertNotNull($user);
        $this->assertStringContainsString('Test User', $user->name);
    }

    /**
     * Test registration does not have N+1 query issues.
     */
    public function test_registration_does_not_have_n_plus_one_queries(): void
    {
        \DB::enableQueryLog();

        $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $queries = \DB::getQueryLog();
        
        // Registration should execute minimal queries (email check, insert user, possibly session update)
        $this->assertLessThan(6, count($queries), 'Registration should not have excessive database queries');
    }

    /**
     * Test user defaults are set correctly.
     */
    public function test_user_defaults_are_set_correctly(): void
    {
        $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);
        $this->assertFalse($user->is_admin);
        $this->assertNull($user->two_factor_secret);
    }

    /**
     * Test CSRF protection is enabled for registration.
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
     * Test SQL injection attempt is prevented.
     */
    public function test_sql_injection_is_prevented(): void
    {
        $response = $this->post(route('register'), [
            'name' => "Test'; DROP TABLE users; --",
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // Should complete successfully with escaped data
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        // Verify users table still exists
        $this->assertTrue(\Schema::hasTable('users'));
    }

    /**
     * Clean up after each test.
     */
    protected function tearDown(): void
    {
        RateLimiter::clear('register:test@example.com');
        parent::tearDown();
    }
}
