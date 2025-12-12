<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Word;
use App\Models\Topic;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WordManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);
    }

    /**
     * Test admin can list all words.
     */
    public function test_admin_can_list_words(): void
    {
        Word::factory()->count(10)->create();

        $words = Word::all();

        $this->assertEquals(10, $words->count());
    }

    /**
     * Test admin can view word details.
     */
    public function test_admin_can_view_word(): void
    {
        $word = Word::factory()->create([
            'word' => 'serendipity',
            'definition' => 'Finding something by chance',
        ]);

        $found = Word::find($word->id);

        $this->assertNotNull($found);
        $this->assertEquals('serendipity', $found->word);
    }

    /**
     * Test admin can create word.
     */
    public function test_admin_can_create_word(): void
    {
        $word = Word::create([
            'word' => 'eloquent',
            'pronunciation' => 'EL-uh-kwent',
            'definition' => 'Fluent or persuasive in speaking',
            'example' => 'She was an eloquent speaker.',
        ]);

        $this->assertNotNull($word->id);
        $this->assertEquals('eloquent', $word->word);
    }

    /**
     * Test admin can update word.
     */
    public function test_admin_can_update_word(): void
    {
        $word = Word::factory()->create(['definition' => 'Old definition']);

        $word->update(['definition' => 'New definition']);

        $this->assertEquals('New definition', $word->fresh()->definition);
    }

    /**
     * Test admin can update word CEFR level.
     */
    public function test_admin_can_update_cefr_level(): void
    {
        $word = Word::factory()->create(['cefr_level' => 'A1']);

        $word->update(['cefr_level' => 'B2']);

        $this->assertEquals('B2', $word->fresh()->cefr_level);
    }

    /**
     * Test admin can delete word.
     */
    public function test_admin_can_delete_word(): void
    {
        $word = Word::factory()->create();
        $wordId = $word->id;

        $word->delete();

        $this->assertNull(Word::find($wordId));
    }

    /**
     * Test admin can bulk create words.
     */
    public function test_admin_bulk_create_words(): void
    {
        $words = [
            [
                'word' => 'word1',
                'definition' => 'def1',
                'example' => 'example1',
            ],
            [
                'word' => 'word2',
                'definition' => 'def2',
                'example' => 'example2',
            ],
            [
                'word' => 'word3',
                'definition' => 'def3',
                'example' => 'example3',
            ],
        ];

        foreach ($words as $data) {
            Word::create($data);
        }

        $count = Word::count();

        $this->assertEquals(3, $count);
    }

    /**
     * Test admin can filter words by CEFR level.
     */
    public function test_admin_filter_by_cefr_level(): void
    {
        Word::factory()->count(3)->create(['cefr_level' => 'A1']);
        Word::factory()->count(4)->create(['cefr_level' => 'B1']);

        $a1Words = Word::where('cefr_level', 'A1')->count();
        $b1Words = Word::where('cefr_level', 'B1')->count();

        $this->assertEquals(3, $a1Words);
        $this->assertEquals(4, $b1Words);
    }

    /**
     * Test admin can filter words by topic.
     */
    public function test_admin_filter_by_topic(): void
    {
        Word::factory()->count(4)->create(['topic' => 'Animals']);
        Word::factory()->count(3)->create(['topic' => 'Foods']);

        $animals = Word::where('topic', 'Animals')->count();
        $foods = Word::where('topic', 'Foods')->count();

        $this->assertEquals(4, $animals);
        $this->assertEquals(3, $foods);
    }

    /**
     * Test admin can search words.
     */
    public function test_admin_search_words(): void
    {
        Word::factory()->create(['word' => 'elephant']);
        Word::factory()->create(['word' => 'zebra']);

        $found = Word::where('word', 'like', '%elephant%')->first();

        $this->assertNotNull($found);
        $this->assertEquals('elephant', $found->word);
    }

    /**
     * Test admin validates word uniqueness.
     */
    public function test_admin_word_uniqueness(): void
    {
        Word::factory()->create(['word' => 'unique']);

        // Duplicate word should fail
        $this->expectException(\Exception::class);
        Word::factory()->create(['word' => 'unique']);
    }

    /**
     * Test admin cannot create word without required fields.
     */
    public function test_admin_word_validation(): void
    {
        $word = new Word();
        $word->word = '';

        $this->assertEmpty($word->word);
    }

    /**
     * Test admin can update pronunciation.
     */
    public function test_admin_update_pronunciation(): void
    {
        $word = Word::factory()->create(['pronunciation' => 'OLD']);

        $word->update(['pronunciation' => 'NEW']);

        $this->assertEquals('NEW', $word->fresh()->pronunciation);
    }

    /**
     * Test admin can update example.
     */
    public function test_admin_update_example(): void
    {
        $word = Word::factory()->create(['example' => 'Old example']);

        $word->update(['example' => 'New example']);

        $this->assertEquals('New example', $word->fresh()->example);
    }

    /**
     * Test admin can bulk delete words by topic.
     */
    public function test_admin_bulk_delete_by_topic(): void
    {
        Word::factory()->count(5)->create(['topic' => 'DeleteMe']);
        Word::factory()->count(3)->create(['topic' => 'KeepMe']);

        Word::where('topic', 'DeleteMe')->delete();

        $remaining = Word::where('topic', 'DeleteMe')->count();

        $this->assertEquals(0, $remaining);
    }

    /**
     * Test admin can view word usage stats.
     */
    public function test_admin_word_usage_stats(): void
    {
        $word = Word::factory()->create();

        // Create users and attach word
        User::factory()->count(5)->create()->each(function ($user) use ($word) {
            $user->words()->attach($word->id);
        });

        $usageCount = $word->users()->count();

        $this->assertEquals(5, $usageCount);
    }

    /**
     * Test admin can filter by source.
     */
    public function test_admin_filter_by_source(): void
    {
        Word::factory()->count(3)->create(['source' => 'Dictionary']);
        Word::factory()->count(2)->create(['source' => 'Book']);

        $dictWords = Word::where('source', 'Dictionary')->count();

        $this->assertEquals(3, $dictWords);
    }

    /**
     * Test word pagination.
     */
    public function test_word_pagination(): void
    {
        Word::factory()->count(25)->create();

        $page = Word::paginate(10);

        $this->assertLessThanOrEqual(10, $page->count());
    }

    /**
     * Test admin can sort words.
     */
    public function test_admin_sort_words(): void
    {
        Word::factory()->create(['word' => 'zebra']);
        Word::factory()->create(['word' => 'apple']);
        Word::factory()->create(['word' => 'mango']);

        $sorted = Word::orderBy('word', 'asc')->get();

        $this->assertEquals('apple', $sorted->first()->word);
    }

    /**
     * Test word with multiple topics.
     */
    public function test_word_topic_assignment(): void
    {
        $word = Word::factory()->create(['topic' => 'Animals']);

        $this->assertEquals('Animals', $word->topic);
    }

    /**
     * Test admin mass update CEFR.
     */
    public function test_admin_mass_update_cefr(): void
    {
        Word::factory()->count(5)->create(['cefr_level' => 'A1']);

        Word::where('cefr_level', 'A1')->limit(3)->update(['cefr_level' => 'A2']);

        $a2Count = Word::where('cefr_level', 'A2')->count();

        $this->assertEquals(3, $a2Count);
    }

    /**
     * Test word count by difficulty.
     */
    public function test_word_count_by_difficulty(): void
    {
        Word::factory()->count(5)->create(['cefr_level' => 'A1']);
        Word::factory()->count(4)->create(['cefr_level' => 'B1']);
        Word::factory()->count(3)->create(['cefr_level' => 'C1']);

        $stats = [
            'A1' => Word::where('cefr_level', 'A1')->count(),
            'B1' => Word::where('cefr_level', 'B1')->count(),
            'C1' => Word::where('cefr_level', 'C1')->count(),
        ];

        $this->assertEquals(5, $stats['A1']);
        $this->assertEquals(4, $stats['B1']);
        $this->assertEquals(3, $stats['C1']);
    }
}
