<?php

namespace Davion190510\MiniCRUDGenerator;

use Illuminate\Support\ServiceProvider;

class MiniCRUDGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/minicrud.php' => config_path('minicrud.php'),
            ], 'config');
        }
        // Registering package commands.
        $this->commands([
            Commands\AddFieldsToView::class,
            Commands\MakeCustomController::class,
            Commands\MakeCustomFeatureTest::class,
            Commands\MakeCustomLanguage::class,
            Commands\MakeCustomModel::class,
            Commands\MakeCustomResource::class,
            Commands\MakeCustomRoot::class,
            Commands\MakeCustomRootLogic::class,
            Commands\MakeCustomRootView::class,
            Commands\MakeCustomService::class,
            Commands\MakeCustomValidation::class,
        ]);
    }

    /**
     * Register the application services.
     */
    public function register()
    { // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/minicrud.php', 'minicrud');

        // Register the main class to use with the facade
        $this->app->singleton('mini-curd-generator', function () {
            return new MiniCRUDGenerator;
        });
    }
}
