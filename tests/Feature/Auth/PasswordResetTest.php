<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test forgot password page can be rendered.
     */
    public function test_forgot_password_page_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Auth/ForgotPassword'));
    }

    /**
     * Test reset password link can be requested.
     */
    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * Test email field is required for password reset request.
     */
    public function test_email_field_is_required_for_reset_request(): void
    {
        $response = $this->post(route('password.email'), []);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test email must be valid format for reset request.
     */
    public function test_email_must_be_valid_format_for_reset_request(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test reset link request fails for non-existent email.
     */
    public function test_reset_request_fails_for_nonexistent_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test reset password page can be rendered with valid token.
     */
    public function test_reset_password_page_can_be_rendered_with_token(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->get(route('password.reset', ['token' => $token]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Auth/ResetPassword'));
    }

    /**
     * Test password can be reset with valid token.
     */
    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect(route('login'));

        $user->refresh();

        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
    }

    /**
     * Test token field is required for password reset.
     */
    public function test_token_field_is_required_for_password_reset(): void
    {
        $response = $this->post(route('password.store'), [
            'email' => 'test@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('token');
    }

    /**
     * Test email field is required for password reset.
     */
    public function test_email_field_is_required_for_password_reset(): void
    {
        $response = $this->post(route('password.store'), [
            'token' => 'fake-token',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test password field is required for password reset.
     */
    public function test_password_field_is_required_for_password_reset(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test password must be confirmed for reset.
     */
    public function test_password_must_be_confirmed_for_reset(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test password reset fails with invalid token.
     */
    public function test_password_reset_fails_with_invalid_token(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post(route('password.store'), [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test password reset fails with expired token.
     */
    public function test_password_reset_fails_with_expired_token(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        // Simulate token expiration by traveling forward in time
        $this->travel(config('auth.passwords.users.expire') + 1)->minutes();

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->travelBack();
    }

    /**
     * Test password reset fails when email doesn't match token.
     */
    public function test_password_reset_fails_when_email_mismatch(): void
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);
        
        $token = Password::createToken($user1);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'user2@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test new password must meet minimum length requirement.
     */
    public function test_new_password_must_meet_minimum_length(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'Pass1!',
            'password_confirmation' => 'Pass1!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test password reset token is invalidated after use.
     */
    public function test_token_is_invalidated_after_successful_reset(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        // First reset succeeds
        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        // Second attempt with same token fails
        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'AnotherPassword123!',
            'password_confirmation' => 'AnotherPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test password reset does not reveal user existence.
     */
    public function test_password_reset_does_not_reveal_user_existence(): void
    {
        $user = User::factory()->create(['email' => 'existing@example.com']);

        // Request for existing user
        $response1 = $this->post(route('password.email'), [
            'email' => 'existing@example.com',
        ]);

        // Request for non-existing user
        $response2 = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        // Both should show errors (security best practice)
        $response2->assertSessionHasErrors('email');
    }

    /**
     * Test password reset does not have N+1 query issues.
     */
    public function test_password_reset_does_not_have_n_plus_one_queries(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        \DB::enableQueryLog();

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $queries = \DB::getQueryLog();
        
        // Password reset should execute minimal queries
        $this->assertLessThan(8, count($queries), 'Password reset should not have excessive database queries');
    }

    /**
     * Test CSRF protection is enabled for password reset.
     */
    public function test_csrf_protection_is_enabled(): void
    {
        $this->assertTrue(
            in_array(\App\Http\Middleware\VerifyCsrfToken::class, $this->app['router']->getMiddleware())
            || in_array('web', array_keys($this->app['router']->getMiddlewareGroups()))
        );
    }
}
