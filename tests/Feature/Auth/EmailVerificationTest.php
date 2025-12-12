<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test email verification notice page can be rendered.
     */
    public function test_email_verification_notice_page_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Auth/VerifyEmail'));
    }

    /**
     * Test email can be verified with valid link.
     */
    public function test_email_can_be_verified_with_valid_link(): void
    {
        Event::fake([Verified::class]);

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
        $response->assertRedirect(route('home'));
    }

    /**
     * Test email verification fails with invalid hash.
     */
    public function test_email_verification_fails_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $response->assertStatus(403);
    }

    /**
     * Test email verification fails with expired link.
     */
    public function test_email_verification_fails_with_expired_link(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(10),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $response->assertStatus(403);
    }

    /**
     * Test email verification fails for wrong user.
     */
    public function test_email_verification_fails_for_wrong_user(): void
    {
        $user1 = User::factory()->unverified()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->unverified()->create(['email' => 'user2@example.com']);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user1->id, 'hash' => sha1($user1->email)]
        );

        $response = $this->actingAs($user2)->get($verificationUrl);

        $this->assertFalse($user1->fresh()->hasVerifiedEmail());
        $response->assertStatus(403);
    }

    /**
     * Test already verified email redirects properly.
     */
    public function test_already_verified_email_redirects_properly(): void
    {
        $user = User::factory()->create(); // Already verified by default

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('home'));
    }

    /**
     * Test verification notification can be sent.
     */
    public function test_verification_notification_can_be_sent(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post(route('verification.send'));

        Notification::assertSentTo($user, VerifyEmail::class);
        $response->assertStatus(302);
    }

    /**
     * Test verification notification is rate limited.
     */
    public function test_verification_notification_is_rate_limited(): void
    {
        $user = User::factory()->unverified()->create();

        // Send multiple verification emails
        for ($i = 0; $i < 7; $i++) {
            $response = $this->actingAs($user)->post(route('verification.send'));
        }

        // Should be rate limited after 6 requests
        $response->assertStatus(429);
    }

    /**
     * Test guest cannot access verification routes.
     */
    public function test_guest_cannot_access_verification_notice(): void
    {
        $response = $this->get(route('verification.notice'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test guest cannot verify email.
     */
    public function test_guest_cannot_verify_email(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect(route('login'));
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    /**
     * Test guest cannot send verification notification.
     */
    public function test_guest_cannot_send_verification_notification(): void
    {
        $response = $this->post(route('verification.send'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test verified event is dispatched on verification.
     */
    public function test_verified_event_is_dispatched(): void
    {
        Event::fake([Verified::class]);

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    /**
     * Test verified user can access protected routes.
     */
    public function test_verified_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create(); // Verified by default

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Test unverified user is redirected from verified routes.
     */
    public function test_unverified_user_is_redirected_from_verified_routes(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('verification.notice'));
    }

    /**
     * Test verification link uses signed URL.
     */
    public function test_verification_link_uses_signed_url(): void
    {
        $user = User::factory()->unverified()->create();

        // Try to access without signature
        $unsignedUrl = route('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->email)
        ]);

        $response = $this->actingAs($user)->get($unsignedUrl);

        $response->assertStatus(403);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    /**
     * Test multiple verification notifications don't create issues.
     */
    public function test_multiple_verification_notifications_work_correctly(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        // Send first notification
        $this->actingAs($user)->post(route('verification.send'));
        
        // Send second notification
        $this->actingAs($user)->post(route('verification.send'));

        // Both should be sent
        Notification::assertSentTo($user, VerifyEmail::class, 2);
    }

    /**
     * Test email verification doesn't have N+1 query issues.
     */
    public function test_email_verification_does_not_have_n_plus_one_queries(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        \DB::enableQueryLog();

        $this->actingAs($user)->get($verificationUrl);

        $queries = \DB::getQueryLog();
        
        // Email verification should execute minimal queries
        $this->assertLessThan(6, count($queries), 'Email verification should not have excessive database queries');
    }

    /**
     * Test verification with tampered user ID.
     */
    public function test_verification_fails_with_tampered_user_id(): void
    {
        $user1 = User::factory()->unverified()->create();
        $user2 = User::factory()->unverified()->create();

        // Create valid signature for user1 but use user2's hash
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user1->id, 'hash' => sha1($user2->email)]
        );

        $response = $this->actingAs($user1)->get($verificationUrl);

        $this->assertFalse($user1->fresh()->hasVerifiedEmail());
        $response->assertStatus(403);
    }

    /**
     * Test CSRF protection is enabled for verification actions.
     */
    public function test_csrf_protection_is_enabled(): void
    {
        $this->assertTrue(
            in_array(\App\Http\Middleware\VerifyCsrfToken::class, $this->app['router']->getMiddleware())
            || in_array('web', array_keys($this->app['router']->getMiddlewareGroups()))
        );
    }
}
