<?php

namespace Antares\Acl\Tests\Database;

use Antares\Acl\Models\AclGroup;
use Antares\Acl\Models\AclGroupRight;
use Antares\Acl\Models\AclMenu;
use Antares\Acl\Models\AclSession;
use Antares\Acl\Models\AclUserGroup;
use Antares\Acl\Models\User;
use Antares\Acl\Tests\TestCase;
use Antares\Acl\Tests\Traits\SeedDatabaseTrait;

class DatabaseTest extends TestCase
{
    use SeedDatabaseTrait;

    /** @test */
    public function seed_users()
    {
        $users = $this->seedUsers($amount = 30);
        $this->assertInstanceOf(User::class, $users[rand(1, $amount) - 1]);
        $this->assertCount($amount + 1, User::all()); //-- plus one because admin user
    }

    /** @test */
    public function seed_acl_session()
    {
        $sessions = $this->seedSessions($amount = 20);
        $this->assertInstanceOf(AclSession::class, $sessions[rand(1, $amount) - 1]);
        $this->assertCount($amount, AclSession::all());
    }

    /** @test */
    public function seed_acl_menus()
    {
        $menus = $this->seedMenus($menuAmount = 5, $pathAmount = 3, $optionAmount = 3, $actionAmount = 3);
        $menuCount = $menuAmount * $pathAmount * $optionAmount * $actionAmount; //-- actions
        $menuCount += $menuAmount * $pathAmount * $optionAmount; //-- options
        $menuCount += $menuAmount * $pathAmount; //-- paths
        $menuCount += $menuAmount; //-- menus
        $menuCount++; //-- Root Menu
        $this->assertInstanceOf(AclMenu::class, $menus[rand(1, $menuCount) - 1]);
        $this->assertCount($menuCount, AclMenu::all());
    }

    /** @test */
    public function seed_acl_groups()
    {
        $groups = $this->seedGroups($amount = 5);
        $this->assertInstanceOf(AclGroup::class, $groups[rand(1, $amount) - 1]);
        $this->assertCount($amount, AclGroup::all());
    }

    /** @test */
    public function seed_acl_group_rights()
    {
        $groupRights = $this->seedGroupRights($amount = 5);
        $this->assertInstanceOf(AclGroupRight::class, $groupRights[rand(1, $amount) - 1]);
        $this->assertCount($amount, AclGroupRight::all());
    }

    /** @test */
    public function seed_acl_user_groups()
    {
        $userGroups = $this->seedUserGroups($amount = 5);
        $this->assertInstanceOf(AclUserGroup::class, $userGroups[rand(1, $amount) - 1]);
        $this->assertCount($amount, AclGroup::all());
    }

    /** @test */
    public function seed_acl_admin_acls()
    {
        $this->seedAdminAcls();
        $this->assertCount(1, AclGroup::where('id', 999999)->get());
        $this->assertCount(1, AclUserGroup::where('group_id', 999999)->get());
        $this->assertCount(1, AclGroupRight::where(['group_id' => 999999, 'enabled' => true])->get());
    }
}
