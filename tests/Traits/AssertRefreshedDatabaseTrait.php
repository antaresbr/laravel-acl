<?php

namespace Antares\Acl\Tests\Traits;

use Antares\Acl\Models\AclGroup;
use Antares\Acl\Models\AclGroupRight;
use Antares\Acl\Models\AclMenu;
use Antares\Acl\Models\AclSession;
use Antares\Acl\Models\AclUserGroup;
use Antares\Acl\Models\AclUserRight;
use Antares\Acl\Models\User;

trait AssertRefreshedDatabaseTrait
{
    private function assertRefreshedDatabase()
    {
        $this->assertCount(1, User::all());
        $this->assertCount(0, AclSession::all());
        $this->assertCount(0, AclMenu::all());
        $this->assertCount(0, AclGroup::all());
        $this->assertCount(0, AclGroupRight::all());
        $this->assertCount(0, AclUserGroup::all());
        $this->assertCount(0, AclUserRight::all());
    }
}
