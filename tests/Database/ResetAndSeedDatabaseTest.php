<?php

namespace Antares\Acl\Tests\Database;

use Antares\Acl\Models\AclGroup;
use Antares\Acl\Models\AclGroupRight;
use Antares\Acl\Models\AclMenu;
use Antares\Acl\Models\AclSession;
use Antares\Acl\Models\AclUserGroup;
use Antares\Acl\Models\AclUserRight;
use Antares\Acl\Models\User;
use Antares\Acl\Tests\TestCase;
use Antares\Acl\Tests\Traits\ResetDatabaseTrait;
use PHPUnit\Framework\Attributes\Test;

class ResetAndSeedDatabaseTest extends TestCase
{
    use ResetDatabaseTrait;

    #[Test]
    public function reset_database()
    {
        $this->resetDatabase();
    }

    #[Test]
    public function assert_refreshed_database()
    {
        $this->assertRefreshedDatabase();
    }

    #[Test]
    public function seed_users()
    {
        $data = $this->seedUsers($amount = 30);
        $this->assertInstanceOf(User::class, $data[rand(1, $amount) - 1]);
        $this->assertCount($amount + 1, User::all()); //-- plus one because admin user
    }

    #[Test]
    public function seed_acl_session()
    {
        $data = $this->seedSessions($amount = 20);
        $this->assertInstanceOf(AclSession::class, $data[rand(1, $amount) - 1]);
        $this->assertCount($amount, AclSession::all());
    }

    #[Test]
    public function seed_acl_menus()
    {
        $data = $this->seedMenus($menuAmount = 5, $pathAmount = 3, $optionAmount = 3, $actionAmount = 3);
        $menuCount = $menuAmount * $pathAmount * $optionAmount * $actionAmount; //-- actions
        $menuCount += $menuAmount * $pathAmount * $optionAmount; //-- options
        $menuCount += $menuAmount * $pathAmount; //-- paths
        $menuCount += $menuAmount; //-- menus
        $menuCount++; //-- Root Menu
        $this->assertInstanceOf(AclMenu::class, $data[rand(1, $menuCount) - 1]);
        $this->assertCount($menuCount, AclMenu::all());
    }

    #[Test]
    public function seed_acl_groups()
    {
        $data = $this->seedGroups($amount = 5);
        $this->assertInstanceOf(AclGroup::class, $data[rand(1, $amount) - 1]);
        $this->assertCount($amount, AclGroup::all());
    }

    #[Test]
    public function seed_acl_group_rights()
    {
        $data = $this->seedGroupRights($amount = 5);
        $this->assertInstanceOf(AclGroupRight::class, $data[rand(1, $amount) - 1]);
        $this->assertCount($amount, AclGroupRight::all());
    }

    #[Test]
    public function seed_acl_user_groups()
    {
        $data = $this->seedUserGroups($amount = 5);
        $this->assertInstanceOf(AclUserGroup::class, $data[rand(1, $amount) - 1]);
        $this->assertCount($amount, AclGroup::all());
    }

    #[Test]
    public function seed_acl_user_rights()
    {
        $data = $this->seedUserRights($amount = 5);
        $this->assertInstanceOf(AclUserRight::class, $data[rand(1, $amount) - 1]);
        $this->assertCount($amount, AclGroup::all());
    }

    #[Test]
    public function seed_acl_admin_acls()
    {
        $this->seedAdminAcls();
        $this->assertCount(1, AclGroup::where('id', 999999)->get());
        $this->assertCount(1, AclUserGroup::where('group_id', 999999)->get());
        $this->assertCount(1, AclGroupRight::where(['group_id' => 999999, 'enabled' => true])->get());
    }
}
