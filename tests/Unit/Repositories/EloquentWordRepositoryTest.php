<?php

namespace Tests\Unit\Repositories;

use App\Models\Word;
use App\Repositories\Eloquent\EloquentWordRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentWordRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentWordRepository();
    }

    /** @test */
    public function it_gets_random_word()
    {
        // Arrange
        Word::factory()->count(5)->create();

        // Act
        $word = $this->repository->getRandomWord();

        // Assert
        $this->assertInstanceOf(Word::class, $word);
        $this->assertDatabaseHas('words', ['id' => $word->id]);
    }

    /** @test */
    public function it_returns_null_when_no_words_exist()
    {
        // Act
        $word = $this->repository->getRandomWord();

        // Assert
        $this->assertNull($word);
    }

    /** @test */
    public function it_gets_random_word_excluding_specific_ids()
    {
        // Arrange
        $word1 = Word::factory()->create();
        $word2 = Word::factory()->create();
        $word3 = Word::factory()->create();
        
        $excludeIds = [$word1->id, $word2->id];

        // Act
        $word = $this->repository->getRandomWordExcluding($excludeIds);

        // Assert
        $this->assertInstanceOf(Word::class, $word);
        $this->assertEquals($word3->id, $word->id);
        $this->assertNotContains($word->id, $excludeIds);
    }

    /** @test */
    public function it_returns_null_when_all_words_excluded()
    {
        // Arrange
        $word1 = Word::factory()->create();
        $word2 = Word::factory()->create();
        
        $excludeIds = [$word1->id, $word2->id];

        // Act
        $word = $this->repository->getRandomWordExcluding($excludeIds);

        // Assert
        $this->assertNull($word);
    }

    /** @test */
    public function it_finds_word_by_id()
    {
        // Arrange
        $word = Word::factory()->create();

        // Act
        $foundWord = $this->repository->findById($word->id);

        // Assert
        $this->assertInstanceOf(Word::class, $foundWord);
        $this->assertEquals($word->id, $foundWord->id);
    }

    /** @test */
    public function it_returns_null_when_word_not_found()
    {
        // Act
        $word = $this->repository->findById(999);

        // Assert
        $this->assertNull($word);
    }

    /** @test */
    public function it_searches_words_by_word_content()
    {
        // Arrange
        Word::factory()->create(['word' => 'hello', 'definition' => 'greeting']);
        Word::factory()->create(['word' => 'world', 'definition' => 'planet']);
        Word::factory()->create(['word' => 'test', 'definition' => 'examination']);

        // Act
        $results = $this->repository->search('hel');

        // Assert
        $this->assertCount(1, $results);
        $this->assertEquals('hello', $results->first()->word);
    }

    /** @test */
    public function it_searches_words_by_definition_content()
    {
        // Arrange
        Word::factory()->create(['word' => 'hello', 'definition' => 'greeting']);
        Word::factory()->create(['word' => 'world', 'definition' => 'planet']);
        Word::factory()->create(['word' => 'test', 'definition' => 'examination']);

        // Act
        $results = $this->repository->search('planet');

        // Assert
        $this->assertCount(1, $results);
        $this->assertEquals('world', $results->first()->word);
    }

    /** @test */
    public function it_limits_search_results()
    {
        // Arrange
        Word::factory()->count(25)->create([
            'word' => 'test',
            'definition' => 'test definition'
        ]);

        // Act
        $results = $this->repository->search('test', 10);

        // Assert
        $this->assertCount(10, $results);
    }

    /** @test */
    public function it_gets_all_words_with_limit()
    {
        // Arrange
        Word::factory()->count(100)->create();

        // Act
        $results = $this->repository->all(20);

        // Assert
        $this->assertCount(20, $results);
    }

    /** @test */
    public function it_gets_all_words_with_default_limit()
    {
        // Arrange
        Word::factory()->count(60)->create();

        // Act
        $results = $this->repository->all();

        // Assert
        $this->assertCount(50, $results); // Default limit is 50
    }
}
