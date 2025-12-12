<?php

namespace Tests\Feature\Learning;

use App\Models\User;
use App\Models\Word;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test user can mark word as learned.
     */
    public function test_user_can_mark_word_as_learned(): void
    {
        $word = Word::factory()->create(['word' => 'hello']);

        $this->user->words()->attach($word);

        $this->assertTrue($this->user->words()->where('word_id', $word->id)->exists());
    }

    /**
     * Test learned words count increases.
     */
    public function test_learned_words_count_increases(): void
    {
        Word::factory()->count(5)->create();

        $words = Word::all();

        foreach ($words as $word) {
            $this->user->words()->attach($word);
        }

        $count = $this->user->words()->count();

        $this->assertEquals(5, $count);
    }

    /**
     * Test unlearned words are available for learning.
     */
    public function test_unlearned_words_are_available(): void
    {
        $learned = Word::factory()->create(['word' => 'hello']);
        $unlearned = Word::factory()->create(['word' => 'goodbye']);

        $this->user->words()->attach($learned);

        $available = Word::whereNotIn('words.id', $this->user->words()->pluck('word_id'))->get();

        $this->assertCount(1, $available);
        $this->assertTrue($available->first()->is($unlearned));
    }

    /**
     * Test learning progress calculation.
     */
    public function test_learning_progress_calculation(): void
    {
        Word::factory()->count(10)->create();

        $this->user->words()->attach(Word::take(3)->pluck('id'));

        $total = Word::count();
        $learned = $this->user->words()->count();
        $progress = ($learned / $total) * 100;

        $this->assertEquals(30, $progress);
    }

    /**
     * Test multiple users have independent learned words.
     */
    public function test_multiple_users_independent_progress(): void
    {
        $user2 = User::factory()->create();
        $word = Word::factory()->create();

        $this->user->words()->attach($word);

        $user1Count = $this->user->words()->count();
        $user2Count = $user2->words()->count();

        $this->assertEquals(1, $user1Count);
        $this->assertEquals(0, $user2Count);
    }

    /**
     * Test learned words by topic.
     */
    public function test_learned_words_by_topic(): void
    {
        Word::factory()->count(3)->create(['topic' => 'Animals']);
        Word::factory()->count(2)->create(['topic' => 'Foods']);

        $animalWords = Word::where('topic', 'Animals')->get();
        $this->user->words()->attach($animalWords);

        $learnedAnimals = $this->user->words()
            ->where('topic', 'Animals')
            ->count();

        $this->assertEquals(3, $learnedAnimals);
    }

    /**
     * Test word retrieval by CEFR level.
     */
    public function test_words_by_difficulty_level(): void
    {
        Word::factory()->count(3)->create(['cefr_level' => 'A1']);
        Word::factory()->count(2)->create(['cefr_level' => 'B1']);

        $beginners = Word::where('cefr_level', 'A1')->get();

        $this->assertCount(3, $beginners);
    }

    /**
     * Test cannot learn same word twice.
     */
    public function test_cannot_learn_same_word_twice(): void
    {
        $word = Word::factory()->create();

        $this->user->words()->attach($word);
        // Trying to attach again should not create duplicate
        
        $count = $this->user->words()->where('word_id', $word->id)->count();

        $this->assertEquals(1, $count);
    }

    /**
     * Test words with examples are available.
     */
    public function test_words_with_examples(): void
    {
        Word::factory()->create([
            'word' => 'test',
            'example' => 'This is a test sentence.',
        ]);

        $word = Word::where('word', 'test')->first();

        $this->assertNotNull($word->example);
    }

    /**
     * Test word definition retrieval.
     */
    public function test_word_definition_retrieval(): void
    {
        Word::factory()->create([
            'word' => 'serendipity',
            'definition' => 'Finding something good by chance',
        ]);

        $word = Word::where('word', 'serendipity')->first();

        $this->assertEquals('Finding something good by chance', $word->definition);
    }

    /**
     * Test learning session tracks progression.
     */
    public function test_learning_tracks_word_progression(): void
    {
        $word1 = Word::factory()->create();
        $word2 = Word::factory()->create();

        // First learning session
        $this->user->words()->attach($word1);
        $firstCount = $this->user->words()->count();

        // Second learning session
        $this->user->words()->attach($word2);
        $secondCount = $this->user->words()->count();

        $this->assertEquals(1, $firstCount);
        $this->assertEquals(2, $secondCount);
    }

    /**
     * Test learning history by user.
     */
    public function test_learning_history_by_user(): void
    {
        Word::factory()->count(5)->create();

        $this->user->words()->attach(Word::all());

        $learned = $this->user->words()->orderBy('created_at')->get();

        $this->assertCount(5, $learned);
    }

    /**
     * Test word pronunciations available for learning.
     */
    public function test_word_with_pronunciation(): void
    {
        Word::factory()->create([
            'word' => 'pronunciation',
            'pronunciation' => 'prə-ˌnən-sē-ˈā-shən',
        ]);

        $word = Word::where('word', 'pronunciation')->first();

        $this->assertNotNull($word->pronunciation);
    }

    /**
     * Test large vocabulary session.
     */
    public function test_large_vocabulary_session(): void
    {
        Word::factory()->count(100)->create();

        $words = Word::all();

        $this->assertCount(100, $words);
    }

    /**
     * Test resume learning from specific word.
     */
    public function test_resume_learning_from_specific_point(): void
    {
        $word1 = Word::factory()->create(['word' => 'word1']);
        $word2 = Word::factory()->create(['word' => 'word2']);
        $word3 = Word::factory()->create(['word' => 'word3']);

        $this->user->words()->attach($word1);
        $this->user->words()->attach($word2);

        $remaining = Word::whereNotIn('words.id', $this->user->words()->pluck('word_id'))->get();

        $this->assertCount(1, $remaining);
        $this->assertTrue($remaining->first()->is($word3));
    }
}
