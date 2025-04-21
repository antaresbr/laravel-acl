<?php

namespace Antares\Acl\Tests\Database;

use Antares\Acl\Tests\TestCase;
use Antares\Acl\Tests\Traits\RefreshDatabaseTrait;
use PHPUnit\Framework\Attributes\Test;

class RefreshDatabaseTest extends TestCase
{
    use RefreshDatabaseTrait;

    #[Test]
    public function refreshed_database()
    {
        $this->assertRefreshedDatabase();
    }
}
