<?php

namespace Jestrux\Pier;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

use Jestrux\Pier\View\Components\ActionButtons;
use Jestrux\Pier\View\Components\AddButton;
use Jestrux\Pier\View\Components\Data;
use Jestrux\Pier\View\Components\DataGrid;
use Jestrux\Pier\View\Components\FilterButton;
use Jestrux\Pier\View\Components\Form;
use Jestrux\Pier\View\Components\FormField;
use Jestrux\Pier\View\Components\Grid;
use Jestrux\Pier\View\Components\Modal;
use Jestrux\Pier\View\Components\SearchInput;
use Jestrux\Pier\View\Components\Stack;
use Jestrux\Pier\View\Components\Table;

use Jestrux\Pier\View\Components\Livewire\CMS;
use Jestrux\Pier\View\Components\Livewire\DataTable;
use Jestrux\Pier\View\Components\Livewire\Table as LivewireTable;

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
        require __DIR__ . '/helpers.php';
        
        $this->registerDirectives();
        $this->registerResources();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('pier.php'),
            ], 'pier-config');

            $this->publishes([
                __DIR__ . '/../resources/assets' => public_path('pier'),
            ], 'pier-assets');
        }
    }

    /**
     * Register the package directives.
     *
     * @return void
     */
    private function registerDirectives()
    {
        function pierDataDirective($expression)
        {
            return <<<PHP
                <?php
                    \$expression = $expression;

                    ob_start();

                    (function() {
                        \$args = func_get_args();

                        \$withModel = count(\$args) > 1 && \$args[1];

                        \$arg = \$args[0];
                        \$model = \$arg;
                        \$filters = null;

                        if(is_array(\$arg)) {
                            \$model = \$arg['model'];
                            \$filters = \$arg;
                        }

                        \$__res = \$withModel 
                            ? \Jestrux\Pier\PierData::model(model: \$model, filters: \$filters)
                            :\Jestrux\Pier\PierData::browse(model: \$model, filters: \$filters);

                        \$model = \$__res['model'] ?? null;

                        extract(\$__res);
                        
                        extract(\$__res['pagination']);

                        if(\$withModel && \$model) {
                            extract([
                                'name' => \$model->name,
                                'mainField' => \$model->display_field,
                                'fields' => \$model->fields,
                                'settings' => \$model->settings,
                            ]);
                        }
                ?>
            PHP;
        }

        Blade::directive('pierdata', function ($expression) {
            return pierDataDirective($expression);
        });

        Blade::directive('endpierdata', function () {
            return <<<PHP
                <?php })(\$expression); echo ob_get_clean(); ?>
            PHP;
        });

        Blade::directive('piermodel', function ($expression) {
            return pierDataDirective($expression);
        });

        Blade::directive('endpiermodel', function () {
            return <<<PHP
                <?php })(\$expression, true); echo ob_get_clean(); ?>
            PHP;
        });

        Blade::directive('pierrow', function ($expression) {
            return <<<PHP
                <?php
                    \$expression = $expression;

                    ob_start();

                    (function() {
                        \$args = func_get_args();

                        \$arg = \$args[0];
                        \$model = array_is_list(\$arg) ? \$arg[0] : \$arg['model'];
                        \$rowId = array_is_list(\$arg) ? \$arg[1] : \$arg['rowId'];

                        \$__res = \Jestrux\Pier\PierData::row(model: \$model, rowId: \$rowId);

                        extract(\$__res);
                ?>
            PHP;
        });

        Blade::directive('endpierrow', function () {
            return <<<PHP
                <?php })(\$expression, true); echo ob_get_clean(); ?>
            PHP;
        });
    }

    /**
     * Register the package resources.
     *
     * @return void
     */
    private function registerResources()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'pier');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Livewire::component('pier-datatable', DataTable::class);
        Livewire::component('pier-table', LivewireTable::class);
        Livewire::component('pier-cms', CMS::class);

        $this->loadViewComponentsAs('pier', [
            Data::class,
            FilterButton::class,
            Form::class,
            SearchInput::class,
            AddButton::class,
            ActionButtons::class,
            DataGrid::class,
            Grid::class,
            Modal::class,
            FormField::class,
            Stack::class,
            Table::class,
        ]);
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'pier');
        $this->registerRoutes();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/excluded.php');

        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            $this->loadRoutesFrom(__DIR__ . '/../routes/pier-internals.php');
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    protected function routeConfiguration(): array
    {
        $prefix = config('pier.prefix');
        $middleware = config('pier.middleware');

        $configs = [];
        if ($prefix) $configs['prefix'] = $prefix;
        if ($middleware) $configs['middleware'] = $middleware;

        return $configs;
    }
}
