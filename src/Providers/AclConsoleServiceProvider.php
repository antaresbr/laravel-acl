<?php

namespace Antares\Acl\Providers;

use Illuminate\Support\ServiceProvider;

class AclConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            \Antares\Acl\Console\CreateConfigCommand::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishResources();
        }
    }

    protected function publishResources()
    {
        $acl_path = 'acl_path';
        $this->publishes([
            "{$acl_path('config/acl.php')}" => config_path('acl.php'),
        ], 'acl-config');
    }
}
