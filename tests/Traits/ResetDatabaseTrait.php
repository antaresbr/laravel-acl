<?php

namespace Antares\Acl\Tests\Traits;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;

trait ResetDatabaseTrait
{
    use CanConfigureMigrationCommands;
    use AssertRefreshedDatabaseTrait;
    use SeedDatabaseTrait;

    /**
     * Reset database test.
     *
     * @return void
     */
    public function resetDatabase()
    {
        $this->artisan('migrate:fresh', $this->migrateUsing())->assertExitCode(0);

        $this->app[Kernel::class]->setArtisan(null);

        RefreshDatabaseState::$migrated = true;

        $this->afterResettingDatabase();
    }

    /**
     * The parameters that should be used when running "migrate".
     *
     * @return array
     */
    protected function migrateUsing()
    {
        return [
            '--seed' => $this->shouldSeed(),
            '--seeder' => $this->seeder(),
        ];
    }

    /**
     * Perform any work that should take place once the database has finished resetting.
     *
     * @return void
     */
    protected function afterResettingDatabase()
    {
        // ...
    }
}
