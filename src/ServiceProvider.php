<?php

namespace Kamiyuri\Laravel\XmlDB;

use Illuminate\Support\ServiceProvider;
//use Illuminate\Database\DatabaseManager;
//use Illuminate\Foundation\Console\AboutCommand;
//use Kamiyuri\XmlDb\Console\Commands\InstallCommand;
//use Kamiyuri\XmlDb\Console\Commands\BackupCommand;
//use Kamiyuri\XmlDb\Console\Commands\RestoreCommand;
//use Kamiyuri\XmlDb\Console\Commands\OptimizeCommand;
//use Kamiyuri\XmlDb\View\Components\XmlDebugger;
//use Illuminate\Support\Facades\Blade;

class ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__.'/../config/xmldb.php', 'xmldb'
        );

        // Register database connection
        $this->app->resolving('db', function (DatabaseManager $db) {
            $db->extend('xmldb', function ($config, $name) {
                $connector = new Connector();
                return $connector->connect($config);
            });
        });

        // Register core services
        $this->app->singleton('xmldb.file-manager', function ($app) {
            return new Support\FileManager($app['config']['xmldb']);
        });

        $this->app->singleton('xmldb.hierarchy-manager', function ($app) {
            return new Support\HierarchyManager($app['config']['xmldb']);
        });
    }

    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/xmldb.php' => config_path('xmldb.php'),
        ], 'xmldb-config');

//        // Publish migrations
//        $this->publishesMigrations([
//            __DIR__.'/../database/migrations' => database_path('migrations'),
//        ], 'xmldb-migrations');
//
//        // Load routes
//        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
//
//        // Load views
//        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xmldb');
//
//        // Publish views
//        $this->publishes([
//            __DIR__.'/../resources/views' => resource_path('views/vendor/xmldb'),
//        ], 'xmldb-views');
//
//        // Load translations
//        $this->loadTranslationsFrom(__DIR__.'/../lang', 'xmldb');
//
//        // Publish language files
//        $this->publishes([
//            __DIR__.'/../lang' => $this->app->langPath('vendor/xmldb'),
//        ], 'xmldb-lang');
//
//        // Publish public assets
//        $this->publishes([
//            __DIR__.'/../public' => public_path('vendor/xmldb'),
//        ], 'xmldb-assets');
//
//        // Register commands
//        if ($this->app->runningInConsole()) {
//            $this->commands([
//                InstallCommand::class,
//                BackupCommand::class,
//                RestoreCommand::class,
//                OptimizeCommand::class,
//            ]);
//
//            // Register optimize commands
//            $this->optimizes(
//                optimize: 'xmldb:optimize',
//                clear: 'xmldb:clear'
//            );
//        }
//
//        // Register Blade components
//        Blade::component('xmldb-debugger', XmlDebugger::class);
//
//        // Add to About command
//        AboutCommand::add('XML Database', fn () => [
//            'Version' => '1.0.0',
//            'Connection' => config('xmldb.default'),
//            'Path' => config('xmldb.connections.xmldb.path'),
//        ]);

        // Publishing file groups
        $this->publishes([
            __DIR__.'/../config/xmldb.php' => config_path('xmldb.php')
        ], 'xmldb-config');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/xmldb')
        ], 'xmldb-public');
    }
}
