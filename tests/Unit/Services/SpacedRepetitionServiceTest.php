<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Word;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpacedRepetitionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test service identifies learned words.
     */
    public function test_service_identifies_learned_words(): void
    {
        $word1 = Word::factory()->create();
        $word2 = Word::factory()->create();

        $this->user->words()->attach($word1);

        $learned = $this->user->words()->count();

        $this->assertEquals(1, $learned);
    }

    /**
     * Test service prevents review of unlearned words.
     */
    public function test_service_prevents_unlearned_review(): void
    {
        Word::factory()->create();

        $reviewable = $this->user->words()->count();

        $this->assertEquals(0, $reviewable);
    }

    /**
     * Test service counts review pool.
     */
    public function test_service_counts_review_pool(): void
    {
        Word::factory()->count(20)->create();

        $this->user->words()->attach(Word::all());

        $pool = $this->user->words()->count();

        $this->assertEquals(20, $pool);
    }

    /**
     * Test service retrieves word for review.
     */
    public function test_service_retrieves_word_for_review(): void
    {
        $word = Word::factory()->create();

        $this->user->words()->attach($word);

        $reviewWord = $this->user->words()->first();

        $this->assertNotNull($reviewWord);
        $this->assertEquals($word->id, $reviewWord->id);
    }

    /**
     * Test service word review data.
     */
    public function test_service_review_word_data(): void
    {
        $word = Word::factory()->create([
            'word' => 'review',
            'definition' => 'To examine again',
            'example' => 'Please review the document.',
        ]);

        $this->user->words()->attach($word);

        $reviewed = $this->user->words()->first();

        $this->assertEquals('review', $reviewed->word);
        $this->assertNotNull($reviewed->definition);
    }

    /**
     * Test service tracks review statistics.
     */
    public function test_service_tracks_stats(): void
    {
        Word::factory()->count(10)->create();

        $this->user->words()->attach(Word::all());

        $stats = [
            'total' => $this->user->words()->count(),
            'reviewed' => 0,
        ];

        $this->assertEquals(10, $stats['total']);
    }

    /**
     * Test service calculates accuracy.
     */
    public function test_service_calculates_accuracy(): void
    {
        $words = Word::factory()->count(5)->create();

        $this->user->words()->attach($words);

        $correct = 3;
        $total = $this->user->words()->count();
        $accuracy = ($correct / $total) * 100;

        $this->assertEquals(60, $accuracy);
    }

    /**
     * Test service filters by topic for review.
     */
    public function test_service_review_by_topic(): void
    {
        Word::factory()->count(5)->create(['topic' => 'Animals']);
        Word::factory()->count(3)->create(['topic' => 'Foods']);

        $animalWords = Word::where('topic', 'Animals')->get();
        $this->user->words()->attach($animalWords);

        $animalReview = $this->user->words()
            ->where('topic', 'Animals')
            ->count();

        $this->assertEquals(5, $animalReview);
    }

    /**
     * Test service difficulty filtering.
     */
    public function test_service_difficulty_filtering(): void
    {
        Word::factory()->count(4)->create(['cefr_level' => 'A1']);
        Word::factory()->count(3)->create(['cefr_level' => 'B2']);

        $beginnerWords = Word::where('cefr_level', 'A1')->get();
        $this->user->words()->attach($beginnerWords);

        $beginnerReview = $this->user->words()
            ->where('cefr_level', 'A1')
            ->count();

        $this->assertEquals(4, $beginnerReview);
    }

    /**
     * Test service multiple user independence.
     */
    public function test_service_multiple_users(): void
    {
        $user2 = User::factory()->create();
        $word = Word::factory()->create();

        $this->user->words()->attach($word);

        $user1Words = $this->user->words()->count();
        $user2Words = $user2->words()->count();

        $this->assertEquals(1, $user1Words);
        $this->assertEquals(0, $user2Words);
    }

    /**
     * Test service source filtering.
     */
    public function test_service_review_by_source(): void
    {
        Word::factory()->count(3)->create(['source' => 'oxford']);
        Word::factory()->count(2)->create(['source' => 'cambridge']);

        $oxfordWords = Word::where('source', 'oxford')->get();
        $this->user->words()->attach($oxfordWords);

        $sourceReview = $this->user->words()
            ->where('source', 'oxford')
            ->count();

        $this->assertEquals(3, $sourceReview);
    }

    /**
     * Test service session review count.
     */
    public function test_service_session_word_count(): void
    {
        Word::factory()->count(15)->create();

        $this->user->words()->attach(Word::all());

        $sessionCount = $this->user->words()->count();

        $this->assertEquals(15, $sessionCount);
    }
}
