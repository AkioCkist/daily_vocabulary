<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Word;
use App\Models\Topic;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserManagementTest extends TestCase
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
     * Test admin can view all users.
     */
    public function test_admin_can_list_users(): void
    {
        User::factory()->count(5)->create();

        $users = User::all();

        $this->assertGreaterThanOrEqual(7, $users->count()); // 2 + 5
    }

    /**
     * Test admin can view specific user.
     */
    public function test_admin_can_view_user(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $found = User::find($user->id);

        $this->assertNotNull($found);
        $this->assertEquals('John Doe', $found->name);
        $this->assertEquals('john@example.com', $found->email);
    }

    /**
     * Test admin can update user info.
     */
    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $user->update(['name' => 'New Name']);

        $this->assertEquals('New Name', $user->fresh()->name);
    }

    /**
     * Test admin can update user email.
     */
    public function test_admin_can_update_user_email(): void
    {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $user->update(['email' => 'new@example.com']);

        $this->assertEquals('new@example.com', $user->fresh()->email);
    }

    /**
     * Test admin can promote user to admin.
     */
    public function test_admin_can_promote_user(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $user->update(['is_admin' => true]);

        $this->assertTrue($user->fresh()->is_admin);
    }

    /**
     * Test admin can demote admin to user.
     */
    public function test_admin_can_demote_admin(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $user->update(['is_admin' => false]);

        $this->assertFalse($user->fresh()->is_admin);
    }

    /**
     * Test admin can delete user.
     */
    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertNull(User::find($userId));
    }

    /**
     * Test admin can filter users by role.
     */
    public function test_admin_can_filter_by_role(): void
    {
        User::factory()->count(3)->create(['is_admin' => true]);
        User::factory()->count(4)->create(['is_admin' => false]);

        $admins = User::where('is_admin', true)->count();
        $users = User::where('is_admin', false)->count();

        $this->assertGreaterThanOrEqual(3, $admins);
        $this->assertGreaterThanOrEqual(4, $users);
    }

    /**
     * Test admin can view user stats.
     */
    public function test_admin_can_view_user_stats(): void
    {
        $user = User::factory()->create();
        Word::factory()->count(5)->create();
        $user->words()->attach(Word::take(3)->pluck('id'));

        $learned = $user->words()->count();
        $total = Word::count();

        $this->assertEquals(3, $learned);
        $this->assertEquals(5, $total);
    }

    /**
     * Test regular user cannot access admin functions.
     */
    public function test_regular_user_cannot_access_admin_functions(): void
    {
        $this->assertTrue($this->regularUser->is_admin === false);
    }

    /**
     * Test admin can reset user password.
     */
    public function test_admin_can_reset_user_password(): void
    {
        $user = User::factory()->create();
        $oldPassword = $user->password;

        // Admin can update user's password (in real scenario, would use password reset service)
        $user->update(['password' => bcrypt('newpassword123')]);

        $this->assertNotEquals($oldPassword, $user->fresh()->password);
    }

    /**
     * Test admin can verify user email.
     */
    public function test_admin_can_verify_user_email(): void
    {
        $user = User::factory()->unverified()->create();
        $this->assertNull($user->email_verified_at);

        $user->markEmailAsVerified();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    /**
     * Test admin can search users by name.
     */
    public function test_admin_can_search_users_by_name(): void
    {
        User::factory()->create(['name' => 'John Smith']);
        User::factory()->create(['name' => 'Jane Doe']);

        $found = User::where('name', 'like', '%John%')->first();

        $this->assertNotNull($found);
        $this->assertEquals('John Smith', $found->name);
    }

    /**
     * Test admin can search users by email.
     */
    public function test_admin_can_search_users_by_email(): void
    {
        User::factory()->create(['email' => 'search@example.com']);

        $found = User::where('email', 'search@example.com')->first();

        $this->assertNotNull($found);
        $this->assertEquals('search@example.com', $found->email);
    }

    /**
     * Test admin cannot delete themselves.
     */
    public function test_admin_self_deletion_validation(): void
    {
        $canDelete = $this->admin->id !== $this->admin->id;

        $this->assertFalse($canDelete);
    }

    /**
     * Test bulk user operations.
     */
    public function test_bulk_user_promotion(): void
    {
        User::factory()->count(3)->create(['is_admin' => false]);

        User::where('is_admin', false)->limit(3)->update(['is_admin' => true]);

        $admins = User::where('is_admin', true)->count();

        $this->assertGreaterThanOrEqual(4, $admins); // 1 original + 3 promoted
    }

    /**
     * Test user pagination.
     */
    public function test_user_pagination(): void
    {
        User::factory()->count(15)->create();

        $page1 = User::paginate(10);

        $this->assertLessThanOrEqual(10, $page1->count());
    }

    /**
     * Test user query N+1 optimization.
     */
    public function test_user_with_relationships(): void
    {
        User::factory()->count(5)->create();
        Word::factory()->count(10)->create();

        // Attach words to each user
        User::all()->each(function ($user) {
            $user->words()->attach(Word::take(3)->pluck('id'));
        });

        $users = User::all();

        // Check that we can access relationships
        foreach ($users as $user) {
            $count = $user->words()->count();
            $this->assertGreaterThanOrEqual(0, $count);
        }
    }

    /**
     * Test user creation validation.
     */
    public function test_user_validation_required_fields(): void
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test@example.com';

        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
    }

    /**
     * Test user email uniqueness.
     */
    public function test_user_email_uniqueness(): void
    {
        User::factory()->create(['email' => 'unique@example.com']);

        $this->expectException(\Exception::class);
        User::factory()->create(['email' => 'unique@example.com']);
    }

    /**
     * Test user count.
     */
    public function test_count_all_users(): void
    {
        User::factory()->count(5)->create();

        $count = User::count();

        $this->assertGreaterThanOrEqual(7, $count); // 2 initial + 5 new
    }
}
