<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Word;
use App\Models\UserWord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LearningFeatureTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_authenticated_user_can_access_learning_page(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get(route('learning.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Learning/Index'));
    }

    public function test_user_can_mark_word_as_learned(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('learning.mark-learned'), [
                'word_id' => $word->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Word marked as learned!',
        ]);

        $this->assertDatabaseHas('user_words', [
            'user_id' => $user->id,
            'word_id' => $word->id,
            'is_learned' => true,
        ]);
    }

    public function test_user_can_add_word_to_review(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('learning.add-to-review'), [
                'word_id' => $word->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Word added to review list!',
        ]);

        $this->assertDatabaseHas('user_words', [
            'user_id' => $user->id,
            'word_id' => $word->id,
        ]);

        $userWord = UserWord::where('user_id', $user->id)
            ->where('word_id', $word->id)
            ->first();

        $this->assertGreaterThan(0, $userWord->mistake_count);
    }

    public function test_user_can_get_next_random_word(): void
    {
        $user = User::factory()->create();
        Word::factory()->count(5)->create();

        $response = $this->actingAs($user)
            ->postJson(route('learning.next'), [
                'exclude_ids' => [],
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'word' => [
                'id',
                'word',
                'definition',
                'example',
            ],
            'has_more',
        ]);
    }

    public function test_user_can_start_learning_session_with_filters(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post(route('learning.start'), [
                'topic' => 'travel',
                'cefr_level' => 'A2',
            ]);

        $response->assertRedirect(route('learning.index'));
        $this->assertEquals('travel', session('word_filters.topic'));
        $this->assertEquals('A2', session('word_filters.cefr_level'));
    }

    public function test_user_can_update_learning_progress(): void
    {
        $user = User::factory()->create();
        $word = Word::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('learning.update-progress'), [
                'word_id' => $word->id,
                'is_correct' => true,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Correct!',
        ]);

        $this->assertDatabaseHas('user_words', [
            'user_id' => $user->id,
            'word_id' => $word->id,
        ]);
    }

    public function test_learning_requires_authentication(): void
    {
        $response = $this->get(route('learning.index'));
        $response->assertRedirect(route('login'));

        $response = $this->postJson(route('learning.mark-learned'), [
            'word_id' => 1,
        ]);
        $response->assertStatus(401);
    }

    public function test_mark_learned_validates_word_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('learning.mark-learned'), [
                'word_id' => 999999, // Non-existent word
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['word_id']);
    }
}
