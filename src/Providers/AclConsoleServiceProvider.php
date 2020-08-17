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
        $ai_acl_path = 'ai_acl_path';

        $this->publishes([
            "{$ai_acl_path('config/acl.php')}" => config_path('acl.php'),
        ], 'acl-config');

        $this->publishes([
            "{$ai_acl_path('lang')}" => resource_path('lang/vendor/acl'),
        ], 'acl-lang');
    }
}
