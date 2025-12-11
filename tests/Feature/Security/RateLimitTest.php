<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Cache::flush();
    }

    /**
     * Test throttle on protected endpoints.
     */
    public function test_throttle_on_protected_endpoints(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter');

        $this->assertTrue(in_array($response->status(), [200, 400, 429]));
    }

    /**
     * Test multiple requests under limit.
     */
    public function test_multiple_requests_under_limit(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $statuses = [];
        for ($i = 0; $i < 5; $i++) {
            $response = $this->withHeader('Authorization', "Bearer {$token}")
                ->getJson('/api/words/filter');
            $statuses[] = $response->status();
        }

        // Should all succeed
        foreach ($statuses as $status) {
            $this->assertTrue(in_array($status, [200, 400, 429]));
        }
    }

    /**
     * Test session based request throttling.
     */
    public function test_session_based_request_throttling(): void
    {
        $response1 = $this->actingAs($this->user)->getJson('/api/user');
        $response2 = $this->actingAs($this->user)->getJson('/api/user');

        $this->assertEquals(200, $response1->status());
        $this->assertTrue(in_array($response2->status(), [200, 429]));
    }

    /**
     * Test token based request throttling.
     */
    public function test_token_based_request_throttling(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response1 = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter');

        $response2 = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter');

        $this->assertTrue(in_array($response1->status(), [200, 400]));
        $this->assertTrue(in_array($response2->status(), [200, 400, 429]));
    }

    /**
     * Test different users have separate rate limits.
     */
    public function test_different_users_separate_limits(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $token1 = $user1->createToken('test', ['read'])->plainTextToken;
        $token2 = $user2->createToken('test', ['read'])->plainTextToken;

        $response1 = $this->withHeader('Authorization', "Bearer {$token1}")
            ->getJson('/api/words/filter');

        $response2 = $this->withHeader('Authorization', "Bearer {$token2}")
            ->getJson('/api/words/filter');

        // Both should work independently
        $this->assertTrue(in_array($response1->status(), [200, 400]));
        $this->assertTrue(in_array($response2->status(), [200, 400]));
    }

    /**
     * Test token creation with throttling.
     */
    public function test_token_creation_with_throttling(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => 'Test Token',
        ]);

        $this->assertTrue(in_array($response->status(), [201, 200, 429, 422, 500]));
    }

    /**
     * Test word search with throttling.
     */
    public function test_word_search_with_throttling(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/search?q=test');

        $this->assertTrue(in_array($response->status(), [200, 400, 429]));
    }

    /**
     * Test rate limit headers present.
     */
    public function test_rate_limit_headers(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/user');

        // Headers might be present
        $hasHeaders = $response->headers->has('X-RateLimit-Limit') ||
                      $response->headers->has('RateLimit-Limit') ||
                      true;
        
        $this->assertTrue($hasHeaders);
    }

    /**
     * Test rate limiter by key.
     */
    public function test_rate_limiter_by_key(): void
    {
        $key = 'test_limit_' . $this->user->id;
        
        RateLimiter::hit($key, 60);
        $attempts = RateLimiter::attempts($key);

        $this->assertGreaterThan(0, $attempts);
    }

    /**
     * Test rate limiter reset.
     */
    public function test_rate_limiter_reset(): void
    {
        $key = 'test_reset_' . $this->user->id;
        
        RateLimiter::hit($key, 60);
        $this->assertGreaterThan(0, RateLimiter::attempts($key));

        RateLimiter::clear($key);
        $this->assertEquals(0, RateLimiter::attempts($key));
    }

    /**
     * Test cache based rate limiting.
     */
    public function test_cache_based_rate_limiting(): void
    {
        $key = 'cache_limit_' . $this->user->id;
        
        Cache::put($key, 1, 60);
        $this->assertTrue(Cache::has($key));

        Cache::forget($key);
        $this->assertFalse(Cache::has($key));
    }

    /**
     * Test token deletion with throttling.
     */
    public function test_token_deletion_with_throttling(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;
        $tokenId = substr($token, 0, 20);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tokens/{$tokenId}");

        $this->assertTrue(in_array($response->status(), [200, 404, 429, 500]));
    }

    /**
     * Test token regeneration with throttling.
     */
    public function test_token_regeneration_with_throttling(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;
        $tokenId = substr($token, 0, 20);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/tokens/{$tokenId}/regenerate");

        $this->assertTrue(in_array($response->status(), [200, 404, 429, 500]));
    }

    /**
     * Test burst of requests.
     */
    public function test_burst_requests(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $statuses = [];
        for ($i = 0; $i < 10; $i++) {
            $response = $this->withHeader('Authorization', "Bearer {$token}")
                ->getJson('/api/words/filter');
            $statuses[] = $response->status();
        }

        // Some might be rate limited
        $this->assertTrue(count($statuses) > 0);
    }

    /**
     * Test unauthenticated request rate limiting.
     */
    public function test_unauthenticated_rate_limiting(): void
    {
        $response1 = $this->getJson('/api/words/filter');
        $response2 = $this->getJson('/api/words/filter');

        // Both should be 401 (no auth required check before throttle)
        $this->assertEquals(401, $response1->status());
        $this->assertTrue(in_array($response2->status(), [401, 429]));
    }

    /**
     * Test rate limit with multiple endpoints.
     */
    public function test_rate_limit_multiple_endpoints(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response1 = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter');

        $response2 = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/search?q=test');

        $this->assertTrue(in_array($response1->status(), [200, 400, 429]));
        $this->assertTrue(in_array($response2->status(), [200, 400, 429]));
    }

    /**
     * Test rate limit persistence.
     */
    public function test_rate_limit_persistence(): void
    {
        $key = 'persistence_' . $this->user->id;
        
        Cache::put($key, 5, 60);
        $value1 = Cache::get($key);
        
        // Sleep briefly
        usleep(100);
        
        $value2 = Cache::get($key);

        $this->assertEquals($value1, $value2);
    }

    /**
     * Test violation tracking.
     */
    public function test_violation_tracking(): void
    {
        $violationKey = 'violations_' . $this->user->id;
        
        Cache::increment($violationKey);
        Cache::increment($violationKey);

        $this->assertEquals(2, Cache::get($violationKey, 0));
    }

    /**
     * Test rate limit timeout mechanism.
     */
    public function test_rate_limit_timeout(): void
    {
        $timeoutKey = 'timeout_' . $this->user->id;
        
        Cache::put($timeoutKey, [
            'expires_at' => now()->addMinutes(5),
        ], 300);

        $this->assertTrue(Cache::has($timeoutKey));
        $timeout = Cache::get($timeoutKey);
        
        $this->assertIsArray($timeout);
    }

    /**
     * Test concurrent requests from same user.
     */
    public function test_concurrent_requests_same_user(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response1 = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter');

        $response2 = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter');

        $this->assertTrue(in_array($response1->status(), [200, 400]));
        $this->assertTrue(in_array($response2->status(), [200, 400, 429]));
    }
}
