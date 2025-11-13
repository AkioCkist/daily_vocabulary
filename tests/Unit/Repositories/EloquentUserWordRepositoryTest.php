<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Models\UserWord;
use App\Models\Word;
use App\Repositories\Eloquent\EloquentUserWordRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentUserWordRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $user;
    protected $word;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentUserWordRepository();
        $this->user = User::factory()->create();
        $this->word = Word::factory()->create();
    }

    /** @test */
    public function it_adds_new_word_to_user()
    {
        // Act
        $userWord = $this->repository->addWordToUser($this->user->id, $this->word->id);

        // Assert
        $this->assertInstanceOf(UserWord::class, $userWord);
        $this->assertEquals($this->user->id, $userWord->user_id);
        $this->assertEquals($this->word->id, $userWord->word_id);
        $this->assertEquals('saved', $userWord->status);
        
        $this->assertDatabaseHas('user_words', [
            'user_id' => $this->user->id,
            'word_id' => $this->word->id,
            'status' => 'saved'
        ]);
    }

    /** @test */
    public function it_updates_existing_user_word()
    {
        // Arrange
        UserWord::create([
            'user_id' => $this->user->id,
            'word_id' => $this->word->id,
            'status' => 'saved'
        ]);

        // Act
        $userWord = $this->repository->addWordToUser($this->user->id, $this->word->id, 'learned');

        // Assert
        $this->assertEquals('learned', $userWord->status);
        $this->assertDatabaseHas('user_words', [
            'user_id' => $this->user->id,
            'word_id' => $this->word->id,
            'status' => 'learned'
        ]);
        
        // Ensure only one record exists
        $this->assertEquals(1, UserWord::where('user_id', $this->user->id)
            ->where('word_id', $this->word->id)
            ->count());
    }

    /** @test */
    public function it_adds_word_with_custom_status()
    {
        // Act
        $userWord = $this->repository->addWordToUser($this->user->id, $this->word->id, 'learned');

        // Assert
        $this->assertEquals('learned', $userWord->status);
    }

    /** @test */
    public function it_removes_word_from_user()
    {
        // Arrange
        UserWord::create([
            'user_id' => $this->user->id,
            'word_id' => $this->word->id,
            'status' => 'saved'
        ]);

        // Act
        $result = $this->repository->removeWord($this->user->id, $this->word->id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('user_words', [
            'user_id' => $this->user->id,
            'word_id' => $this->word->id
        ]);
    }

    /** @test */
    public function it_returns_false_when_removing_non_existent_word()
    {
        // Act
        $result = $this->repository->removeWord($this->user->id, $this->word->id);

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    public function it_updates_word_status()
    {
        // Arrange
        UserWord::create([
            'user_id' => $this->user->id,
            'word_id' => $this->word->id,
            'status' => 'saved'
        ]);

        // Act
        $result = $this->repository->updateStatus($this->user->id, $this->word->id, 'learned');

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('user_words', [
            'user_id' => $this->user->id,
            'word_id' => $this->word->id,
            'status' => 'learned'
        ]);
    }

    /** @test */
    public function it_returns_false_when_updating_non_existent_word_status()
    {
        // Act
        $result = $this->repository->updateStatus($this->user->id, $this->word->id, 'learned');

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    public function it_gets_all_user_words()
    {
        // Arrange
        $word2 = Word::factory()->create();
        UserWord::create([
            'user_id' => $this->user->id,
            'word_id' => $this->word->id,
            'status' => 'saved'
        ]);
        UserWord::create([
            'user_id' => $this->user->id,
            'word_id' => $word2->id,
            'status' => 'learned'
        ]);

        // Act
        $userWords = $this->repository->getUserWords($this->user->id);

        // Assert
        $this->assertCount(2, $userWords);
        $this->assertTrue($userWords->first()->relationLoaded('word'));
    }

    /** @test */
    public function it_gets_user_words_by_status()
    {
        // Arrange
        $word2 = Word::factory()->create();
        UserWord::create([
            'user_id' => $this->user->id,
            'word_id' => $this->word->id,
            'status' => 'saved'
        ]);
        UserWord::create([
            'user_id' => $this->user->id,
            'word_id' => $word2->id,
            'status' => 'learned'
        ]);

        // Act
        $learnedWords = $this->repository->getUserWords($this->user->id, 'learned');
        $savedWords = $this->repository->getUserWords($this->user->id, 'saved');

        // Assert
        $this->assertCount(1, $learnedWords);
        $this->assertCount(1, $savedWords);
        $this->assertEquals('learned', $learnedWords->first()->status);
        $this->assertEquals('saved', $savedWords->first()->status);
    }

    /** @test */
    public function it_returns_empty_collection_for_user_with_no_words()
    {
        // Act
        $userWords = $this->repository->getUserWords($this->user->id);

        // Assert
        $this->assertCount(0, $userWords);
        $this->assertTrue($userWords->isEmpty());
    }
}
