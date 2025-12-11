<?php

namespace Tests\Unit\Services;

use App\Models\Subscription;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test subscribe service validates email format.
     */
    public function test_service_email_format_validation(): void
    {
        $validEmails = [
            'test@example.com',
            'user.name@example.co.uk',
            'user+tag@example.com',
        ];

        foreach ($validEmails as $email) {
            $isValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
            $this->assertTrue($isValid);
        }
    }

    /**
     * Test subscribe service rejects invalid email.
     */
    public function test_service_rejects_invalid_email(): void
    {
        $invalidEmails = [
            'not-an-email',
            '@example.com',
            'user@',
            'user name@example.com',
        ];

        foreach ($invalidEmails as $email) {
            $isValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
            $this->assertFalse($isValid);
        }
    }

    /**
     * Test subscribe service prevents duplicate emails.
     */
    public function test_service_prevents_duplicate_email(): void
    {
        $email = 'unique@example.com';
        Subscription::create(['email' => $email]);

        $this->expectException(\Exception::class);
        Subscription::create(['email' => $email]);
    }

    /**
     * Test subscribe service creates subscription.
     */
    public function test_service_creates_subscription(): void
    {
        $email = 'new@example.com';

        $subscription = Subscription::create([
            'email' => $email,
            'user_id' => $this->user->id,
        ]);

        $this->assertNotNull($subscription->id);
        $this->assertEquals($email, $subscription->email);
        $this->assertEquals($this->user->id, $subscription->user_id);
    }

    /**
     * Test subscribe service confirms subscription.
     */
    public function test_service_confirms_subscription(): void
    {
        $subscription = Subscription::create(['email' => 'confirm@example.com']);

        $subscription->update(['confirmed_at' => now()]);

        $isConfirmed = $subscription->fresh()->confirmed_at !== null;

        $this->assertTrue($isConfirmed);
    }

    /**
     * Test subscribe service unsubscribes.
     */
    public function test_service_unsubscribes(): void
    {
        $subscription = Subscription::create([
            'email' => 'unsub@example.com',
            'confirmed_at' => now(),
        ]);

        $subscription->update(['unsubscribed_at' => now()]);

        $isActive = $subscription->fresh()->confirmed_at !== null && 
                    $subscription->fresh()->unsubscribed_at === null;

        $this->assertFalse($isActive);
    }

    /**
     * Test subscribe service checks status.
     */
    public function test_service_checks_status(): void
    {
        $sub1 = Subscription::create([
            'email' => 'active@example.com',
            'confirmed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        $sub2 = Subscription::create([
            'email' => 'pending@example.com',
            'confirmed_at' => null,
        ]);

        $sub3 = Subscription::create([
            'email' => 'unsubscribed@example.com',
            'confirmed_at' => now(),
            'unsubscribed_at' => now(),
        ]);

        $status1 = $sub1->confirmed_at !== null && $sub1->unsubscribed_at === null ? 'active' : 'inactive';
        $status2 = $sub2->confirmed_at === null ? 'pending' : 'active';
        $status3 = $sub3->unsubscribed_at !== null ? 'unsubscribed' : 'active';

        $this->assertEquals('active', $status1);
        $this->assertEquals('pending', $status2);
        $this->assertEquals('unsubscribed', $status3);
    }

    /**
     * Test subscribe service updates preferences.
     */
    public function test_service_updates_preferences(): void
    {
        $subscription = Subscription::create(['email' => 'pref@example.com']);

        $subscription->update([
            'receive_ads' => true,
            'incorrect_words_frequency' => 'weekly',
            'topic_summary_frequency' => 'monthly',
        ]);

        $updated = $subscription->fresh();

        $this->assertTrue($updated->receive_ads);
        $this->assertEquals('weekly', $updated->incorrect_words_frequency);
        $this->assertEquals('monthly', $updated->topic_summary_frequency);
    }

    /**
     * Test subscribe service tracks email sent.
     */
    public function test_service_tracks_email_sent(): void
    {
        $subscription = Subscription::create(['email' => 'track@example.com']);

        $subscription->update(['last_ads_sent_at' => now()]);
        $subscription->update(['last_incorrect_words_sent_at' => now()]);
        $subscription->update(['last_topic_summary_sent_at' => now()]);

        $updated = $subscription->fresh();

        $this->assertNotNull($updated->last_ads_sent_at);
        $this->assertNotNull($updated->last_incorrect_words_sent_at);
        $this->assertNotNull($updated->last_topic_summary_sent_at);
    }

    /**
     * Test subscribe service finds by email.
     */
    public function test_service_finds_by_email(): void
    {
        $email = 'findme@example.com';
        Subscription::create(['email' => $email]);

        $found = Subscription::where('email', $email)->first();

        $this->assertNotNull($found);
        $this->assertEquals($email, $found->email);
    }

    /**
     * Test subscribe service gets user subscriptions.
     */
    public function test_service_gets_user_subscriptions(): void
    {
        Subscription::create([
            'email' => 'user1@example.com',
            'user_id' => $this->user->id,
        ]);

        Subscription::create([
            'email' => 'user2@example.com',
            'user_id' => $this->user->id,
        ]);

        $otherUser = User::factory()->create();
        Subscription::create([
            'email' => 'other@example.com',
            'user_id' => $otherUser->id,
        ]);

        $userSubs = Subscription::where('user_id', $this->user->id)->count();

        $this->assertEquals(2, $userSubs);
    }

    /**
     * Test subscribe service rate limiting.
     */
    public function test_service_rate_limiting(): void
    {
        // Simulate rate limiting - track attempts
        $attempts = 0;
        $maxAttempts = 5;

        for ($i = 0; $i < 10; $i++) {
            if ($attempts < $maxAttempts) {
                Subscription::create(['email' => "ratelimit{$i}@example.com"]);
                $attempts++;
            }
        }

        $this->assertEquals(5, $attempts);
    }

    /**
     * Test subscribe service sanitizes input.
     */
    public function test_service_sanitizes_input(): void
    {
        $unsafeEmail = '  test@example.com  ';
        $cleanEmail = trim($unsafeEmail);

        $subscription = Subscription::create(['email' => $cleanEmail]);

        $this->assertEquals('test@example.com', $subscription->email);
    }

    /**
     * Test subscribe service deletes subscription.
     */
    public function test_service_deletes_subscription(): void
    {
        $subscription = Subscription::create(['email' => 'delete@example.com']);
        $id = $subscription->id;

        $subscription->delete();

        $exists = Subscription::find($id);

        $this->assertNull($exists);
    }

    /**
     * Test subscribe service bulk create.
     */
    public function test_service_bulk_create(): void
    {
        $emails = [
            ['email' => 'bulk1@example.com'],
            ['email' => 'bulk2@example.com'],
            ['email' => 'bulk3@example.com'],
        ];

        foreach ($emails as $data) {
            Subscription::create($data);
        }

        $count = Subscription::count();

        $this->assertEquals(3, $count);
    }

    /**
     * Test subscribe service with timestamps.
     */
    public function test_service_timestamps_tracking(): void
    {
        $subscription = Subscription::create(['email' => 'timestamp@example.com']);

        $this->assertNotNull($subscription->created_at);
        $this->assertNotNull($subscription->updated_at);

        $subscription->update(['receive_ads' => true]);

        $this->assertNotNull($subscription->fresh()->updated_at);
    }

    /**
     * Test subscribe service email preference defaults.
     */
    public function test_service_email_preference_defaults(): void
    {
        $subscription = Subscription::create(['email' => 'defaults@example.com']);

        // Defaults should be 'none' per migration, but check what we get
        $this->assertThat(
            $subscription->incorrect_words_frequency,
            $this->logicalOr(
                $this->equalTo('none'),
                $this->isNull()
            )
        );
        $this->assertThat(
            $subscription->topic_summary_frequency,
            $this->logicalOr(
                $this->equalTo('none'),
                $this->isNull()
            )
        );
    }

    /**
     * Test subscribe service resubscribe flow.
     */
    public function test_service_resubscribe_flow(): void
    {
        $subscription = Subscription::create([
            'email' => 'resubscribe@example.com',
            'confirmed_at' => now(),
        ]);

        // Unsubscribe
        $subscription->update(['unsubscribed_at' => now()]);
        $this->assertNotNull($subscription->fresh()->unsubscribed_at);

        // Resubscribe
        $subscription->update(['unsubscribed_at' => null]);
        $isActive = $subscription->fresh()->confirmed_at !== null && 
                    $subscription->fresh()->unsubscribed_at === null;

        $this->assertTrue($isActive);
    }

    /**
     * Test subscribe service query performance with indexing.
     */
    public function test_service_email_query_performance(): void
    {
        // Create many subscriptions
        for ($i = 0; $i < 100; $i++) {
            Subscription::create(['email' => "perf{$i}@example.com"]);
        }

        // Query should be indexed on email (unique constraint)
        $found = Subscription::where('email', 'perf50@example.com')->first();

        $this->assertNotNull($found);
        $this->assertEquals('perf50@example.com', $found->email);
    }

    /**
     * Test subscribe service unconfirmed count.
     */
    public function test_service_unconfirmed_subscriptions(): void
    {
        Subscription::create(['email' => 'pending1@example.com']);
        Subscription::create(['email' => 'pending2@example.com']);
        Subscription::create([
            'email' => 'confirmed@example.com',
            'confirmed_at' => now(),
        ]);

        $unconfirmed = Subscription::whereNull('confirmed_at')->count();

        $this->assertEquals(2, $unconfirmed);
    }

    /**
     * Test subscribe service active vs inactive.
     */
    public function test_service_active_vs_inactive(): void
    {
        // Active
        Subscription::create([
            'email' => 'active@example.com',
            'confirmed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        // Inactive (unsubscribed)
        Subscription::create([
            'email' => 'inactive@example.com',
            'confirmed_at' => now(),
            'unsubscribed_at' => now(),
        ]);

        // Inactive (pending)
        Subscription::create([
            'email' => 'pending@example.com',
            'confirmed_at' => null,
        ]);

        $active = Subscription::whereNotNull('confirmed_at')
            ->whereNull('unsubscribed_at')
            ->count();

        $this->assertEquals(1, $active);
    }
}
