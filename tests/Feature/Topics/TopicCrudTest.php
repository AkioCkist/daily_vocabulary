<?php

namespace Tests\Feature\Topics;

use App\Models\User;
use App\Models\Topic;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test topic can be created with valid data.
     */
    public function test_topic_can_be_created_with_valid_data(): void
    {
        $data = [
            'name' => 'Animals',
            'description' => 'Vocabulary related to animals',
            'user_id' => $this->user->id,
        ];

        $topic = Topic::create($data);

        $this->assertDatabaseHas('topics', [
            'name' => 'Animals',
            'description' => 'Vocabulary related to animals',
            'user_id' => $this->user->id,
        ]);

        $this->assertNotNull($topic->id);
    }

    /**
     * Test system topic is created.
     */
    public function test_system_topic_can_be_created(): void
    {
        $topic = Topic::create([
            'name' => 'System Topic',
            'is_system' => true,
        ]);

        $this->assertTrue($topic->is_system);
        $this->assertDatabaseHas('topics', [
            'name' => 'System Topic',
            'is_system' => true,
        ]);
    }

    /**
     * Test topic can be updated.
     */
    public function test_topic_can_be_updated(): void
    {
        $topic = Topic::create([
            'name' => 'Original Topic',
            'description' => 'Original description',
        ]);

        $topic->update([
            'description' => 'Updated description',
        ]);

        $this->assertDatabaseHas('topics', [
            'id' => $topic->id,
            'name' => 'Original Topic',
            'description' => 'Updated description',
        ]);
    }

    /**
     * Test topic can be deleted.
     */
    public function test_topic_can_be_deleted(): void
    {
        $topic = Topic::create(['name' => 'To Delete']);
        $topicId = $topic->id;

        $topic->delete();

        $this->assertDatabaseMissing('topics', ['id' => $topicId]);
    }

    /**
     * Test topic has relationship with words.
     */
    public function test_topic_has_relationship_with_words(): void
    {
        $topic = Topic::factory()->create(['name' => 'Colors']);
        Word::factory()->create(['topic' => 'Colors']);
        Word::factory()->create(['topic' => 'Colors']);

        $words = $topic->words;

        $this->assertCount(2, $words);
    }

    /**
     * Test topic has relationship with user.
     */
    public function test_topic_has_relationship_with_user(): void
    {
        $topic = Topic::create(['name' => 'Test Topic', 'user_id' => $this->user->id]);

        $this->assertEquals($this->user->id, $topic->user_id);
        $this->assertTrue($topic->user->is($this->user));
    }

    /**
     * Test topic retrieved by user.
     */
    public function test_topic_retrieved_by_specific_user(): void
    {
        $user2 = User::factory()->create();

        Topic::create(['name' => 'Topic1', 'user_id' => $this->user->id]);
        Topic::create(['name' => 'Topic2', 'user_id' => $user2->id]);

        $userTopics = Topic::where('user_id', $this->user->id)->get();

        $this->assertCount(1, $userTopics);
    }

    /**
     * Test topic filtering by name.
     */
    public function test_topic_can_be_filtered_by_name(): void
    {
        Topic::create(['name' => 'Animals']);
        Topic::create(['name' => 'Foods']);

        $animals = Topic::where('name', 'Animals')->get();

        $this->assertCount(1, $animals);
    }

    /**
     * Test topic with timestamps.
     */
    public function test_topic_has_timestamps(): void
    {
        $topic = Topic::create([
            'name' => 'Test Topic',
        ]);

        $this->assertNotNull($topic->created_at);
        $this->assertNotNull($topic->updated_at);
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

    /**
     * Test topic user_id is optional.
     */
    public function test_topic_user_id_is_optional(): void
    {
        $topic = Topic::create([
            'name' => 'System Topic',
            'is_system' => true,
        ]);

        $this->assertNull($topic->user_id);
    }

    /**
     * Test topic bulk create works.
     */
    public function test_topic_bulk_create_works(): void
    {
        $topics = [
            ['name' => 'topic1', 'user_id' => $this->user->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'topic2', 'user_id' => $this->user->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'topic3', 'user_id' => $this->user->id, 'created_at' => now(), 'updated_at' => now()],
        ];

        Topic::insert($topics);

        $this->assertDatabaseCount('topics', 3);
    }

    /**
     * Test topic search does not have N+1 queries.
     */
    public function test_topic_search_does_not_have_n_plus_one_queries(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Topic::create(['name' => "Topic $i"]);
        }

        \DB::enableQueryLog();

        $topics = Topic::all();

        $queries = \DB::getQueryLog();
        
        // Should only have 1 query (SELECT all topics)
        $this->assertLessThan(3, count($queries), 'Topic search should not have excessive database queries');
    }

    /**
     * Test multiple topics can be created for same user.
     */
    public function test_multiple_topics_can_be_created_for_same_user(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Topic::create(['name' => "Topic $i", 'user_id' => $this->user->id]);
        }

        $userTopics = Topic::where('user_id', $this->user->id)->get();

        $this->assertCount(5, $userTopics);
    }

    /**
     * Test topic partial update preserves other fields.
     */
    public function test_topic_partial_update_preserves_other_fields(): void
    {
        $topic = Topic::create([
            'name' => 'Original',
            'description' => 'Original Description',
            'user_id' => $this->user->id,
        ]);

        $topic->update(['description' => 'New Description']);

        $this->assertEquals('Original', $topic->name);
        $this->assertEquals('New Description', $topic->description);
        $this->assertEquals($this->user->id, $topic->user_id);
    }
}
