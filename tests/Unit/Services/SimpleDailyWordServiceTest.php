<?php

namespace Tests\Unit\Services;

use App\Repositories\Interfaces\WordRepositoryInterface;
use App\Services\DailyWordService;
use PHPUnit\Framework\TestCase;
use Mockery;

class SimpleDailyWordServiceTest extends TestCase
{
    protected $wordRepository;
    protected $dailyWordService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->wordRepository = Mockery::mock(WordRepositoryInterface::class);
        $this->dailyWordService = new DailyWordService($this->wordRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_is_constructed_with_word_repository()
    {
        $this->assertInstanceOf(DailyWordService::class, $this->dailyWordService);
    }

    /** @test */
    public function it_has_get_today_word_method()
    {
        $this->assertTrue(method_exists($this->dailyWordService, 'getTodayWord'));
    }

    /** @test */
    public function word_repository_has_required_methods()
    {
        $this->assertTrue(method_exists($this->wordRepository, 'getRandomWord'));
        $this->assertTrue(method_exists($this->wordRepository, 'getRandomWordExcluding'));
    }
}