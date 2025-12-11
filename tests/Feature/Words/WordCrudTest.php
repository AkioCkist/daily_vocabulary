<?php

namespace Tests\Feature\Words;

use App\Models\User;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test words list can be retrieved.
     */
    public function test_words_list_can_be_retrieved(): void
    {
        Word::factory()->count(5)->create();

        $response = $this->get(route('words.index'));

        $response->assertStatus(200);
    }

    /**
     * Test word can be searched by name.
     */
    public function test_word_can_be_searched_by_name(): void
    {
        Word::factory()->create(['word' => 'serendipity']);
        Word::factory()->create(['word' => 'eloquent']);

        $response = $this->get(route('words.index', ['q' => 'serendipity']));

        $response->assertStatus(200);
    }

    /**
     * Test word can be created with valid data.
     */
    public function test_word_can_be_created_with_valid_data(): void
    {
        $data = [
            'word' => 'persistent',
            'pronunciation' => 'pər-ˈsis-tənt',
            'definition' => 'continuing firmly or obstinately',
            'example' => 'He made persistent efforts to improve.',
            'topic' => 'adjectives',
            'cefr_level' => 'B1',
        ];

        $word = Word::create($data);

        $this->assertDatabaseHas('words', [
            'word' => 'persistent',
            'definition' => 'continuing firmly or obstinately',
            'example' => 'He made persistent efforts to improve.',
            'topic' => 'adjectives',
        ]);

        $this->assertNotNull($word->id);
    }

    /**
     * Test word can be updated.
     */
    public function test_word_can_be_updated(): void
    {
        $word = Word::factory()->create([
            'word' => 'original',
            'definition' => 'Original definition',
        ]);

        $word->update([
            'definition' => 'Updated definition',
            'example' => 'Updated example',
        ]);

        $this->assertDatabaseHas('words', [
            'id' => $word->id,
            'word' => 'original',
            'definition' => 'Updated definition',
            'example' => 'Updated example',
        ]);
    }

    /**
     * Test word can be deleted.
     */
    public function test_word_can_be_deleted(): void
    {
        $word = Word::factory()->create();
        $wordId = $word->id;

        $word->delete();

        $this->assertDatabaseMissing('words', ['id' => $wordId]);
    }

    /**
     * Test word with pronunciation.
     */
    public function test_word_with_pronunciation(): void
    {
        $word = Word::factory()->create([
            'pronunciation' => 'test-pronunciation',
        ]);

        $this->assertEquals('test-pronunciation', $word->pronunciation);
    }

    /**
     * Test word with source.
     */
    public function test_word_with_source(): void
    {
        $word = Word::factory()->create([
            'source' => 'https://example.com',
        ]);

        $this->assertEquals('https://example.com', $word->source);
    }

    /**
     * Test word with meaning.
     */
    public function test_word_with_meaning(): void
    {
        $word = Word::factory()->create([
            'meaning' => 'Alternative meaning',
        ]);

        $this->assertEquals('Alternative meaning', $word->meaning);
    }

    /**
     * Test word bulk create works.
     */
    public function test_word_bulk_create_works(): void
    {
        $words = [
            ['word' => 'word1', 'definition' => 'def1', 'example' => 'ex1', 'topic' => 'test', 'created_at' => now(), 'updated_at' => now()],
            ['word' => 'word2', 'definition' => 'def2', 'example' => 'ex2', 'topic' => 'test', 'created_at' => now(), 'updated_at' => now()],
            ['word' => 'word3', 'definition' => 'def3', 'example' => 'ex3', 'topic' => 'test', 'created_at' => now(), 'updated_at' => now()],
        ];

        Word::insert($words);

        $this->assertDatabaseCount('words', 3);
    }

    /**
     * Test word can be filtered by topic.
     */
    public function test_word_can_be_filtered_by_topic(): void
    {
        Word::factory()->create(['topic' => 'animals']);
        Word::factory()->create(['topic' => 'animals']);
        Word::factory()->create(['topic' => 'foods']);

        $animals = Word::where('topic', 'animals')->get();
        $foods = Word::where('topic', 'foods')->get();

        $this->assertCount(2, $animals);
        $this->assertCount(1, $foods);
    }

    /**
     * Test word has timestamps.
     */
    public function test_word_has_timestamps(): void
    {
        $word = Word::create([
            'word' => 'test',
            'definition' => 'A test word',
            'example' => 'Test example',
            'topic' => 'test',
        ]);

        $this->assertNotNull($word->created_at);
        $this->assertNotNull($word->updated_at);
    }

    /**
     * Test word prevents SQL injection in definition.
     */
    public function test_word_prevents_sql_injection(): void
    {
        $maliciousData = [
            'word' => 'test',
            'definition' => "'; DROP TABLE words; --",
            'example' => "'; DROP TABLE words; --",
            'topic' => 'test',
        ];

        $word = Word::create($maliciousData);

        // Table should still exist and word should be created with exact definition
        $this->assertDatabaseHas('words', [
            'word' => 'test',
            'definition' => "'; DROP TABLE words; --",
        ]);

        $this->assertDatabaseCount('words', 1);
    }

    /**
     * Test word has relationship with users.
     */
    public function test_word_has_relationship_with_users(): void
    {
        $word = Word::factory()->create();
        $user = User::factory()->create();

        $word->users()->attach($user, [
            'is_learned' => false,
            'mastered' => false,
        ]);

        $this->assertTrue($word->users()->where('user_id', $user->id)->exists());
    }

    /**
     * Test word search does not have N+1 query issue.
     */
    public function test_word_search_does_not_have_n_plus_one_queries(): void
    {
        Word::factory()->count(5)->create();

        \DB::enableQueryLog();

        $words = Word::all();

        $queries = \DB::getQueryLog();
        
        // Should only have 1 query (SELECT all words)
        $this->assertLessThan(3, count($queries), 'Word search should not have excessive database queries');
    }

    /**
     * Test word list can be paginated.
     */
    public function test_word_list_can_be_paginated(): void
    {
        Word::factory()->count(60)->create();

        $response = $this->get(route('words.index'));

        $response->assertStatus(200);
    }

    /**
     * Test word can have CEFR level.
     */
    public function test_word_can_have_cefr_level(): void
    {
        $word = Word::factory()->create(['cefr_level' => 'C1']);

        $this->assertEquals('C1', $word->cefr_level);
    }

    /**
     * Test word can be queried by CEFR level.
     */
    public function test_word_can_be_queried_by_cefr_level(): void
    {
        Word::factory()->create(['cefr_level' => 'A1']);
        Word::factory()->create(['cefr_level' => 'B1']);
        Word::factory()->create(['cefr_level' => 'B1']);

        $b1Words = Word::where('cefr_level', 'B1')->get();

        $this->assertCount(2, $b1Words);
    }

    /**
     * Test word search by partial match.
     */
    public function test_word_search_by_partial_match(): void
    {
        Word::factory()->create(['word' => 'serendipity']);
        Word::factory()->create(['word' => 'serpentine']);
        Word::factory()->create(['word' => 'eloquent']);

        $results = Word::where('word', 'like', 'ser%')->get();

        $this->assertCount(2, $results);
    }

    /**
     * Test word can be mass updated.
     */
    public function test_word_can_be_mass_updated(): void
    {
        Word::factory()->count(5)->create(['topic' => 'old_topic']);

        Word::where('topic', 'old_topic')->update(['topic' => 'new_topic']);

        $this->assertDatabaseMissing('words', ['topic' => 'old_topic']);
        $this->assertDatabaseCount('words', 5);
    }
}
