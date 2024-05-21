<?php

namespace Antares\Acl\Tests\Traits;

use Illuminate\Foundation\Testing\RefreshDatabase;

trait RefreshDatabaseTrait
{
    use AssertRefreshedDatabaseTrait;
    use RefreshDatabase;
    use SeedDatabaseTrait;
}
