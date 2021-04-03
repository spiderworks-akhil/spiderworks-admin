<?php

namespace Spiderworks\Webadmin;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

use Yajra\DataTables\DataTablesServiceProvider;
use Intervention\Image\ImageServiceProvider;
use Arrilot\Widgets\ServiceProvider as ArrilotServiceProvider;

use Artisan;

use Illuminate\Console\Scheduling\Schedule;

class WebadminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(DataTablesServiceProvider::class);
        $this->app->register(ImageServiceProvider::class);
        $this->app->register(ArrilotServiceProvider::class);
        
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'webadmin');
        $this->publishes([
            __DIR__.'/resources/views' => base_path('resources/views/spiderworks/webadmin'),
        ]);
        $this->publishes([
            __DIR__.'/assets' => base_path('public/webadmin'),
        ]);
        $this->publishes([
            __DIR__ . '/config' => base_path('config')
        ], 'config');





    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make('Spiderworks\Webadmin\Controllers\BaseController');
        $this->app->make('Spiderworks\Webadmin\Controllers\BlogController');
        $this->app->make('Spiderworks\Webadmin\Controllers\WebadminController');
        $this->app->make('Spiderworks\Webadmin\Controllers\MediaController');
    }
}
