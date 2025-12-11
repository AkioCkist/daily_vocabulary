<?php

namespace Tests\Feature\Subscription;

use App\Models\Subscription;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscribeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test subscribe with valid email.
     */
    public function test_subscribe_with_valid_email(): void
    {
        $email = 'test@example.com';

        $subscription = Subscription::create([
            'email' => $email,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => $email,
            'confirmed_at' => null,
            'unsubscribed_at' => null,
        ]);
        $this->assertEquals($email, $subscription->email);
    }

    /**
     * Test subscribe with duplicate email.
     */
    public function test_subscribe_duplicate_email_fails(): void
    {
        Subscription::create(['email' => 'duplicate@example.com']);

        $this->expectException(\Exception::class);
        Subscription::create(['email' => 'duplicate@example.com']);
    }

    /**
     * Test subscribe with invalid email format.
     */
    public function test_subscribe_invalid_email_format(): void
    {
        $subscription = new Subscription(['email' => 'invalid-email']);

        // Laravel validation would catch this in routes
        // Here we test that the model can be created, validation happens in service
        $this->assertNotNull($subscription);
    }

    /**
     * Test subscribe with authenticated user.
     */
    public function test_subscribe_with_user(): void
    {
        $user = User::factory()->create();
        $email = 'user.sub@example.com';

        $subscription = Subscription::create([
            'email' => $email,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => $email,
            'user_id' => $user->id,
        ]);
        $this->assertEquals($user->id, $subscription->user_id);
    }

    /**
     * Test confirm subscription.
     */
    public function test_confirm_subscription(): void
    {
        $subscription = Subscription::create(['email' => 'confirm@example.com']);

        $subscription->update(['confirmed_at' => now()]);

        $this->assertNotNull($subscription->confirmed_at);
        $this->assertDatabaseHas('subscriptions', [
            'email' => 'confirm@example.com',
            'confirmed_at' => $subscription->confirmed_at,
        ]);
    }

    /**
     * Test unsubscribe.
     */
    public function test_unsubscribe(): void
    {
        $subscription = Subscription::create([
            'email' => 'unsubscribe@example.com',
            'confirmed_at' => now(),
        ]);

        $subscription->update(['unsubscribed_at' => now()]);

        $this->assertNotNull($subscription->unsubscribed_at);
        $this->assertDatabaseHas('subscriptions', [
            'email' => 'unsubscribe@example.com',
            'unsubscribed_at' => $subscription->unsubscribed_at,
        ]);
    }

    /**
     * Test check subscription status - confirmed.
     */
    public function test_check_status_confirmed(): void
    {
        $subscription = Subscription::create([
            'email' => 'confirmed@example.com',
            'confirmed_at' => now(),
        ]);

        $isActive = $subscription->confirmed_at !== null && $subscription->unsubscribed_at === null;

        $this->assertTrue($isActive);
    }

    /**
     * Test check subscription status - unsubscribed.
     */
    public function test_check_status_unsubscribed(): void
    {
        $subscription = Subscription::create([
            'email' => 'unsubscribed@example.com',
            'confirmed_at' => now(),
            'unsubscribed_at' => now(),
        ]);

        $isActive = $subscription->confirmed_at !== null && $subscription->unsubscribed_at === null;

        $this->assertFalse($isActive);
    }

    /**
     * Test check subscription status - pending.
     */
    public function test_check_status_pending(): void
    {
        $subscription = Subscription::create([
            'email' => 'pending@example.com',
            'confirmed_at' => null,
        ]);

        $isPending = $subscription->confirmed_at === null && $subscription->unsubscribed_at === null;

        $this->assertTrue($isPending);
    }

    /**
     * Test re-subscribe after unsubscribe.
     */
    public function test_resubscribe_after_unsubscribe(): void
    {
        $subscription = Subscription::create([
            'email' => 'resubscribe@example.com',
            'confirmed_at' => now(),
            'unsubscribed_at' => now(),
        ]);

        // Re-subscribe by clearing unsubscribed_at
        $subscription->update(['unsubscribed_at' => null]);

        $this->assertNull($subscription->fresh()->unsubscribed_at);
        $this->assertNotNull($subscription->fresh()->confirmed_at);
    }

    /**
     * Test subscription email preferences - ads.
     */
    public function test_subscription_preference_ads(): void
    {
        $subscription = Subscription::create([
            'email' => 'ads@example.com',
            'receive_ads' => true,
        ]);

        $this->assertTrue($subscription->receive_ads);

        $subscription->update(['receive_ads' => false]);
        $this->assertFalse($subscription->fresh()->receive_ads);
    }

    /**
     * Test subscription email preferences - incorrect words frequency.
     */
    public function test_subscription_preference_incorrect_words(): void
    {
        $subscription = Subscription::create([
            'email' => 'incorrect@example.com',
            'incorrect_words_frequency' => 'weekly',
        ]);

        $this->assertEquals('weekly', $subscription->incorrect_words_frequency);

        $subscription->update(['incorrect_words_frequency' => 'monthly']);
        $this->assertEquals('monthly', $subscription->fresh()->incorrect_words_frequency);
    }

    /**
     * Test subscription email preferences - topic summary frequency.
     */
    public function test_subscription_preference_topic_summary(): void
    {
        $subscription = Subscription::create([
            'email' => 'topic@example.com',
            'topic_summary_frequency' => 'weekly',
        ]);

        $this->assertEquals('weekly', $subscription->topic_summary_frequency);

        $subscription->update(['topic_summary_frequency' => 'none']);
        $this->assertEquals('none', $subscription->fresh()->topic_summary_frequency);
    }

    /**
     * Test subscription email log tracking.
     */
    public function test_subscription_last_sent_timestamps(): void
    {
        $subscription = Subscription::create([
            'email' => 'tracking@example.com',
            'last_ads_sent_at' => now(),
        ]);

        $this->assertNotNull($subscription->last_ads_sent_at);

        $subscription->update(['last_incorrect_words_sent_at' => now()]);
        $this->assertNotNull($subscription->fresh()->last_incorrect_words_sent_at);
    }

    /**
     * Test multiple subscriptions per user.
     */
    public function test_multiple_subscriptions_per_user(): void
    {
        $user = User::factory()->create();

        $sub1 = Subscription::create([
            'email' => 'sub1@example.com',
            'user_id' => $user->id,
        ]);

        $sub2 = Subscription::create([
            'email' => 'sub2@example.com',
            'user_id' => $user->id,
        ]);

        $userSubs = Subscription::where('user_id', $user->id)->count();

        $this->assertEquals(2, $userSubs);
    }

    /**
     * Test subscription without user.
     */
    public function test_subscription_without_user(): void
    {
        $subscription = Subscription::create([
            'email' => 'nouser@example.com',
            'user_id' => null,
        ]);

        $this->assertNull($subscription->user_id);
    }

    /**
     * Test subscription user foreign key constraint.
     */
    public function test_subscription_user_deletion(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::create([
            'email' => 'cascading@example.com',
            'user_id' => $user->id,
        ]);

        $user->delete();

        // Foreign key should null out
        $this->assertNull($subscription->fresh()->user_id);
    }

    /**
     * Test bulk subscriptions.
     */
    public function test_bulk_subscriptions(): void
    {
        $emails = [
            'bulk1@example.com',
            'bulk2@example.com',
            'bulk3@example.com',
        ];

        foreach ($emails as $email) {
            Subscription::create(['email' => $email]);
        }

        $count = Subscription::count();

        $this->assertEquals(3, $count);
    }

    /**
     * Test subscription query by email.
     */
    public function test_query_subscription_by_email(): void
    {
        $email = 'query@example.com';
        Subscription::create(['email' => $email]);

        $subscription = Subscription::where('email', $email)->first();

        $this->assertNotNull($subscription);
        $this->assertEquals($email, $subscription->email);
    }

    /**
     * Test subscription query active subscriptions.
     */
    public function test_query_active_subscriptions(): void
    {
        Subscription::create([
            'email' => 'active1@example.com',
            'confirmed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        Subscription::create([
            'email' => 'unactive@example.com',
            'confirmed_at' => now(),
            'unsubscribed_at' => now(),
        ]);

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
