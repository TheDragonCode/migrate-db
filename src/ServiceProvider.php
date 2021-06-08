<?php

namespace Helldar\MigrateDB;

use Bavix\LaravelClickHouse\Database\Eloquent\Model;
use Helldar\MigrateDB\Connectors\ClickHouseConnector;
use Helldar\MigrateDB\Console\Migrate;
use Helldar\MigrateDB\Constants\Drivers;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

final class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->bootCommands();
        $this->bootClickHouse();
    }

    public function register()
    {
        $this->registerClickHouse();
    }

    protected function bootCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Migrate::class,
            ]);
        }
    }

    protected function bootClickHouse(): void
    {
        Model::setConnectionResolver($this->app['db']);
        Model::setEventDispatcher($this->app['events']);
    }

    protected function registerClickHouse(): void
    {
        $this->app->resolving('db', static function (DatabaseManager $db) {
            $db->extend(Drivers::CLICKHOUSE, static function ($config, $name) {
                return new ClickHouseConnector(array_merge($config, compact('name')));
            });
        });
    }
}
