<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Word;
use App\Repositories\Interfaces\WordRepositoryInterface;
use App\Services\WordService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class WordServiceTest extends TestCase
{
    use RefreshDatabase;

    private WordService $wordService;
    private $mockWordRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockWordRepository = Mockery::mock(WordRepositoryInterface::class);
        $this->wordService = new WordService($this->mockWordRepository);
    }

    public function test_filter_words_calls_repository_with_correct_parameters(): void
    {
        $filters = ['topic' => 'travel', 'cefr_level' => 'A2'];
        $perPage = 20;

        $this->mockWordRepository
            ->shouldReceive('filter')
            ->once()
            ->with($filters, $perPage)
            ->andReturn(collect([])->paginate($perPage));

        $result = $this->wordService->filterWords($filters, $perPage);

        $this->assertNotNull($result);
    }

    public function test_get_random_word_returns_word_from_repository(): void
    {
        $word = Word::factory()->make();
        $filters = ['topic' => 'travel'];

        $this->mockWordRepository
            ->shouldReceive('getRandomWord')
            ->once()
            ->with($filters)
            ->andReturn($word);

        $result = $this->wordService->getRandomWord($filters);

        $this->assertEquals($word, $result);
    }

    public function test_get_topics_returns_collection_from_repository(): void
    {
        $topics = collect(['travel', 'business', 'food']);

        $this->mockWordRepository
            ->shouldReceive('getTopics')
            ->once()
            ->andReturn($topics);

        $result = $this->wordService->getTopics();

        $this->assertEquals($topics, $result);
    }

    public function test_get_cefr_levels_returns_array_from_repository(): void
    {
        $levels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];

        $this->mockWordRepository
            ->shouldReceive('getCefrLevels')
            ->once()
            ->andReturn($levels);

        $result = $this->wordService->getCefrLevels();

        $this->assertEquals($levels, $result);
    }

    public function test_find_by_id_returns_word_from_repository(): void
    {
        $word = Word::factory()->make(['id' => 1]);

        $this->mockWordRepository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($word);

        $result = $this->wordService->findById(1);

        $this->assertEquals($word, $result);
    }

    public function test_get_new_words_for_user_returns_collection(): void
    {
        $userId = 1;
        $filters = ['topic' => 'travel'];
        $limit = 10;
        $words = Word::factory()->count(5)->make();

        $this->mockWordRepository
            ->shouldReceive('getNewWordsForUser')
            ->once()
            ->with($userId, $filters, $limit)
            ->andReturn(new Collection($words));

        $result = $this->wordService->getNewWordsForUser($userId, $filters, $limit);

        $this->assertCount(5, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
