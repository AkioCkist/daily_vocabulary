<?php

namespace Tests\Unit\Repositories;

use App\Models\Subscription;
use App\Repositories\Eloquent\EloquentSubscriptionRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentSubscriptionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentSubscriptionRepository();
    }

    /** @test */
    public function it_finds_subscription_by_email()
    {
        // Arrange
        $subscription = Subscription::factory()->create([
            'email' => 'test@example.com'
        ]);

        // Act
        $found = $this->repository->findByEmail('test@example.com');

        // Assert
        $this->assertInstanceOf(Subscription::class, $found);
        $this->assertEquals($subscription->id, $found->id);
        $this->assertEquals('test@example.com', $found->email);
    }

    /** @test */
    public function it_returns_null_when_subscription_not_found()
    {
        // Act
        $found = $this->repository->findByEmail('nonexistent@example.com');

        // Assert
        $this->assertNull($found);
    }

    /** @test */
    public function it_creates_new_subscription()
    {
        // Arrange
        $data = [
            'email' => 'new@example.com'
        ];

        // Act
        $subscription = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('new@example.com', $subscription->email);
        $this->assertNull($subscription->confirmed_at);
        $this->assertNull($subscription->unsubscribed_at);
        
        $this->assertDatabaseHas('subscriptions', [
            'email' => 'new@example.com',
            'confirmed_at' => null,
            'unsubscribed_at' => null
        ]);
    }

    /** @test */
    public function it_creates_subscription_with_additional_data()
    {
        // Arrange
        $data = [
            'email' => 'new@example.com',
            'confirmed_at' => now()
        ];

        // Act
        $subscription = $this->repository->create($data);

        // Assert
        $this->assertEquals('new@example.com', $subscription->email);
        $this->assertNotNull($subscription->confirmed_at);
    }

    /** @test */
    public function it_confirms_subscription()
    {
        // Arrange
        $subscription = Subscription::factory()->create([
            'email' => 'test@example.com',
            'confirmed_at' => null
        ]);
        
        $beforeTime = now()->subSecond();

        // Act
        $result = $this->repository->confirm('test@example.com');
        
        $afterTime = now()->addSecond();

        // Assert
        $this->assertTrue($result);
        
        $subscription->refresh();
        $this->assertNotNull($subscription->confirmed_at);
        $this->assertTrue($subscription->confirmed_at->between($beforeTime, $afterTime));
        
        $this->assertDatabaseHas('subscriptions', [
            'email' => 'test@example.com',
            'id' => $subscription->id
        ]);
        $this->assertDatabaseMissing('subscriptions', [
            'email' => 'test@example.com',
            'confirmed_at' => null
        ]);
    }

    /** @test */
    public function it_returns_false_when_confirming_non_existent_subscription()
    {
        // Act
        $result = $this->repository->confirm('nonexistent@example.com');

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    public function it_unsubscribes_subscription()
    {
        // Arrange
        $subscription = Subscription::factory()->create([
            'email' => 'test@example.com',
            'confirmed_at' => now()->subDays(5),
            'unsubscribed_at' => null
        ]);
        
        $beforeTime = now()->subSecond();

        // Act
        $result = $this->repository->unsubscribe('test@example.com');
        
        $afterTime = now()->addSecond();

        // Assert
        $this->assertTrue($result);
        
        $subscription->refresh();
        $this->assertNotNull($subscription->unsubscribed_at);
        $this->assertTrue($subscription->unsubscribed_at->between($beforeTime, $afterTime));
        
        $this->assertDatabaseHas('subscriptions', [
            'email' => 'test@example.com',
            'id' => $subscription->id
        ]);
        $this->assertDatabaseMissing('subscriptions', [
            'email' => 'test@example.com',
            'unsubscribed_at' => null
        ]);
    }

    /** @test */
    public function it_returns_false_when_unsubscribing_non_existent_subscription()
    {
        // Act
        $result = $this->repository->unsubscribe('nonexistent@example.com');

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_confirm_already_confirmed_subscription()
    {
        // Arrange
        $originalConfirmTime = now()->subDays(1);
        $subscription = Subscription::factory()->create([
            'email' => 'test@example.com',
            'confirmed_at' => $originalConfirmTime
        ]);

        // Act
        $result = $this->repository->confirm('test@example.com');

        // Assert
        $this->assertTrue($result);
        
        $subscription->refresh();
        $this->assertNotEquals($originalConfirmTime, $subscription->confirmed_at);
    }

    /** @test */
    public function it_can_unsubscribe_already_unsubscribed_subscription()
    {
        // Arrange
        $originalUnsubTime = now()->subDays(1);
        $subscription = Subscription::factory()->create([
            'email' => 'test@example.com',
            'unsubscribed_at' => $originalUnsubTime
        ]);

        // Act
        $result = $this->repository->unsubscribe('test@example.com');

        // Assert
        $this->assertTrue($result);
        
        $subscription->refresh();
        $this->assertNotEquals($originalUnsubTime, $subscription->unsubscribed_at);
    }
}
