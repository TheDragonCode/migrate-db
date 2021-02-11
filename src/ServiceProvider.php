<?php

namespace Helldar\MigrateDB;

use Helldar\MigrateDB\Console\Migrate;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

final class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->bootCommands();
    }

    protected function bootCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Migrate::class,
            ]);
        }
    }
}
