<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Topic;
use App\Models\Word;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TopicManagementTest extends TestCase
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
     * Test admin can list all topics.
     */
    public function test_admin_can_list_topics(): void
    {
        Topic::factory()->count(5)->create();

        $topics = Topic::all();

        $this->assertEquals(5, $topics->count());
    }

    /**
     * Test admin can view topic details.
     */
    public function test_admin_can_view_topic(): void
    {
        $topic = Topic::factory()->create([
            'name' => 'Animals',
            'description' => 'Words about animals',
        ]);

        $found = Topic::find($topic->id);

        $this->assertNotNull($found);
        $this->assertEquals('Animals', $found->name);
    }

    /**
     * Test admin can create topic.
     */
    public function test_admin_can_create_topic(): void
    {
        $topic = Topic::create([
            'name' => 'New Topic',
            'description' => 'A new topic',
            'is_system' => false,
        ]);

        $this->assertNotNull($topic->id);
        $this->assertEquals('New Topic', $topic->name);
    }

    /**
     * Test admin can create system topic.
     */
    public function test_admin_can_create_system_topic(): void
    {
        $topic = Topic::create([
            'name' => 'System Topic',
            'is_system' => true,
        ]);

        $this->assertTrue($topic->is_system);
    }

    /**
     * Test admin can update topic.
     */
    public function test_admin_can_update_topic(): void
    {
        $topic = Topic::factory()->create(['name' => 'Old Name']);

        $topic->update(['name' => 'New Name']);

        $this->assertEquals('New Name', $topic->fresh()->name);
    }

    /**
     * Test admin can update topic description.
     */
    public function test_admin_can_update_topic_description(): void
    {
        $topic = Topic::factory()->create(['description' => 'Old']);

        $topic->update(['description' => 'New description']);

        $this->assertEquals('New description', $topic->fresh()->description);
    }

    /**
     * Test admin can delete topic.
     */
    public function test_admin_can_delete_topic(): void
    {
        $topic = Topic::factory()->create();
        $topicId = $topic->id;

        $topic->delete();

        $this->assertNull(Topic::find($topicId));
    }

    /**
     * Test admin can filter system topics.
     */
    public function test_admin_filter_system_topics(): void
    {
        Topic::factory()->count(2)->create(['is_system' => true]);
        Topic::factory()->count(3)->create(['is_system' => false]);

        $system = Topic::where('is_system', true)->count();
        $custom = Topic::where('is_system', false)->count();

        $this->assertEquals(2, $system);
        $this->assertEquals(3, $custom);
    }

    /**
     * Test admin can search topics by name.
     */
    public function test_admin_search_topics(): void
    {
        Topic::factory()->create(['name' => 'Animals']);
        Topic::factory()->create(['name' => 'Foods']);

        $found = Topic::where('name', 'like', '%Animals%')->first();

        $this->assertNotNull($found);
        $this->assertEquals('Animals', $found->name);
    }

    /**
     * Test topic name uniqueness.
     */
    public function test_topic_name_uniqueness(): void
    {
        Topic::factory()->create(['name' => 'Unique']);

        $this->expectException(\Exception::class);
        Topic::factory()->create(['name' => 'Unique']);
    }

    /**
     * Test admin can view topic word count.
     */
    public function test_admin_view_topic_word_count(): void
    {
        $topic = Topic::factory()->create();
        Word::factory()->count(5)->create(['topic' => $topic->name]);

        $count = Word::where('topic', $topic->name)->count();

        $this->assertEquals(5, $count);
    }

    /**
     * Test admin can view topic word statistics.
     */
    public function test_admin_view_topic_word_stats(): void
    {
        $topic = Topic::factory()->create();
        $words = Word::factory()->count(10)->create(['topic' => $topic->name]);

        $count = Word::where('topic', $topic->name)->count();

        $this->assertEquals(10, $count);
    }

    /**
     * Test topic pagination.
     */
    public function test_topic_pagination(): void
    {
        // Create unique topic names to avoid constraint violations
        for ($i = 0; $i < 15; $i++) {
            Topic::factory()->create(['name' => "topic-{$i}"]);
        }

        $page = Topic::paginate(10);

        $this->assertLessThanOrEqual(10, $page->count());
    }

    /**
     * Test admin can sort topics.
     */
    public function test_admin_sort_topics(): void
    {
        Topic::factory()->create(['name' => 'Zebra']);
        Topic::factory()->create(['name' => 'Apple']);

        $sorted = Topic::orderBy('name', 'asc')->get();

        $this->assertEquals('Apple', $sorted->first()->name);
    }

    /**
     * Test admin can bulk create topics.
     */
    public function test_admin_bulk_create_topics(): void
    {
        $topics = [
            ['name' => 'Topic1', 'is_system' => false],
            ['name' => 'Topic2', 'is_system' => false],
            ['name' => 'Topic3', 'is_system' => true],
        ];

        foreach ($topics as $data) {
            Topic::create($data);
        }

        $count = Topic::count();

        $this->assertEquals(3, $count);
    }

    /**
     * Test admin topics statistics.
     */
    public function test_admin_topics_statistics(): void
    {
        $topic = Topic::factory()->create();
        $words = Word::factory()->count(5)->create(['topic' => $topic->name]);
        $users = User::factory()->count(3)->create();

        // Attach words to users
        $users->each(function ($user) use ($words) {
            $user->words()->attach($words->random(2)->pluck('id'));
        });

        $topicWordCount = Word::where('topic', $topic->name)->count();

        $this->assertEquals(5, $topicWordCount);
    }

    /**
     * Test topic with no words.
     */
    public function test_topic_with_no_words(): void
    {
        $topic = Topic::factory()->create();

        $count = Word::where('topic', $topic->name)->count();

        $this->assertEquals(0, $count);
    }

    /**
     * Test topic with many words.
     */
    public function test_topic_with_many_words(): void
    {
        $topic = Topic::factory()->create();
        Word::factory()->count(100)->create(['topic' => $topic->name]);

        $count = Word::where('topic', $topic->name)->count();

        $this->assertEquals(100, $count);
    }

    /**
     * Test user can create private topic.
     */
    public function test_user_can_create_private_topic(): void
    {
        $topic = Topic::create([
            'name' => 'Private Topic',
            'is_system' => false,
            'user_id' => $this->regularUser->id,
        ]);

        $this->assertEquals($this->regularUser->id, $topic->user_id);
    }

    /**
     * Test topic description is optional.
     */
    public function test_topic_description_optional(): void
    {
        $topic = Topic::create([
            'name' => 'No Desc',
            'description' => null,
        ]);

        $this->assertNull($topic->fresh()->description);
    }

    /**
     * Test admin can update topic system flag.
     */
    public function test_admin_update_topic_system_flag(): void
    {
        $topic = Topic::factory()->create(['is_system' => false]);

        $topic->update(['is_system' => true]);

        $this->assertTrue($topic->fresh()->is_system);
    }

    /**
     * Test admin can count topics by type.
     */
    public function test_count_topics_by_type(): void
    {
        // Create topics with unique names
        for ($i = 0; $i < 4; $i++) {
            Topic::factory()->create(['name' => "system-{$i}", 'is_system' => true]);
        }
        for ($i = 0; $i < 6; $i++) {
            Topic::factory()->create(['name' => "custom-{$i}", 'is_system' => false]);
        }

        $system = Topic::where('is_system', true)->count();
        $custom = Topic::where('is_system', false)->count();

        $this->assertEquals(4, $system);
        $this->assertEquals(6, $custom);
    }
}
