<?php

namespace Tests\Unit\Models;

use App\Models\Topic;
use App\Models\Word;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TopicTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test topic model instantiation.
     */
    public function test_topic_model_can_be_instantiated(): void
    {
        $topic = Topic::create([
            'name' => 'Animals',
            'description' => 'Vocabulary about animals',
        ]);

        $this->assertIsObject($topic);
        $this->assertInstanceOf(Topic::class, $topic);
        $this->assertEquals('Animals', $topic->name);
    }

    /**
     * Test topic has fillable attributes.
     */
    public function test_topic_has_fillable_attributes(): void
    {
        $data = [
            'name' => 'Colors',
            'description' => 'Colors vocabulary',
            'is_system' => true,
        ];

        $topic = Topic::create($data);

        $this->assertEquals('Colors', $topic->name);
        $this->assertEquals('Colors vocabulary', $topic->description);
        $this->assertTrue($topic->is_system);
    }

    /**
     * Test topic has timestamps.
     */
    public function test_topic_has_timestamps(): void
    {
        $topic = Topic::create(['name' => 'Test Topic']);

        $this->assertNotNull($topic->created_at);
        $this->assertNotNull($topic->updated_at);
    }

    /**
     * Test topic can be queried by name.
     */
    public function test_topic_can_be_queried_by_name(): void
    {
        Topic::factory()->create(['name' => 'Sports']);
        Topic::factory()->create(['name' => 'Foods']);

        $sports = Topic::where('name', 'Sports')->first();

        $this->assertNotNull($sports);
        $this->assertEquals('Sports', $sports->name);
    }

    /**
     * Test topic can be queried by is_system flag.
     */
    public function test_topic_can_be_queried_by_system_flag(): void
    {
        Topic::factory()->create(['is_system' => true]);
        Topic::factory()->create(['is_system' => false]);

        $systemTopics = Topic::where('is_system', true)->get();

        $this->assertCount(1, $systemTopics);
    }

    /**
     * Test topic can be updated.
     */
    public function test_topic_can_be_updated(): void
    {
        $topic = Topic::factory()->create(['name' => 'Original']);

        $topic->update(['name' => 'Updated']);

        $this->assertEquals('Updated', $topic->name);
    }

    /**
     * Test topic can be deleted.
     */
    public function test_topic_can_be_deleted(): void
    {
        $topic = Topic::factory()->create();
        $id = $topic->id;

        $topic->delete();

        $this->assertNull(Topic::find($id));
    }

    /**
     * Test topic has many words relationship.
     */
    public function test_topic_has_many_words(): void
    {
        $topic = Topic::factory()->create(['name' => 'Animals']);
        
        Word::factory()->count(3)->create(['topic' => 'Animals']);

        $words = $topic->words;

        $this->assertCount(3, $words);
    }

    /**
     * Test topic belongs to user.
     */
    public function test_topic_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $topic = Topic::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $topic->user_id);
        $this->assertTrue($topic->user->is($user));
    }

    /**
     * Test topic can have null user.
     */
    public function test_topic_can_have_null_user(): void
    {
        $topic = Topic::create([
            'name' => 'System Topic',
            'is_system' => true,
        ]);

        $this->assertNull($topic->user_id);
    }

    /**
     * Test multiple topics can be created.
     */
    public function test_multiple_topics_can_be_created(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Topic::create(['name' => "Topic $i"]);
        }

        $this->assertDatabaseCount('topics', 10);
    }

    /**
     * Test topic bulk create works.
     */
    public function test_topic_bulk_create_works(): void
    {
        $topics = [
            ['name' => 'topic1', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'topic2', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'topic3', 'created_at' => now(), 'updated_at' => now()],
        ];

        Topic::insert($topics);

        $this->assertDatabaseCount('topics', 3);
    }

    /**
     * Test topic all attributes are accessible.
     */
    public function test_topic_all_attributes_are_accessible(): void
    {
        $user = User::factory()->create();
        $topic = Topic::create([
            'name' => 'Test Topic',
            'description' => 'Test Description',
            'is_system' => false,
            'user_id' => $user->id,
        ]);

        $this->assertEquals('Test Topic', $topic->name);
        $this->assertEquals('Test Description', $topic->description);
        $this->assertFalse($topic->is_system);
        $this->assertEquals($user->id, $topic->user_id);
    }

    /**
     * Test topic collection methods.
     */
    public function test_topic_collection_methods_work(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Topic::create(['name' => "Topic $i"]);
        }

        $topics = Topic::all();

        $this->assertCount(5, $topics);
        $this->assertTrue($topics->isNotEmpty());
    }

    /**
     * Test topic where clause filtering.
     */
    public function test_topic_where_clause_filtering(): void
    {
        Topic::create(['name' => 'Topic1', 'is_system' => true]);
        Topic::create(['name' => 'Topic2', 'is_system' => false]);

        $custom = Topic::where('is_system', false)->get();

        $this->assertCount(1, $custom);
        $this->assertEquals('Topic2', $custom->first()->name);
    }

    /**
     * Test topic findOrFail method.
     */
    public function test_topic_find_or_fail_returns_topic(): void
    {
        $topic = Topic::factory()->create();

        $found = Topic::findOrFail($topic->id);

        $this->assertTrue($found->is($topic));
    }

    /**
     * Test topic findOrFail throws exception.
     */
    public function test_topic_find_or_fail_throws_exception(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Topic::findOrFail(99999);
    }

    /**
     * Test topic count aggregation works.
     */
    public function test_topic_count_aggregation_works(): void
    {
        for ($i = 0; $i < 20; $i++) {
            Topic::create(['name' => 'Topic' . uniqid()]);
        }

        $count = Topic::count();

        $this->assertGreaterThanOrEqual(20, $count);
    }

    /**
     * Test topic whereIn filtering.
     */
    public function test_topic_wherein_filtering_works(): void
    {
        Topic::factory()->create(['name' => 'Topic1']);
        Topic::factory()->create(['name' => 'Topic2']);
        Topic::factory()->create(['name' => 'Topic3']);

        $topics = Topic::whereIn('name', ['Topic1', 'Topic3'])->get();

        $this->assertCount(2, $topics);
    }

    /**
     * Test topic user filtering.
     */
    public function test_topic_user_filtering_works(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        for ($i = 0; $i < 3; $i++) {
            Topic::create(['name' => "Topic$i", 'user_id' => $user1->id]);
        }
        for ($i = 0; $i < 2; $i++) {
            Topic::create(['name' => "Topic2-$i", 'user_id' => $user2->id]);
        }

        $user1Topics = Topic::where('user_id', $user1->id)->get();

        $this->assertCount(3, $user1Topics);
    }

    /**
     * Test topic system flag can be set.
     */
    public function test_topic_system_flag_can_be_set(): void
    {
        $topic = Topic::create([
            'name' => 'Custom Topic',
            'is_system' => false,
        ]);

        $this->assertFalse($topic->is_system);
    }
}
