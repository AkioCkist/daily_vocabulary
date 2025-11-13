<?php

namespace Tests\Unit\Services;

use App\Repositories\Interfaces\UserWordRepositoryInterface;
use App\Services\UserVocabularyService;
use PHPUnit\Framework\TestCase;
use Mockery;

class SimpleUserVocabularyServiceTest extends TestCase
{
    protected $userWordRepository;
    protected $userVocabularyService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userWordRepository = Mockery::mock(UserWordRepositoryInterface::class);
        $this->userVocabularyService = new UserVocabularyService($this->userWordRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_is_constructed_with_user_word_repository()
    {
        $this->assertInstanceOf(UserVocabularyService::class, $this->userVocabularyService);
    }

    /** @test */
    public function it_has_required_methods()
    {
        $this->assertTrue(method_exists($this->userVocabularyService, 'addWord'));
        $this->assertTrue(method_exists($this->userVocabularyService, 'removeWord'));
        $this->assertTrue(method_exists($this->userVocabularyService, 'markLearned'));
        $this->assertTrue(method_exists($this->userVocabularyService, 'getUserWords'));
    }

    /** @test */
    public function repository_has_required_methods()
    {
        $this->assertTrue(method_exists($this->userWordRepository, 'addWordToUser'));
        $this->assertTrue(method_exists($this->userWordRepository, 'removeWord'));
        $this->assertTrue(method_exists($this->userWordRepository, 'updateStatus'));
        $this->assertTrue(method_exists($this->userWordRepository, 'getUserWords'));
    }

    /** @test */
    public function it_delegates_add_word_to_repository()
    {
        $mockUserWord = Mockery::mock('App\Models\UserWord');
        
        $this->userWordRepository
            ->shouldReceive('addWordToUser')
            ->with(1, 2)
            ->once()
            ->andReturn($mockUserWord);

        $result = $this->userVocabularyService->addWord(1, 2);
        $this->assertEquals($mockUserWord, $result);
    }

    /** @test */
    public function it_delegates_remove_word_to_repository()
    {
        $this->userWordRepository
            ->shouldReceive('removeWord')
            ->with(1, 2)
            ->once()
            ->andReturn(true);

        $result = $this->userVocabularyService->removeWord(1, 2);
        $this->assertTrue($result);
    }

    /** @test */
    public function it_delegates_mark_learned_to_repository()
    {
        $this->userWordRepository
            ->shouldReceive('updateStatus')
            ->with(1, 2, 'learned')
            ->once()
            ->andReturn(true);

        $result = $this->userVocabularyService->markLearned(1, 2);
        $this->assertTrue($result);
    }
}