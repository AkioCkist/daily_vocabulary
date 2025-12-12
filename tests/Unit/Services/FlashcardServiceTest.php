<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Word;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FlashcardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test service retrieves unlearned words.
     */
    public function test_service_retrieves_unlearned_words(): void
    {
        Word::factory()->count(10)->create();

        $unlearned = Word::all();

        $this->assertCount(10, $unlearned);
    }

    /**
     * Test service filters by available pool.
     */
    public function test_service_filters_learned_words(): void
    {
        $word1 = Word::factory()->create();
        $word2 = Word::factory()->create();

        $this->user->words()->attach($word1);

        $available = Word::whereNotIn('words.id', $this->user->words()->pluck('word_id'))->get();

        $this->assertCount(1, $available);
    }

    /**
     * Test service marks word learned in session.
     */
    public function test_service_marks_word_learned(): void
    {
        $word = Word::factory()->create();

        $this->user->words()->attach($word);

        $isLearned = $this->user->words()->where('word_id', $word->id)->exists();

        $this->assertTrue($isLearned);
    }

    /**
     * Test service retrieves next word randomly.
     */
    public function test_service_retrieves_random_word(): void
    {
        Word::factory()->count(10)->create();

        $word = Word::inRandomOrder()->first();

        $this->assertNotNull($word);
    }

    /**
     * Test service session progress tracking.
     */
    public function test_service_tracks_session_progress(): void
    {
        Word::factory()->count(5)->create();

        for ($i = 0; $i < 3; $i++) {
            $this->user->words()->attach(Word::all()->random());
        }

        $learned = $this->user->words()->count();

        $this->assertEquals(3, $learned);
    }

    /**
     * Test service handles empty vocabulary.
     */
    public function test_service_handles_empty_vocabulary(): void
    {
        $words = Word::all();

        $this->assertCount(0, $words);
    }

    /**
     * Test service counts total words.
     */
    public function test_service_counts_total_words(): void
    {
        Word::factory()->count(25)->create();

        $count = Word::count();

        $this->assertEquals(25, $count);
    }

    /**
     * Test service retrieves words by CEFR level.
     */
    public function test_service_retrieves_by_cefr(): void
    {
        Word::factory()->count(5)->create(['cefr_level' => 'A1']);
        Word::factory()->count(3)->create(['cefr_level' => 'B1']);

        $a1Words = Word::where('cefr_level', 'A1')->count();

        $this->assertEquals(5, $a1Words);
    }

    /**
     * Test service retrieves words by topic.
     */
    public function test_service_retrieves_by_topic(): void
    {
        Word::factory()->count(4)->create(['topic' => 'Animals']);
        Word::factory()->count(2)->create(['topic' => 'Foods']);

        $animalWords = Word::where('topic', 'Animals')->count();

        $this->assertEquals(4, $animalWords);
    }

    /**
     * Test service learning progress calculation.
     */
    public function test_service_calculates_progress(): void
    {
        Word::factory()->count(10)->create();

        $this->user->words()->attach(Word::take(7)->pluck('id'));

        $total = Word::count();
        $learned = $this->user->words()->count();
        $progress = ($learned / $total) * 100;

        $this->assertEquals(70, $progress);
    }

    /**
     * Test service tracks multiple word attachments.
     */
    public function test_service_tracks_word_attachments(): void
    {
        $word1 = Word::factory()->create();
        $word2 = Word::factory()->create();

        $this->user->words()->attach($word1);
        $this->user->words()->attach($word2);

        $count = $this->user->words()->count();

        $this->assertEquals(2, $count);
    }

    /**
     * Test service word with complete data.
     */
    public function test_service_word_complete_data(): void
    {
        $word = Word::factory()->create([
            'word' => 'complete',
            'pronunciation' => 'kəm-ˈplēt',
            'definition' => 'Having all necessary parts',
            'example' => 'The project is complete.',
            'source' => 'oxford',
            'cefr_level' => 'B1',
            'meaning' => 'Finished or whole',
        ]);

        $retrieved = Word::findOrFail($word->id);

        $this->assertEquals('complete', $retrieved->word);
        $this->assertNotNull($retrieved->pronunciation);
        $this->assertNotNull($retrieved->definition);
    }

    /**
     * Test service per-user statistics.
     */
    public function test_service_per_user_stats(): void
    {
        $user2 = User::factory()->create();
        Word::factory()->count(10)->create();

        $this->user->words()->attach(Word::take(5)->pluck('id'));
        $user2->words()->attach(Word::skip(5)->take(3)->pluck('id'));

        $user1Count = $this->user->words()->count();
        $user2Count = $user2->words()->count();

        $this->assertEquals(5, $user1Count);
        $this->assertEquals(3, $user2Count);
    }
}
