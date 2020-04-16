<?php

namespace Antares\Acl\Tests;

use Antares\Acl\Models\AclSession;
use Antares\Acl\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

trait DatabaseTrait
{
    use RefreshDatabase;

    private function seedUsers($amount = 10)
    {
        return factory(User::class, $amount)->create();
    }

    private function seedSessions($amount = 20)
    {
        return factory(AclSession::class, $amount)->create();
    }

    private function seedAll()
    {
        $this->seedUsers();
        $this->seedSessions();
    }
}