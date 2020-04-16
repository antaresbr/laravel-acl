<?php

namespace Antares\Acl\Tests\Database;

use Antares\Acl\Models\AclSession;
use Antares\Acl\Models\User;
use Antares\Acl\Tests\DatabaseTrait;
use Antares\Acl\Tests\TestCase;

class DatabaseTest extends TestCase
{
    use DatabaseTrait;

    /** @test */
    public function create_users_with_factory()
    {
        $users = $this->seedUsers($amount = 10);

        $this->assertInstanceOf(User::class, $users[rand(1, $amount) - 1]);
        $this->assertCount($amount + 1, User::all()); //-- plus one because admin user
    }

    /** @test */
    public function create_sessions_with_factory()
    {
        $this->seedUsers();
        $sessions = $this->seedSessions($amount = 20);

        $this->assertInstanceOf(AclSession::class, $sessions[rand(1, $amount) - 1]);
        $this->assertCount($amount, AclSession::all());
    }
}
