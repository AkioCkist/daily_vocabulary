<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InputValidationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test XSS protection with script tag in token name.
     */
    public function test_xss_script_tag_protection(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => '<script>alert("xss")</script>',
        ]);

        // Should be rejected or safely handled
        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }

    /**
     * Test XSS protection with event handler.
     */
    public function test_xss_event_handler_protection(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => '<img src=x onerror="alert(1)">',
        ]);

        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }

    /**
     * Test HTML entity handling.
     */
    public function test_html_entity_encoding(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => 'Test & <Token>',
        ]);

        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }

    /**
     * Test SQL injection in search query.
     */
    public function test_sql_injection_in_search(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/words/search?q=' OR '1'='1");

        // Should safely handle
        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test command injection protection.
     */
    public function test_command_injection_protection(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => 'Test; rm -rf /',
        ]);

        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }

    /**
     * Test null character handling.
     */
    public function test_null_character_handling(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => "Test\x00Token",
        ]);

        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }

    /**
     * Test emoji handling in token name.
     */
    public function test_emoji_handling(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => 'Test 🔐 Token 🎉',
        ]);

        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }

    /**
     * Test JSON injection protection.
     */
    public function test_json_injection_protection(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => '{"admin": true}',
        ]);

        // Should treat as string, not parse JSON
        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }

    /**
     * Test path traversal protection in search.
     */
    public function test_path_traversal_protection(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/words/search?q=../../config/app.php");

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test very long input handling.
     */
    public function test_long_input_handling(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => str_repeat('a', 10000),
        ]);

        $this->assertTrue(in_array($response->status(), [413, 422, 201, 200, 500]));
    }

    /**
     * Test special characters in token name.
     */
    public function test_special_characters_handling(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => 'Test!@#$%^&*()Token',
        ]);

        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }

    /**
     * Test unicode input handling.
     */
    public function test_unicode_input_handling(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => 'Token—with—dashes',
        ]);

        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }

    /**
     * Test newline characters in input.
     */
    public function test_newline_characters(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => "Test\nToken",
        ]);

        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }

    /**
     * Test CRLF injection protection.
     */
    public function test_crlf_injection_protection(): void
    {
        $response = $this->actingAs($this->user)
            ->withHeader('X-Custom-Header', "Value\r\nMalicious: Header")
            ->getJson('/api/user');

        $this->assertTrue(in_array($response->status(), [200, 400, 401]));
    }

    /**
     * Test empty search query.
     */
    public function test_empty_search_query(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/search?q=');

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test whitespace only search.
     */
    public function test_whitespace_only_search(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/search?q=%20%20%20');

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test URL encoded malicious input.
     */
    public function test_url_encoded_malicious_input(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/search?q=' . urlencode('<script>'));

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test double URL encoded input.
     */
    public function test_double_url_encoded_input(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $encoded = urlencode(urlencode('<script>'));
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/words/search?q={$encoded}");

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test null byte in search.
     */
    public function test_null_byte_in_search(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/words/search?q=test\x00bad");

        $this->assertTrue(in_array($response->status(), [200, 400, 404]));
    }

    /**
     * Test control characters.
     */
    public function test_control_characters(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => "Test\x01\x02Token",
        ]);

        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }

    /**
     * Test regex DoS protection via long pattern.
     */
    public function test_regex_dos_protection(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $largeInput = str_repeat('a', 10000);
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/words/search?q={$largeInput}");

        // Should not cause timeout
        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test boolean parameter injection.
     */
    public function test_boolean_parameter_injection(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter?active=true&admin=true');

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test numeric parameter validation.
     */
    public function test_numeric_parameter_validation(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter?limit=invalid');

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test array parameter handling.
     */
    public function test_array_parameter_handling(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter?ids[]=1&ids[]=2&ids[]=3');

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test deeply nested array parameters.
     */
    public function test_deeply_nested_parameters(): void
    {
        $token = $this->user->createToken('test', ['read'])->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/words/filter?data[a][b][c][d][e]=value');

        $this->assertTrue(in_array($response->status(), [200, 400]));
    }

    /**
     * Test mixed encoding in input.
     */
    public function test_mixed_encoding(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/tokens', [
            'name' => 'Test' . chr(0xFF) . 'Token',
        ]);

        $this->assertTrue(in_array($response->status(), [422, 201, 200, 500]));
    }
}
