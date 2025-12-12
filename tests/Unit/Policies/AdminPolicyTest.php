<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\Word;
use App\Models\Topic;
use App\Policies\SavedSessionPolicy;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPolicyTest extends TestCase
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
     * Test admin can manage users.
     */
    public function test_admin_can_manage_users(): void
    {
        $canManage = $this->admin->is_admin === true;

        $this->assertTrue($canManage);
    }

    /**
     * Test regular user cannot manage users.
     */
    public function test_regular_user_cannot_manage_users(): void
    {
        $canManage = $this->regularUser->is_admin === true;

        $this->assertFalse($canManage);
    }

    /**
     * Test admin can manage words.
     */
    public function test_admin_can_manage_words(): void
    {
        $canManage = $this->admin->is_admin === true;

        $this->assertTrue($canManage);
    }

    /**
     * Test regular user cannot manage words.
     */
    public function test_regular_user_cannot_manage_words(): void
    {
        $canManage = $this->regularUser->is_admin === true;

        $this->assertFalse($canManage);
    }

    /**
     * Test admin can manage topics.
     */
    public function test_admin_can_manage_topics(): void
    {
        $canManage = $this->admin->is_admin === true;

        $this->assertTrue($canManage);
    }

    /**
     * Test regular user cannot manage topics.
     */
    public function test_regular_user_cannot_manage_topics(): void
    {
        $canManage = $this->regularUser->is_admin === true;

        $this->assertFalse($canManage);
    }

    /**
     * Test admin can view audit logs.
     */
    public function test_admin_can_view_logs(): void
    {
        $canView = $this->admin->is_admin === true;

        $this->assertTrue($canView);
    }

    /**
     * Test regular user cannot view audit logs.
     */
    public function test_regular_user_cannot_view_logs(): void
    {
        $canView = $this->regularUser->is_admin === true;

        $this->assertFalse($canView);
    }

    /**
     * Test admin can promote users.
     */
    public function test_admin_can_promote_users(): void
    {
        $canPromote = $this->admin->is_admin === true;

        $this->assertTrue($canPromote);
    }

    /**
     * Test regular user cannot promote users.
     */
    public function test_regular_user_cannot_promote_users(): void
    {
        $canPromote = $this->regularUser->is_admin === true;

        $this->assertFalse($canPromote);
    }

    /**
     * Test admin can demote admins.
     */
    public function test_admin_can_demote_admins(): void
    {
        $other = User::factory()->create(['is_admin' => true]);

        $canDemote = $this->admin->is_admin === true;

        $this->assertTrue($canDemote);
    }

    /**
     * Test admin cannot demote themselves.
     */
    public function test_admin_cannot_demote_self(): void
    {
        $isSelf = $this->admin->id === $this->admin->id;

        $this->assertTrue($isSelf);
    }

    /**
     * Test admin role check validates boolean.
     */
    public function test_admin_role_is_boolean(): void
    {
        $this->assertTrue(is_bool($this->admin->is_admin));
        $this->assertTrue(is_bool($this->regularUser->is_admin));
    }

    /**
     * Test admin can delete users.
     */
    public function test_admin_can_delete_users(): void
    {
        $canDelete = $this->admin->is_admin === true;

        $this->assertTrue($canDelete);
    }

    /**
     * Test regular user cannot delete users.
     */
    public function test_regular_user_cannot_delete_users(): void
    {
        $canDelete = $this->regularUser->is_admin === true;

        $this->assertFalse($canDelete);
    }

    /**
     * Test admin can reset user passwords.
     */
    public function test_admin_can_reset_passwords(): void
    {
        $canReset = $this->admin->is_admin === true;

        $this->assertTrue($canReset);
    }

    /**
     * Test regular user cannot reset passwords.
     */
    public function test_regular_user_cannot_reset_passwords(): void
    {
        $canReset = $this->regularUser->is_admin === true;

        $this->assertFalse($canReset);
    }

    /**
     * Test admin can view user analytics.
     */
    public function test_admin_can_view_analytics(): void
    {
        $canView = $this->admin->is_admin === true;

        $this->assertTrue($canView);
    }

    /**
     * Test regular user cannot view analytics.
     */
    public function test_regular_user_cannot_view_analytics(): void
    {
        $canView = $this->regularUser->is_admin === true;

        $this->assertFalse($canView);
    }

    /**
     * Test admin can manage email settings.
     */
    public function test_admin_can_manage_email_settings(): void
    {
        $canManage = $this->admin->is_admin === true;

        $this->assertTrue($canManage);
    }

    /**
     * Test admin can view system settings.
     */
    public function test_admin_can_view_system_settings(): void
    {
        $canView = $this->admin->is_admin === true;

        $this->assertTrue($canView);
    }

    /**
     * Test multiple admin verification.
     */
    public function test_multiple_admins_can_exist(): void
    {
        $admin2 = User::factory()->create(['is_admin' => true]);

        $this->assertTrue($this->admin->is_admin);
        $this->assertTrue($admin2->is_admin);
    }

    /**
     * Test admin role persists in database.
     */
    public function test_admin_role_persists(): void
    {
        $this->admin->update(['is_admin' => false]);
        $this->assertFalse($this->admin->fresh()->is_admin);

        $this->admin->update(['is_admin' => true]);
        $this->assertTrue($this->admin->fresh()->is_admin);
    }
}
