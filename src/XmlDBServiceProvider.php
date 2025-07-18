<?php

namespace KamiYuri\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\DatabaseManager;
use KamiYuri\Laravel\Database\Connection\XmlConnection;

class XmlDBServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('xmldb', function ($app) {
            return new XmlConnection($app['config']['database.connections.xmldb']);
        });

        $this->app->resolving('db', function (DatabaseManager $db) {
            $db->extend('xmldb', function ($config, $name) {
                return new XmlConnection($config, $name);
            });
        });
    }

    public function boot()
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/Config/xmldb.php' => config_path('xmldb.php'),
        ], 'config');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\XmlDBCommand::class,
            ]);
        }
    }
}
