<?php

namespace DragonCode\MigrateDB;

use DragonCode\MigrateDB\Console\Migrate;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
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
