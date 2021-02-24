<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

trait Connections
{
    abstract protected function defaultSourceConnectionName(): string;

    abstract protected function defaultTargetConnectionName(): string;

    abstract protected function currentSourceConnection(): string;

    abstract protected function currentTargetConnection(): string;

    /**
     * @return \Illuminate\Database\Schema\Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    protected function sourceConnection(): Builder
    {
        return Schema::connection($this->currentSourceConnection());
    }

    /**
     * @return \Illuminate\Database\Schema\Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    protected function targetConnection(): Builder
    {
        return Schema::connection($this->currentTargetConnection());
    }

    protected function setDatabaseConnections($app): void
    {
        $this->setDatabaseConnection($app, $this->currentSourceConnection(), $this->defaultSourceConnectionName());
        $this->setDatabaseConnection($app, $this->currentTargetConnection(), $this->defaultTargetConnectionName());
    }

    protected function setDatabaseConnection($app, string $name, string $connection): void
    {
        $default = $this->getDefaultConfig($app, $connection);

        Arr::set($default, 'database', $name);

        $app->config->set('database.connections.' . $name, $default);
    }

    protected function getDefaultConfig($app, string $connection): array
    {
        return $app->config->get('database.connections.' . $connection);
    }
}
