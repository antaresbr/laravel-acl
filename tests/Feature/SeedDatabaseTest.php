<?php

namespace Antares\Acl\Tests\Feature;

use Antares\Acl\Tests\TestCase;
use Antares\Acl\Tests\Traits\SeedDatabaseTrait;

class SeedDatabaseTest extends TestCase
{
    use SeedDatabaseTrait;

    /** @test */
    public function seed_database()
    {
        $this->seedDatabase();
    }
}
