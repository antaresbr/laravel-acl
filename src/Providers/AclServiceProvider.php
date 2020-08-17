<?php

namespace Antares\Acl\Providers;

use Antares\Acl\Services\AclGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AclServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFile('acl');

        $this->setAclGuardAndProvider();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(ai_acl_path('lang'), 'acl');

        $this->loadMigrationsFrom(ai_acl_path('database/migrations'));

        $this->extendAuth();

        $this->loadRoutes();
    }

    protected function mergeConfigFile($name)
    {
        $targetFile = ai_acl_path("config/{$name}.php");

        if (is_file($targetFile) and !Config::has($name)) {
            $this->mergeConfigFrom($targetFile, $name);
        }
    }

    protected function setAclGuardAndProvider()
    {
        $id = 'auth.guards.acl';
        if (!Config::has($id)) {
            Config::set($id, config('acl.guard'));
        }

        $id = 'auth.providers.' . config('acl.guard.provider');
        if (!Config::has($id)) {
            Config::set($id, config('acl.provider'));
        }
    }

    protected function extendAuth()
    {
        Auth::extend('acl', function ($app, $name, array $config) {
            return new AclGuard(Auth::createUserProvider($config['provider']), $app->request);
        });
    }

    protected function loadRoutes()
    {
        $attributes = [
            'prefix' => config('acl.route.prefix.api'),
            'namespace' => 'Antares\Acl\Http\Controllers',
        ];
        Route::group($attributes, function () {
            $this->loadRoutesFrom(ai_acl_path('routes/api.php'));
        });
    }
}
