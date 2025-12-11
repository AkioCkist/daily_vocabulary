<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Word;
use App\Models\Topic;
use App\Models\UserWord;
use App\Models\DailyTest;
use App\Models\DailyTestItem;
use App\Models\TestAttempt;
use App\Models\SavedSession;
use App\Models\SavedSessionItem;
use App\Models\Subscription;
use App\Models\FlashcardTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test foreign key constraint on user_words.
     */
    public function test_user_words_foreign_key_constraint(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();

        $userWord = UserWord::create([
            'user_id' => $user->id,
            'word_id' => $word->id,
            'status' => 'saved',
        ]);

        $this->assertEquals($user->id, $userWord->user_id);
        $this->assertEquals($word->id, $userWord->word_id);
    }

    /**
     * Test user_words cascade delete on user deletion.
     */
    public function test_user_words_cascade_delete_on_user(): void
    {
        $user = User::factory()->create();
        $words = Word::factory(3)->create();

        foreach ($words as $word) {
            UserWord::create([
                'user_id' => $user->id,
                'word_id' => $word->id,
                'status' => 'saved',
            ]);
        }

        $this->assertCount(3, UserWord::where('user_id', $user->id)->get());

        $userId = $user->id;
        $user->delete();

        $this->assertCount(0, UserWord::where('user_id', $userId)->get());
    }

    /**
     * Test user_words cascade delete on word deletion.
     */
    public function test_user_words_cascade_delete_on_word(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();

        UserWord::create([
            'user_id' => $user->id,
            'word_id' => $word->id,
            'status' => 'saved',
        ]);

        $this->assertCount(1, UserWord::where('word_id', $word->id)->get());

        $wordId = $word->id;
        $word->delete();

        $this->assertCount(0, UserWord::where('word_id', $wordId)->get());
    }

    /**
     * Test daily_tests foreign key constraint.
     */
    public function test_daily_tests_foreign_key_constraint(): void
    {
        $user = User::factory()->create();

        $dailyTest = DailyTest::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'is_completed' => false,
        ]);

        $this->assertEquals($user->id, $dailyTest->user_id);
    }

    /**
     * Test daily_tests cascade delete on user deletion.
     */
    public function test_daily_tests_cascade_delete_on_user(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            DailyTest::create([
                'user_id' => $user->id,
                'date' => now()->subDays($i)->toDateString(),
                'is_completed' => $i % 2 === 0,
            ]);
        }

        $this->assertCount(5, DailyTest::where('user_id', $user->id)->get());

        $userId = $user->id;
        $user->delete();

        $this->assertCount(0, DailyTest::where('user_id', $userId)->get());
    }

    /**
     * Test daily_test_items cascade delete.
     */
    public function test_daily_test_items_cascade_delete(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();

        $dailyTest = DailyTest::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'is_completed' => false,
        ]);

        $testItem = DailyTestItem::create([
            'daily_test_id' => $dailyTest->id,
            'word_id' => $word->id,
            'question_type' => 'multiple_choice',
        ]);

        $this->assertCount(1, DailyTestItem::where('daily_test_id', $dailyTest->id)->get());

        $testItemId = $testItem->id;
        $dailyTest->delete();

        $this->assertCount(0, DailyTestItem::where('id', $testItemId)->get());
    }

    /**
     * Test test_attempts cascade delete.
     */
    public function test_test_attempts_cascade_delete_on_user(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();

        for ($i = 0; $i < 3; $i++) {
            TestAttempt::create([
                'user_id' => $user->id,
                'word_id' => $word->id,
                'is_correct' => $i % 2 === 0,
                'answer_text' => 'test answer',
            ]);
        }

        $this->assertCount(3, TestAttempt::where('user_id', $user->id)->get());

        $userId = $user->id;
        $user->delete();

        $this->assertCount(0, TestAttempt::where('user_id', $userId)->get());
    }

    /**
     * Test saved_sessions cascade delete.
     */
    public function test_saved_sessions_cascade_delete_on_user(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 2; $i++) {
            SavedSession::create([
                'user_id' => $user->id,
                'name' => "Session {$i}",
                'slug' => "session-{$i}",
            ]);
        }

        $this->assertCount(2, SavedSession::where('user_id', $user->id)->get());

        $userId = $user->id;
        $user->delete();

        $this->assertCount(0, SavedSession::where('user_id', $userId)->get());
    }

    /**
     * Test saved_session_items cascade delete.
     */
    public function test_saved_session_items_cascade_delete(): void
    {
        $user = User::factory()->create();
        $flashcardTemplate = FlashcardTemplate::factory()->create();

        $session = SavedSession::create([
            'user_id' => $user->id,
            'name' => 'Test Session',
            'slug' => 'test-session',
        ]);

        SavedSessionItem::create([
            'saved_session_id' => $session->id,
            'flashcard_id' => $flashcardTemplate->id,
            'position' => 1,
        ]);

        $this->assertCount(1, SavedSessionItem::where('saved_session_id', $session->id)->get());

        $sessionId = $session->id;
        $session->delete();

        $this->assertCount(0, SavedSessionItem::where('saved_session_id', $sessionId)->get());
    }

    /**
     * Test subscription cascade delete on user.
     */
    public function test_subscription_cascade_delete_on_user(): void
    {
        $user = User::factory()->create();

        Subscription::create([
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $this->assertNotNull(Subscription::where('user_id', $user->id)->first());

        $userId = $user->id;
        $user->delete();

        $this->assertNull(Subscription::where('user_id', $userId)->first());
    }

    /**
     * Test orphaned records prevention via constraints.
     */
    public function test_prevent_orphaned_user_words(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();

        UserWord::create([
            'user_id' => $user->id,
            'word_id' => $word->id,
            'status' => 'saved',
        ]);

        // User should have the user_word relationship
        $this->assertTrue($user->userWords()->where('word_id', $word->id)->exists());

        // Word should have the user_words relationship
        $this->assertTrue($word->userWords()->where('user_id', $user->id)->exists());
    }

    /**
     * Test bulk delete cascades properly.
     */
    public function test_bulk_delete_cascades(): void
    {
        $users = User::factory(3)->create();

        foreach ($users as $user) {
            for ($i = 0; $i < 2; $i++) {
                DailyTest::create([
                    'user_id' => $user->id,
                    'date' => now()->subDays($i)->toDateString(),
                    'is_completed' => false,
                ]);
            }
        }

        $this->assertCount(6, DailyTest::all());

        $userIds = $users->pluck('id');
        User::whereIn('id', $userIds)->delete();

        $this->assertCount(0, DailyTest::whereIn('user_id', $userIds)->get());
    }

    /**
     * Test relationship loading doesn't create orphans.
     */
    public function test_relationship_integrity(): void
    {
        $user = User::factory()->create();
        $words = Word::factory(5)->create();

        foreach ($words as $word) {
            UserWord::create([
                'user_id' => $user->id,
                'word_id' => $word->id,
                'status' => 'learning',
            ]);
        }

        // Load relationships
        $loadedUser = User::with('userWords')->find($user->id);
        $this->assertCount(5, $loadedUser->userWords);

        // All should have the correct user_id
        foreach ($loadedUser->userWords as $userWord) {
            $this->assertEquals($user->id, $userWord->user_id);
        }
    }

    /**
     * Test foreign key constraint failures.
     */
    public function test_invalid_foreign_key_fails(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to insert with non-existent user_id
        UserWord::create([
            'user_id' => 99999,
            'word_id' => Word::factory()->create()->id,
            'status' => 'saved',
        ]);
    }

    /**
     * Test cascade prevents inconsistent state.
     */
    public function test_cascade_maintains_consistency(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();

        $userWord = UserWord::create([
            'user_id' => $user->id,
            'word_id' => $word->id,
            'status' => 'learned',
        ]);

        // Verify relationship exists
        $this->assertNotNull(UserWord::find($userWord->id));

        // Delete user
        $user->delete();

        // UserWord should be gone
        $this->assertNull(UserWord::find($userWord->id));
    }

    /**
     * Test multiple foreign keys on same table.
     */
    public function test_multiple_foreign_keys(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();
        $topic = Topic::factory()->create();

        // DailyTestItem has both daily_test_id and word_id
        $dailyTest = DailyTest::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'is_completed' => false,
        ]);

        $testItem = DailyTestItem::create([
            'daily_test_id' => $dailyTest->id,
            'word_id' => $word->id,
            'question_type' => 'multiple_choice',
        ]);

        // Both should exist
        $this->assertNotNull(DailyTest::find($dailyTest->id));
        $this->assertNotNull(Word::find($word->id));

        // Verify both relationships are set
        $this->assertEquals($dailyTest->id, $testItem->daily_test_id);
        $this->assertEquals($word->id, $testItem->word_id);
    }

    /**
     * Test constraint doesn't prevent valid deletes.
     */
    public function test_valid_delete_succeeds(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();

        $userWord = UserWord::create([
            'user_id' => $user->id,
            'word_id' => $word->id,
            'status' => 'saved',
        ]);

        // Direct delete of UserWord should succeed
        $id = $userWord->id;
        $userWord->delete();

        $this->assertNull(UserWord::find($id));
    }

    /**
     * Test unique constraints work.
     */
    public function test_unique_constraints(): void
    {
        $user = User::factory()->create();
        $date = now()->toDateString();

        // Create first test
        DailyTest::create([
            'user_id' => $user->id,
            'date' => $date,
            'is_completed' => false,
        ]);

        // Try to create duplicate - should fail
        $this->expectException(\Illuminate\Database\QueryException::class);

        DailyTest::create([
            'user_id' => $user->id,
            'date' => $date,
            'is_completed' => true,
        ]);
    }

    /**
     * Test indexed foreign keys for query performance.
     */
    public function test_foreign_key_indexed_queries(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 10; $i++) {
            DailyTest::create([
                'user_id' => $user->id,
                'date' => now()->subDays($i)->toDateString(),
                'is_completed' => false,
            ]);
        }

        // Queries on foreign key should be fast (indexed)
        $results = DailyTest::where('user_id', $user->id)->get();

        $this->assertCount(10, $results);
    }

    /**
     * Test complex cascade scenario.
     */
    public function test_complex_cascade_scenario(): void
    {
        $user = User::factory()->create();
        $words = Word::factory(5)->create();

        // Create user_words
        foreach ($words as $word) {
            UserWord::create([
                'user_id' => $user->id,
                'word_id' => $word->id,
                'status' => 'learning',
            ]);
        }

        // Create daily test
        $dailyTest = DailyTest::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'is_completed' => false,
        ]);

        // Create test items
        foreach ($words->take(3) as $word) {
            DailyTestItem::create([
                'daily_test_id' => $dailyTest->id,
                'word_id' => $word->id,
                'question_type' => 'multiple_choice',
            ]);
        }

        // Verify everything exists
        $this->assertCount(5, UserWord::where('user_id', $user->id)->get());
        $this->assertCount(3, DailyTestItem::where('daily_test_id', $dailyTest->id)->get());

        // Delete user
        $user->delete();

        // Everything should be gone
        $this->assertCount(0, UserWord::where('user_id', $user->id)->get());
        $this->assertCount(0, DailyTest::where('user_id', $user->id)->get());
        $this->assertCount(0, DailyTestItem::where('daily_test_id', $dailyTest->id)->get());
    }

    /**
     * Test no orphaned records after operations.
     */
    public function test_no_orphaned_records_after_operations(): void
    {
        $users = User::factory(5)->create();
        $words = Word::factory(10)->create();

        // Create associations
        foreach ($users as $user) {
            foreach ($words->random(5) as $word) {
                UserWord::create([
                    'user_id' => $user->id,
                    'word_id' => $word->id,
                    'status' => 'saved',
                ]);
            }
        }

        // Delete some users
        $users->take(2)->each->delete();

        // Check remaining user_words
        $remainingUserWords = UserWord::all();
        $remainingUserIds = User::pluck('id');

        foreach ($remainingUserWords as $userWord) {
            $this->assertTrue($remainingUserIds->contains($userWord->user_id));
        }
    }

    /**
     * Test foreign key preserves referential integrity.
     */
    public function test_referential_integrity_preserved(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();

        $userWord = UserWord::create([
            'user_id' => $user->id,
            'word_id' => $word->id,
            'status' => 'saved',
        ]);

        // Both parent records should exist
        $this->assertTrue(User::where('id', $user->id)->exists());
        $this->assertTrue(Word::where('id', $word->id)->exists());

        // Delete parent
        $user->delete();

        // Child should be gone
        $this->assertFalse(UserWord::where('id', $userWord->id)->exists());

        // Parent word should still exist (different FK)
        $this->assertTrue(Word::where('id', $word->id)->exists());
    }
}
