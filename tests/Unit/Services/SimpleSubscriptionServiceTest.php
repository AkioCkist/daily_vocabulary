<?php

namespace Tests\Unit\Services;

use App\Repositories\Interfaces\SubscriptionRepositoryInterface;
use App\Services\SubscriptionService;
use PHPUnit\Framework\TestCase;
use Mockery;

class SimpleSubscriptionServiceTest extends TestCase
{
    protected $subscriptionRepository;
    protected $subscriptionService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->subscriptionRepository = Mockery::mock(SubscriptionRepositoryInterface::class);
        $this->subscriptionService = new SubscriptionService($this->subscriptionRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_is_constructed_with_subscription_repository()
    {
        $this->assertInstanceOf(SubscriptionService::class, $this->subscriptionService);
    }

    /** @test */
    public function it_has_required_methods()
    {
        $this->assertTrue(method_exists($this->subscriptionService, 'subscribe'));
        $this->assertTrue(method_exists($this->subscriptionService, 'confirm'));
        $this->assertTrue(method_exists($this->subscriptionService, 'unsubscribe'));
    }

    /** @test */
    public function repository_has_required_methods()
    {
        $this->assertTrue(method_exists($this->subscriptionRepository, 'findByEmail'));
        $this->assertTrue(method_exists($this->subscriptionRepository, 'create'));
        $this->assertTrue(method_exists($this->subscriptionRepository, 'confirm'));
        $this->assertTrue(method_exists($this->subscriptionRepository, 'unsubscribe'));
    }
}