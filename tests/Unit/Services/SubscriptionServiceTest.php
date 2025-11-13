<?php

namespace Tests\Unit\Services;

use App\Models\Subscription;
use App\Repositories\Interfaces\SubscriptionRepositoryInterface;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Mail;
use Mockery;

class SubscriptionServiceTest extends BaseTestCase
{
    protected $subscriptionRepository;
    protected $subscriptionService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->subscriptionRepository = Mockery::mock(SubscriptionRepositoryInterface::class);
        $this->subscriptionService = new SubscriptionService($this->subscriptionRepository);
        
        Mail::fake();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_creates_new_subscription_when_email_not_exists()
    {
        // Arrange
        $email = 'test@example.com';
        $subscription = Mockery::mock(Subscription::class);
        $subscription->email = $email;
        $subscription->confirmed_at = null;
        $subscription->unsubscribed_at = null;
        
        $this->subscriptionRepository
            ->shouldReceive('findByEmail')
            ->with($email)
            ->once()
            ->andReturn(null);
            
        $this->subscriptionRepository
            ->shouldReceive('create')
            ->with(['email' => $email])
            ->once()
            ->andReturn($subscription);

        // Act
        $result = $this->subscriptionService->subscribe($email);

        // Assert
        $this->assertEquals($subscription, $result);
        Mail::assertSent(function ($mail) use ($email) {
            return $mail->hasTo($email) && $mail->hasSubject('Confirm your Daily Vocabulary subscription');
        });
    }

    /** @test */
    public function it_returns_existing_active_subscription()
    {
        // Arrange
        $email = 'test@example.com';
        $existingSubscription = Mockery::mock(Subscription::class);
        $existingSubscription->email = $email;
        $existingSubscription->unsubscribed_at = null;
        
        $this->subscriptionRepository
            ->shouldReceive('findByEmail')
            ->with($email)
            ->once()
            ->andReturn($existingSubscription);

        // Act
        $result = $this->subscriptionService->subscribe($email);

        // Assert
        $this->assertEquals($existingSubscription, $result);
        Mail::assertNothingSent();
    }

    /** @test */
    public function it_creates_new_subscription_when_previous_was_unsubscribed()
    {
        // Arrange
        $email = 'test@example.com';
        $unsubscribedSubscription = Mockery::mock(Subscription::class);
        $unsubscribedSubscription->unsubscribed_at = now()->subDays(1);
        
        $newSubscription = Mockery::mock(Subscription::class);
        $newSubscription->email = $email;
        $newSubscription->confirmed_at = null;
        $newSubscription->unsubscribed_at = null;
        
        $this->subscriptionRepository
            ->shouldReceive('findByEmail')
            ->with($email)
            ->once()
            ->andReturn($unsubscribedSubscription);
            
        $this->subscriptionRepository
            ->shouldReceive('create')
            ->with(['email' => $email])
            ->once()
            ->andReturn($newSubscription);

        // Act
        $result = $this->subscriptionService->subscribe($email);

        // Assert
        $this->assertEquals($newSubscription, $result);
        Mail::assertSent(function ($mail) use ($email) {
            return $mail->hasTo($email) && $mail->hasSubject('Confirm your Daily Vocabulary subscription');
        });
    }

    /** @test */
    public function it_confirms_subscription()
    {
        // Arrange
        $email = 'test@example.com';
        
        $this->subscriptionRepository
            ->shouldReceive('confirm')
            ->with($email)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->subscriptionService->confirm($email);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_unsubscribes_subscription()
    {
        // Arrange
        $email = 'test@example.com';
        
        $this->subscriptionRepository
            ->shouldReceive('unsubscribe')
            ->with($email)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->subscriptionService->unsubscribe($email);

        // Assert
        $this->assertTrue($result);
    }
}
