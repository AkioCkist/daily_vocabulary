<?php

namespace Tests\Unit\Models;

use App\Models\Word;
use App\Models\Topic;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test word model instantiation.
     */
    public function test_word_model_can_be_instantiated(): void
    {
        $word = Word::factory()->create([
            'word' => 'hello',
            'definition' => 'a greeting',
            'example' => 'Hello, how are you?',
        ]);

        $this->assertIsObject($word);
        $this->assertInstanceOf(Word::class, $word);
        $this->assertEquals('hello', $word->word);
    }

    /**
     * Test word has fillable attributes.
     */
    public function test_word_has_fillable_attributes(): void
    {
        $data = [
            'word' => 'test',
            'pronunciation' => 'test-pro',
            'definition' => 'a test word',
            'example' => 'This is a test.',
            'source' => 'test-source',
            'topic' => 'tests',
            'cefr_level' => 'A1',
            'meaning' => 'test meaning',
        ];

        $word = Word::factory()->create($data);

        $this->assertEquals('test', $word->word);
        $this->assertEquals('test-pro', $word->pronunciation);
        $this->assertEquals('a test word', $word->definition);
    }

    /**
     * Test word has timestamps.
     */
    public function test_word_has_timestamps(): void
    {
        $word = Word::factory()->create();

        $this->assertNotNull($word->created_at);
        $this->assertNotNull($word->updated_at);
    }

    /**
     * Test word can be queried by word value.
     */
    public function test_word_can_be_queried_by_word_value(): void
    {
        Word::factory()->create(['word' => 'apple']);
        Word::factory()->create(['word' => 'banana']);

        $word = Word::where('word', 'apple')->first();

        $this->assertNotNull($word);
        $this->assertEquals('apple', $word->word);
    }

    /**
     * Test word can be queried by topic.
     */
    public function test_word_can_be_queried_by_topic(): void
    {
        Word::factory()->create(['topic' => 'fruits']);
        Word::factory()->create(['topic' => 'vegetables']);

        $fruits = Word::where('topic', 'fruits')->get();

        $this->assertCount(1, $fruits);
    }

    /**
     * Test word can be queried by CEFR level.
     */
    public function test_word_can_be_queried_by_cefr_level(): void
    {
        Word::factory()->create(['cefr_level' => 'A1']);
        Word::factory()->create(['cefr_level' => 'B1']);

        $beginnerWords = Word::where('cefr_level', 'A1')->get();

        $this->assertCount(1, $beginnerWords);
    }

    /**
     * Test word can be updated.
     */
    public function test_word_can_be_updated(): void
    {
        $word = Word::factory()->create(['word' => 'original']);

        $word->update(['word' => 'updated']);

        $this->assertEquals('updated', $word->word);
    }

    /**
     * Test word can be deleted.
     */
    public function test_word_can_be_deleted(): void
    {
        $word = Word::factory()->create();
        $id = $word->id;

        $word->delete();

        $this->assertNull(Word::find($id));
    }

    /**
     * Test word has user relationship via belongsToMany.
     */
    public function test_word_belongs_to_many_users(): void
    {
        $word = Word::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $word->users()->attach([$user1->id, $user2->id]);

        $users = $word->users;

        $this->assertCount(2, $users);
        $this->assertTrue($users->contains($user1));
        $this->assertTrue($users->contains($user2));
    }

    /**
     * Test word can load users eagerly.
     */
    public function test_word_can_load_users_eagerly(): void
    {
        $word = Word::factory()->create();
        User::factory()->count(3)->create()->each(function ($user) use ($word) {
            $word->users()->attach($user);
        });

        $loadedWord = Word::with('users')->find($word->id);

        $this->assertEquals(3, $loadedWord->users->count());
    }

    /**
     * Test word pivot can be accessed.
     */
    public function test_word_pivot_can_be_accessed(): void
    {
        $word = Word::factory()->create();
        $user = User::factory()->create();

        $word->users()->attach($user);

        $this->assertNotNull($word->users->first()->pivot);
    }

    /**
     * Test multiple words can be created.
     */
    public function test_multiple_words_can_be_created(): void
    {
        Word::factory()->count(10)->create();

        $this->assertDatabaseCount('words', 10);
    }

    /**
     * Test word bulk operations.
     */
    public function test_word_bulk_create_works(): void
    {
        $words = [
            ['word' => 'cat', 'pronunciation' => 'kæt', 'definition' => 'A feline animal', 'example' => 'The cat is sleeping.', 'topic' => 'animals', 'created_at' => now(), 'updated_at' => now()],
            ['word' => 'dog', 'pronunciation' => 'dɔɡ', 'definition' => 'A canine animal', 'example' => 'The dog is running.', 'topic' => 'animals', 'created_at' => now(), 'updated_at' => now()],
        ];

        Word::insert($words);

        $this->assertDatabaseCount('words', 2);
    }

    /**
     * Test word all attributes are accessible.
     */
    public function test_word_all_attributes_are_accessible(): void
    {
        $word = Word::factory()->create([
            'word' => 'example',
            'pronunciation' => 'ig-zam-pul',
            'definition' => 'a thing characteristic',
            'example' => 'Here is an example.',
            'source' => 'oxford',
            'topic' => 'education',
            'cefr_level' => 'A2',
            'meaning' => 'exemplary',
        ]);

        $this->assertEquals('example', $word->word);
        $this->assertEquals('ig-zam-pul', $word->pronunciation);
        $this->assertEquals('a thing characteristic', $word->definition);
        $this->assertEquals('Here is an example.', $word->example);
        $this->assertEquals('oxford', $word->source);
        $this->assertEquals('education', $word->topic);
        $this->assertEquals('A2', $word->cefr_level);
        $this->assertEquals('exemplary', $word->meaning);
    }

    /**
     * Test word collection methods.
     */
    public function test_word_collection_methods_work(): void
    {
        Word::factory()->count(3)->create(['topic' => 'fruits']);
        Word::factory()->count(2)->create(['topic' => 'vegetables']);

        $words = Word::all();

        $this->assertCount(5, $words);
        $this->assertTrue($words->isNotEmpty());
    }

    /**
     * Test word where clause filtering.
     */
    public function test_word_where_clause_filtering(): void
    {
        Word::factory()->create(['cefr_level' => 'A1', 'word' => 'hello']);
        Word::factory()->create(['cefr_level' => 'B1', 'word' => 'serendipity']);

        $advanced = Word::where('cefr_level', '!=', 'A1')->get();

        $this->assertCount(1, $advanced);
        $this->assertEquals('serendipity', $advanced->first()->word);
    }

    /**
     * Test word findOrFail method.
     */
    public function test_word_find_or_fail_returns_word(): void
    {
        $word = Word::factory()->create();

        $found = Word::findOrFail($word->id);

        $this->assertTrue($found->is($word));
    }

    /**
     * Test word findOrFail throws exception for missing word.
     */
    public function test_word_find_or_fail_throws_exception(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Word::findOrFail(99999);
    }

    /**
     * Test word unique attributes.
     */
    public function test_word_attributes_are_unique_per_instance(): void
    {
        $word1 = Word::factory()->create(['word' => 'unique1']);
        $word2 = Word::factory()->create(['word' => 'unique2']);

        $this->assertNotEquals($word1->word, $word2->word);
    }

    /**
     * Test word count aggregation.
     */
    public function test_word_count_aggregation_works(): void
    {
        Word::factory()->count(15)->create();

        $count = Word::count();

        $this->assertEquals(15, $count);
    }

    /**
     * Test word whereIn filtering.
     */
    public function test_word_wherein_filtering_works(): void
    {
        Word::factory()->create(['word' => 'apple']);
        Word::factory()->create(['word' => 'banana']);
        Word::factory()->create(['word' => 'cherry']);

        $words = Word::whereIn('word', ['apple', 'cherry'])->get();

        $this->assertCount(2, $words);
    }
}
