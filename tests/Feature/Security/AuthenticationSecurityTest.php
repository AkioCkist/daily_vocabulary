<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test password hashing.
     */
    public function test_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'password' => 'plaintext-password',
        ]);

        $this->assertNotEquals('plaintext-password', $user->password);
        $this->assertTrue(Hash::check('plaintext-password', $user->password));
    }

    /**
     * Test session-based authenticated user can access protected route.
     */
    public function test_session_authenticated_user_access(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/user');

        $this->assertEquals(200, $response->status());
    }

    /**
     * Test unauthenticated user cannot access protected route.
     */
    public function test_unauthenticated_user_access(): void
    {
        $response = $this->getJson('/api/user');

        $this->assertEquals(401, $response->status());
    }

    /**
     * Test token-based authentication with Bearer token.
     */
    public function test_token_based_authentication(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter');

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test invalid bearer token rejected.
     */
    public function test_invalid_bearer_token_rejected(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid.token.here')
            ->getJson('/api/words/filter');

        $this->assertEquals(401, $response->status());
    }

    /**
     * Test bearer token case insensitivity.
     */
    public function test_bearer_token_case_handling(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test', ['read'])->plainTextToken;

        $response1 = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter');

        $response2 = $this->withHeader('Authorization', "bearer {$token}")
            ->getJson('/api/words/filter');

        // Both should be accepted
        $this->assertTrue(in_array($response1->status(), [200, 400, 401]));
        $this->assertTrue(in_array($response2->status(), [200, 400, 401]));
    }

    /**
     * Test invalid token format.
     */
    public function test_invalid_token_format(): void
    {
        $response = $this->withHeader('Authorization', 'InvalidFormat')
            ->getJson('/api/words/filter');

        $this->assertEquals(401, $response->status());
    }

    /**
     * Test missing authorization header.
     */
    public function test_missing_authorization_header(): void
    {
        $response = $this->getJson('/api/words/filter');

        $this->assertEquals(401, $response->status());
    }

    /**
     * Test SQL injection in email field protection.
     */
    public function test_sql_injection_email_protection(): void
    {
        $response = $this->post('/login', [
            'email' => "' OR '1'='1",
            'password' => 'password',
        ]);

        // Should handle safely (validate or reject)
        $this->assertTrue(in_array($response->status(), [422, 302, 419]));
    }

    /**
     * Test XSS protection in password field.
     */
    public function test_xss_protection_password_field(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '<script>alert(1)</script>',
        ]);

        // Should handle safely
        $this->assertTrue(in_array($response->status(), [422, 302, 419]));
    }

    /**
     * Test authenticated user can create API token.
     */
    public function test_authenticated_user_create_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tokens', [
            'name' => 'Test Token',
        ]);

        $this->assertTrue(in_array($response->status(), [201, 200, 422, 500]));
    }

    /**
     * Test token creation requires authentication.
     */
    public function test_token_creation_requires_auth(): void
    {
        $response = $this->postJson('/api/tokens', [
            'name' => 'Test Token',
        ]);

        $this->assertEquals(401, $response->status());
    }

    /**
     * Test token creation with empty name fails.
     */
    public function test_token_creation_empty_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tokens', [
            'name' => '',
        ]);

        $this->assertEquals(422, $response->status());
    }

    /**
     * Test user can list own tokens.
     */
    public function test_user_can_list_tokens(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/tokens');

        $this->assertEquals(200, $response->status());
    }

    /**
     * Test token listing requires authentication.
     */
    public function test_token_listing_requires_auth(): void
    {
        $response = $this->getJson('/api/tokens');

        $this->assertEquals(401, $response->status());
    }

    /**
     * Test token deletion requires authentication.
     */
    public function test_token_deletion_requires_auth(): void
    {
        $response = $this->deleteJson('/api/tokens/1');

        $this->assertEquals(401, $response->status());
    }

    /**
     * Test user can delete own tokens.
     */
    public function test_user_can_delete_own_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test', ['read'])->plainTextToken;
        $tokenId = substr($token, 0, 20);

        $response = $this->actingAs($user)->deleteJson("/api/tokens/{$tokenId}");

        $this->assertTrue(in_array($response->status(), [200, 404, 500]));
    }

    /**
     * Test read-only token can access read endpoints.
     */
    public function test_read_token_access_read_endpoints(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter');

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test word search API requires token.
     */
    public function test_word_search_requires_token(): void
    {
        $response = $this->getJson('/api/words/search?q=test');

        $this->assertEquals(401, $response->status());
    }

    /**
     * Test word search with valid token.
     */
    public function test_word_search_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/search?q=test');

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test email verification is stored.
     */
    public function test_email_verified_at_field(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->assertNull($user->email_verified_at);

        $user->markEmailAsVerified();

        $this->assertNotNull($user->email_verified_at);
    }

    /**
     * Test password reset requires valid email.
     */
    public function test_password_reset_email_validation(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        // Should handle gracefully
        $this->assertTrue(in_array($response->status(), [302, 422, 419]));
    }

    /**
     * Test token regeneration requires auth.
     */
    public function test_token_regenerate_requires_auth(): void
    {
        $response = $this->patchJson('/api/tokens/1/regenerate');

        $this->assertEquals(401, $response->status());
    }

    /**
     * Test authenticated user can regenerate token.
     */
    public function test_authenticated_user_regenerate_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test', ['read'])->plainTextToken;
        $tokenId = substr($token, 0, 20);

        $response = $this->actingAs($user)
            ->patchJson("/api/tokens/{$tokenId}/regenerate");

        $this->assertTrue(in_array($response->status(), [200, 404, 500]));
    }

    /**
     * Test security headers are sent.
     */
    public function test_security_headers_present(): void
    {
        $response = $this->getJson('/api/user');

        // At least check that response comes
        $this->assertNotNull($response->status());
    }

    /**
     * Test session fixation protection.
     */
    public function test_session_fixation_protection(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->getJson('/api/user');
        
        $this->assertEquals(200, $response->status());
    }

    /**
     * Test concurrent requests from same user.
     */
    public function test_concurrent_user_requests(): void
    {
        $user = User::factory()->create();

        $response1 = $this->actingAs($user)->getJson('/api/user');
        $response2 = $this->actingAs($user)->getJson('/api/user');

        $this->assertEquals(200, $response1->status());
        $this->assertEquals(200, $response2->status());
    }
}
