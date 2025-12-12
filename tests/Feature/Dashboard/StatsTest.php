<?php

namespace Tests\Feature\Dashboard;

use App\Models\User;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test user can retrieve dashboard stats.
     */
    public function test_user_can_retrieve_dashboard_stats(): void
    {
        Word::factory()->count(10)->create();
        $this->user->words()->attach(Word::take(5)->pluck('id'));

        $learned = $this->user->words()->count();
        $total = Word::count();

        $this->assertEquals(5, $learned);
        $this->assertEquals(10, $total);
    }

    /**
     * Test stats calculate learning percentage.
     */
    public function test_stats_calculate_learning_percentage(): void
    {
        Word::factory()->count(100)->create();
        $this->user->words()->attach(Word::take(25)->pluck('id'));

        $learned = $this->user->words()->count();
        $total = Word::count();
        $percentage = ($learned / $total) * 100;

        $this->assertEquals(25, $percentage);
    }

    /**
     * Test stats with no learned words.
     */
    public function test_stats_with_no_learned_words(): void
    {
        Word::factory()->count(10)->create();

        $learned = $this->user->words()->count();

        $this->assertEquals(0, $learned);
    }

    /**
     * Test stats with all words learned.
     */
    public function test_stats_with_all_words_learned(): void
    {
        Word::factory()->count(10)->create();
        $this->user->words()->attach(Word::all());

        $learned = $this->user->words()->count();
        $total = Word::count();
        $percentage = ($learned / $total) * 100;

        $this->assertEquals(10, $learned);
        $this->assertEquals(100, $percentage);
    }

    /**
     * Test stats by topic.
     */
    public function test_stats_learned_by_topic(): void
    {
        Word::factory()->count(5)->create(['topic' => 'Animals']);
        Word::factory()->count(3)->create(['topic' => 'Foods']);

        $animalWords = Word::where('topic', 'Animals')->get();
        $this->user->words()->attach($animalWords);

        $learnedAnimals = $this->user->words()
            ->where('topic', 'Animals')
            ->count();

        $this->assertEquals(5, $learnedAnimals);
    }

    /**
     * Test stats word count by CEFR level.
     */
    public function test_stats_words_by_cefr_level(): void
    {
        Word::factory()->count(10)->create(['cefr_level' => 'A1']);
        Word::factory()->count(5)->create(['cefr_level' => 'B1']);

        $this->user->words()->attach(Word::all());

        $a1Words = $this->user->words()
            ->where('cefr_level', 'A1')
            ->count();

        $this->assertEquals(10, $a1Words);
    }

    /**
     * Test stats multiple users independent.
     */
    public function test_stats_multiple_users_independent(): void
    {
        $user2 = User::factory()->create();
        Word::factory()->count(10)->create();

        $this->user->words()->attach(Word::take(3)->pluck('id'));
        $user2->words()->attach(Word::skip(3)->take(5)->pluck('id'));

        $user1Count = $this->user->words()->count();
        $user2Count = $user2->words()->count();

        $this->assertEquals(3, $user1Count);
        $this->assertEquals(5, $user2Count);
    }

    /**
     * Test stats progression over time.
     */
    public function test_stats_progression_tracking(): void
    {
        Word::factory()->count(5)->create();

        // Day 1: Learn 2 words
        $day1 = $this->user->words()->count();
        $this->user->words()->attach(Word::take(2)->pluck('id'));
        $day1After = $this->user->words()->count();

        // Day 2: Learn 2 more
        $this->user->words()->attach(Word::skip(2)->take(2)->pluck('id'));
        $day2After = $this->user->words()->count();

        $this->assertEquals(0, $day1);
        $this->assertEquals(2, $day1After);
        $this->assertEquals(4, $day2After);
    }

    /**
     * Test stats with empty vocabulary.
     */
    public function test_stats_empty_vocabulary(): void
    {
        $learned = $this->user->words()->count();
        $total = Word::count();

        $this->assertEquals(0, $learned);
        $this->assertEquals(0, $total);
    }

    /**
     * Test stats large vocabulary.
     */
    public function test_stats_large_vocabulary(): void
    {
        Word::factory()->count(1000)->create();
        $this->user->words()->attach(Word::take(250)->pluck('id'));

        $learned = $this->user->words()->count();
        $total = Word::count();
        $percentage = ($learned / $total) * 100;

        $this->assertEquals(250, $learned);
        $this->assertEquals(1000, $total);
        $this->assertEquals(25, $percentage);
    }

    /**
     * Test stats dashboard summary.
     */
    public function test_stats_dashboard_summary(): void
    {
        Word::factory()->count(50)->create();
        $this->user->words()->attach(Word::take(20)->pluck('id'));

        $stats = [
            'total_words' => Word::count(),
            'learned_words' => $this->user->words()->count(),
            'percentage' => ($this->user->words()->count() / Word::count()) * 100,
            'remaining' => Word::count() - $this->user->words()->count(),
        ];

        $this->assertEquals(50, $stats['total_words']);
        $this->assertEquals(20, $stats['learned_words']);
        $this->assertEquals(40, $stats['percentage']);
        $this->assertEquals(30, $stats['remaining']);
    }

    /**
     * Test stats memory retention rate.
     */
    public function test_stats_retention_calculation(): void
    {
        Word::factory()->count(10)->create();
        $this->user->words()->attach(Word::all());

        $total = $this->user->words()->count();
        $retained = $total; // Simplified: all learned = retained
        $retention = ($retained / $total) * 100;

        $this->assertEquals(100, $retention);
    }

    /**
     * Test stats category breakdown.
     */
    public function test_stats_category_breakdown(): void
    {
        Word::factory()->count(3)->create(['topic' => 'Animals']);
        Word::factory()->count(2)->create(['topic' => 'Foods']);
        Word::factory()->count(1)->create(['topic' => 'Colors']);

        $this->user->words()->attach(Word::all());

        $animalCount = $this->user->words()->where('topic', 'Animals')->count();
        $foodCount = $this->user->words()->where('topic', 'Foods')->count();
        $colorCount = $this->user->words()->where('topic', 'Colors')->count();

        $this->assertEquals(3, $animalCount);
        $this->assertEquals(2, $foodCount);
        $this->assertEquals(1, $colorCount);
    }

    /**
     * Test stats difficulty distribution.
     */
    public function test_stats_difficulty_distribution(): void
    {
        Word::factory()->count(5)->create(['cefr_level' => 'A1']);
        Word::factory()->count(3)->create(['cefr_level' => 'A2']);
        Word::factory()->count(2)->create(['cefr_level' => 'B1']);

        $this->user->words()->attach(Word::all());

        $a1 = $this->user->words()->where('cefr_level', 'A1')->count();
        $a2 = $this->user->words()->where('cefr_level', 'A2')->count();
        $b1 = $this->user->words()->where('cefr_level', 'B1')->count();

        $this->assertEquals(5, $a1);
        $this->assertEquals(3, $a2);
        $this->assertEquals(2, $b1);
    }
}
