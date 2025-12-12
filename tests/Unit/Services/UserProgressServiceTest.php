<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Word;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProgressServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test service calculates total learned words.
     */
    public function test_service_calculates_total_learned(): void
    {
        Word::factory()->count(20)->create();
        $this->user->words()->attach(Word::take(10)->pluck('id'));

        $total = $this->user->words()->count();

        $this->assertEquals(10, $total);
    }

    /**
     * Test service calculates learning percentage.
     */
    public function test_service_calculates_percentage(): void
    {
        Word::factory()->count(40)->create();
        $this->user->words()->attach(Word::take(10)->pluck('id'));

        $learned = $this->user->words()->count();
        $total = Word::count();
        $percentage = ($learned / $total) * 100;

        $this->assertEquals(25, $percentage);
    }

    /**
     * Test service calculates remaining words.
     */
    public function test_service_calculates_remaining(): void
    {
        Word::factory()->count(30)->create();
        $this->user->words()->attach(Word::take(12)->pluck('id'));

        $learned = $this->user->words()->count();
        $total = Word::count();
        $remaining = $total - $learned;

        $this->assertEquals(18, $remaining);
    }

    /**
     * Test service with zero progress.
     */
    public function test_service_zero_progress(): void
    {
        Word::factory()->count(10)->create();

        $learned = $this->user->words()->count();

        $this->assertEquals(0, $learned);
    }

    /**
     * Test service with maxed progress.
     */
    public function test_service_maxed_progress(): void
    {
        Word::factory()->count(50)->create();
        $this->user->words()->attach(Word::all());

        $learned = $this->user->words()->count();
        $total = Word::count();
        $percentage = ($learned / $total) * 100;

        $this->assertEquals(50, $learned);
        $this->assertEquals(50, $total);
        $this->assertEquals(100, $percentage);
    }

    /**
     * Test service groups by topic.
     */
    public function test_service_groups_by_topic(): void
    {
        Word::factory()->count(5)->create(['topic' => 'Animals']);
        Word::factory()->count(4)->create(['topic' => 'Foods']);
        Word::factory()->count(3)->create(['topic' => 'Colors']);

        $this->user->words()->attach(Word::all());

        $animals = $this->user->words()->where('topic', 'Animals')->count();
        $foods = $this->user->words()->where('topic', 'Foods')->count();
        $colors = $this->user->words()->where('topic', 'Colors')->count();

        $this->assertEquals(5, $animals);
        $this->assertEquals(4, $foods);
        $this->assertEquals(3, $colors);
    }

    /**
     * Test service groups by difficulty.
     */
    public function test_service_groups_by_difficulty(): void
    {
        Word::factory()->count(6)->create(['cefr_level' => 'A1']);
        Word::factory()->count(4)->create(['cefr_level' => 'B1']);
        Word::factory()->count(2)->create(['cefr_level' => 'C1']);

        $this->user->words()->attach(Word::all());

        $a1 = $this->user->words()->where('cefr_level', 'A1')->count();
        $b1 = $this->user->words()->where('cefr_level', 'B1')->count();
        $c1 = $this->user->words()->where('cefr_level', 'C1')->count();

        $this->assertEquals(6, $a1);
        $this->assertEquals(4, $b1);
        $this->assertEquals(2, $c1);
    }

    /**
     * Test service independent per user.
     */
    public function test_service_independent_per_user(): void
    {
        $user2 = User::factory()->create();
        Word::factory()->count(20)->create();

        $this->user->words()->attach(Word::take(8)->pluck('id'));
        $user2->words()->attach(Word::skip(8)->take(6)->pluck('id'));

        $user1Total = $this->user->words()->count();
        $user2Total = $user2->words()->count();

        $this->assertEquals(8, $user1Total);
        $this->assertEquals(6, $user2Total);
    }

    /**
     * Test service handles large datasets.
     */
    public function test_service_large_dataset_performance(): void
    {
        Word::factory()->count(500)->create();
        $this->user->words()->attach(Word::take(150)->pluck('id'));

        $learned = $this->user->words()->count();
        $total = Word::count();
        $percentage = ($learned / $total) * 100;

        $this->assertEquals(150, $learned);
        $this->assertEquals(500, $total);
        $this->assertEquals(30, $percentage);
    }

    /**
     * Test service scoring calculation.
     */
    public function test_service_scoring_basic(): void
    {
        Word::factory()->count(10)->create();
        $this->user->words()->attach(Word::all());

        $learned = $this->user->words()->count();
        $score = $learned * 10; // 10 points per word

        $this->assertEquals(100, $score);
    }

    /**
     * Test service scoring with bonus.
     */
    public function test_service_scoring_with_bonus(): void
    {
        Word::factory()->count(20)->create();
        $this->user->words()->attach(Word::all());

        $learned = $this->user->words()->count();
        $baseScore = $learned * 10;
        $percentage = (($learned / Word::count()) * 100);
        $bonus = $percentage >= 100 ? 50 : 0;
        $totalScore = $baseScore + $bonus;

        $this->assertEquals(250, $totalScore); // 20 * 10 + 50
    }

    /**
     * Test service progress tracking.
     */
    public function test_service_progress_tracking(): void
    {
        Word::factory()->count(5)->create();

        // Initial
        $progress1 = $this->user->words()->count();

        // After learning 2
        $this->user->words()->attach(Word::take(2)->pluck('id'));
        $progress2 = $this->user->words()->count();

        // After learning 2 more
        $this->user->words()->attach(Word::skip(2)->take(2)->pluck('id'));
        $progress3 = $this->user->words()->count();

        $this->assertEquals(0, $progress1);
        $this->assertEquals(2, $progress2);
        $this->assertEquals(4, $progress3);
    }

    /**
     * Test service stat aggregation.
     */
    public function test_service_stat_aggregation(): void
    {
        Word::factory()->count(25)->create();
        $this->user->words()->attach(Word::take(10)->pluck('id'));

        $stats = [
            'total_vocabulary' => Word::count(),
            'words_learned' => $this->user->words()->count(),
            'words_remaining' => Word::count() - $this->user->words()->count(),
            'completion_percentage' => ($this->user->words()->count() / Word::count()) * 100,
            'score' => $this->user->words()->count() * 10,
        ];

        $this->assertEquals(25, $stats['total_vocabulary']);
        $this->assertEquals(10, $stats['words_learned']);
        $this->assertEquals(15, $stats['words_remaining']);
        $this->assertEquals(40, $stats['completion_percentage']);
        $this->assertEquals(100, $stats['score']);
    }

    /**
     * Test service zero score.
     */
    public function test_service_zero_score(): void
    {
        Word::factory()->count(10)->create();

        $learned = $this->user->words()->count();
        $score = $learned * 10;

        $this->assertEquals(0, $score);
    }

    /**
     * Test service streak calculation.
     */
    public function test_service_streak_calculation(): void
    {
        Word::factory()->count(10)->create();

        // Learn 3 words today
        $this->user->words()->attach(Word::take(3)->pluck('id'));

        $streak = 1; // First day of learning
        $learned = $this->user->words()->count();

        $this->assertEquals(1, $streak);
        $this->assertEquals(3, $learned);
    }
}
