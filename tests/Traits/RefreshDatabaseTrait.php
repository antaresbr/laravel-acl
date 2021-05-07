<?php

namespace Antares\Acl\Tests\Traits;

use Antares\Acl\Models\AclGroup;
use Antares\Acl\Models\AclGroupRight;
use Antares\Acl\Models\AclMenu;
use Antares\Acl\Models\AclSession;
use Antares\Acl\Models\AclUserGroup;
use Antares\Acl\Models\AclUserRight;
use Antares\Acl\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

trait RefreshDatabaseTrait
{
    use RefreshDatabase;

    private function assert_refreshed_database()
    {
        $this->assertCount(1, User::all());
        $this->assertCount(0, AclSession::all());
        $this->assertCount(0, AclMenu::all());
        $this->assertCount(0, AclGroup::all());
        $this->assertCount(0, AclGroupRight::all());
        $this->assertCount(0, AclUserGroup::all());
        $this->assertCount(0, AclUserRight::all());
    }

    /** @test */
    public function flag_migrated_to_false()
    {
        RefreshDatabaseState::$migrated = false;
        $this->assertFalse(RefreshDatabaseState::$migrated);
    }

    /**
     * @test
     * @depends flag_migrated_to_false
     */
    public function refreshed_database()
    {
        $this->assert_refreshed_database();
    }
}
