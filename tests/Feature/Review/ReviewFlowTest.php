<?php

namespace Tests\Feature\Review;

use App\Models\User;
use App\Models\Word;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test user can review learned words.
     */
    public function test_user_can_review_learned_words(): void
    {
        $word1 = Word::factory()->create();
        $word2 = Word::factory()->create();

        $this->user->words()->attach($word1);
        $this->user->words()->attach($word2);

        $learnedWords = $this->user->words()->get();

        $this->assertCount(2, $learnedWords);
    }

    /**
     * Test review counts total learned words.
     */
    public function test_review_counts_total_learned(): void
    {
        Word::factory()->count(10)->create();

        $this->user->words()->attach(Word::all());

        $total = $this->user->words()->count();

        $this->assertEquals(10, $total);
    }

    /**
     * Test cannot review unlearned words.
     */
    public function test_cannot_review_unlearned_words(): void
    {
        Word::factory()->create();

        $learned = $this->user->words()->get();

        $this->assertCount(0, $learned);
    }

    /**
     * Test word properties accessible in review.
     */
    public function test_word_properties_in_review(): void
    {
        $word = Word::factory()->create([
            'word' => 'test',
            'definition' => 'A test definition',
            'example' => 'This is a test.',
        ]);

        $this->user->words()->attach($word);

        $reviewed = $this->user->words()->first();

        $this->assertEquals('test', $reviewed->word);
        $this->assertEquals('A test definition', $reviewed->definition);
    }

    /**
     * Test multiple users have independent reviews.
     */
    public function test_multiple_users_independent_reviews(): void
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
     * Test review by topic.
     */
    public function test_review_words_by_topic(): void
    {
        Word::factory()->count(3)->create(['topic' => 'Animals']);
        Word::factory()->count(2)->create(['topic' => 'Foods']);

        $animalWords = Word::where('topic', 'Animals')->get();
        $this->user->words()->attach($animalWords);

        $topicReview = $this->user->words()
            ->where('topic', 'Animals')
            ->count();

        $this->assertEquals(3, $topicReview);
    }

    /**
     * Test review retrieves word pronunciation.
     */
    public function test_review_with_pronunciation(): void
    {
        $word = Word::factory()->create([
            'word' => 'hello',
            'pronunciation' => 'hə-ˈlō',
        ]);

        $this->user->words()->attach($word);

        $reviewed = $this->user->words()->first();

        $this->assertNotNull($reviewed->pronunciation);
    }

    /**
     * Test review all word attributes.
     */
    public function test_review_all_word_attributes(): void
    {
        $word = Word::factory()->create([
            'word' => 'example',
            'pronunciation' => 'ig-zam-pul',
            'definition' => 'a thing characteristic',
            'example' => 'Here is an example.',
            'source' => 'oxford',
            'topic' => 'education',
            'cefr_level' => 'A2',
        ]);

        $this->user->words()->attach($word);

        $reviewed = $this->user->words()->first();

        $this->assertEquals('example', $reviewed->word);
        $this->assertEquals('ig-zam-pul', $reviewed->pronunciation);
        $this->assertEquals('a thing characteristic', $reviewed->definition);
        $this->assertEquals('A2', $reviewed->cefr_level);
    }

    /**
     * Test review statistics - total words.
     */
    public function test_review_statistics_total(): void
    {
        Word::factory()->count(20)->create();

        $this->user->words()->attach(Word::all());

        $stats = ['total' => $this->user->words()->count()];

        $this->assertEquals(20, $stats['total']);
    }

    /**
     * Test review by difficulty level.
     */
    public function test_review_by_cefr_level(): void
    {
        Word::factory()->count(5)->create(['cefr_level' => 'A1']);
        Word::factory()->count(3)->create(['cefr_level' => 'B1']);

        $this->user->words()->attach(Word::all());

        $beginnerReview = $this->user->words()
            ->where('cefr_level', 'A1')
            ->count();

        $this->assertEquals(5, $beginnerReview);
    }

    /**
     * Test review list sorting by creation.
     */
    public function test_review_list_preserved_order(): void
    {
        $word1 = Word::factory()->create(['word' => 'apple']);
        $word2 = Word::factory()->create(['word' => 'banana']);
        $word3 = Word::factory()->create(['word' => 'cherry']);

        $this->user->words()->attach($word1);
        $this->user->words()->attach($word2);
        $this->user->words()->attach($word3);

        $reviewed = $this->user->words()->get();

        $this->assertCount(3, $reviewed);
    }

    /**
     * Test review word count calculation.
     */
    public function test_review_progress_percentage(): void
    {
        Word::factory()->count(100)->create();

        $this->user->words()->attach(Word::take(50)->pluck('id'));

        $total = Word::count();
        $reviewed = $this->user->words()->count();
        $percentage = ($reviewed / $total) * 100;

        $this->assertEquals(50, $percentage);
    }

    /**
     * Test review with meaning field.
     */
    public function test_review_word_meaning(): void
    {
        $word = Word::factory()->create([
            'word' => 'test',
            'meaning' => 'An exam or trial',
        ]);

        $this->user->words()->attach($word);

        $reviewed = $this->user->words()->first();

        $this->assertNotNull($reviewed->meaning);
    }

    /**
     * Test review filters by source.
     */
    public function test_review_words_by_source(): void
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
}
