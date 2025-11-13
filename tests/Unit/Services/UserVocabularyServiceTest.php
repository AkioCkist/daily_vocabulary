<?php

namespace Tests\Unit\Services;

use App\Models\UserWord;
use App\Repositories\Interfaces\UserWordRepositoryInterface;
use App\Services\UserVocabularyService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;

class UserVocabularyServiceTest extends BaseTestCase
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
    public function it_adds_word_to_user_vocabulary()
    {
        // Arrange
        $userId = 1;
        $wordId = 10;
        $userWord = Mockery::mock(UserWord::class);
        $userWord->user_id = $userId;
        $userWord->word_id = $wordId;
        $userWord->status = 'saved';
        
        $this->userWordRepository
            ->shouldReceive('addWordToUser')
            ->with($userId, $wordId)
            ->once()
            ->andReturn($userWord);

        // Act
        $result = $this->userVocabularyService->addWord($userId, $wordId);

        // Assert
        $this->assertEquals($userWord, $result);
    }

    /** @test */
    public function it_removes_word_from_user_vocabulary()
    {
        // Arrange
        $userId = 1;
        $wordId = 10;
        
        $this->userWordRepository
            ->shouldReceive('removeWord')
            ->with($userId, $wordId)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->userVocabularyService->removeWord($userId, $wordId);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_marks_word_as_learned()
    {
        // Arrange
        $userId = 1;
        $wordId = 10;
        
        $this->userWordRepository
            ->shouldReceive('updateStatus')
            ->with($userId, $wordId, 'learned')
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->userVocabularyService->markLearned($userId, $wordId);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_gets_all_user_words()
    {
        // Arrange
        $userId = 1;
        $userWord1 = Mockery::mock(UserWord::class);
        $userWord1->user_id = $userId;
        $userWord1->status = 'saved';
        
        $userWord2 = Mockery::mock(UserWord::class);
        $userWord2->user_id = $userId;
        $userWord2->status = 'learned';
        
        $userWords = new Collection([$userWord1, $userWord2]);
        
        $this->userWordRepository
            ->shouldReceive('getUserWords')
            ->with($userId, null)
            ->once()
            ->andReturn($userWords);

        // Act
        $result = $this->userVocabularyService->getUserWords($userId);

        // Assert
        $this->assertEquals($userWords, $result);
        $this->assertCount(2, $result);
    }

    /** @test */
    public function it_gets_user_words_by_status()
    {
        // Arrange
        $userId = 1;
        $status = 'learned';
        
        $userWord = Mockery::mock(UserWord::class);
        $userWord->user_id = $userId;
        $userWord->status = 'learned';
        
        $userWords = new Collection([$userWord]);
        
        $this->userWordRepository
            ->shouldReceive('getUserWords')
            ->with($userId, $status)
            ->once()
            ->andReturn($userWords);

        // Act
        $result = $this->userVocabularyService->getUserWords($userId, $status);

        // Assert
        $this->assertEquals($userWords, $result);
        $this->assertCount(1, $result);
    }
}
