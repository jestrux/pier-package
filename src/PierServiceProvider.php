<?php

namespace Jestrux\Pier;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Jestrux\Pier\View\Components\Data;
use Jestrux\Pier\View\Components\DataGrid;
use Jestrux\Pier\View\Components\FilterButton;
use Jestrux\Pier\View\Components\Form;
use Jestrux\Pier\View\Components\SearchInput;

// use Jestrux\Pier\Pier;

class PierServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerResources();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('pier.php'),
            ], 'pier-config');
    
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('pier'),
            ], 'pier-assets');
        }
    }

    /**
     * Register the package resources.
     *
     * @return void
     */
    private function registerResources()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'pier');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewComponentsAs('pier', [
            Data::class,
            FilterButton::class,
            Form::class,
            SearchInput::class,
            DataGrid::class,
        ]);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pier');
        $this->registerRoutes();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        // Route::redirect('/pier-admin', '/pier/editor');
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    protected function routeConfiguration(){
        $prefix = config('pier.prefix');
        $middleware = config('pier.middleware');
        
        $configs = [];
        if($prefix) $configs['prefix'] = $prefix;
        if($middleware) $configs['middleware'] = $middleware;
        
        return $configs;
    }
}
