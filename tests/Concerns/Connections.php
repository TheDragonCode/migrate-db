<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;
use Tests\Configurations\BaseConfiguration;
use Tests\Configurations\Manager;

trait Connections
{
    protected $source_connection = 'foo_db';

    protected $target_connection = 'bar_db';

    abstract protected function defaultSourceConnectionName(): string;

    abstract protected function defaultTargetConnectionName(): string;

    /**
     * @return Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    protected function sourceConnection(): Builder
    {
        return Schema::connection($this->source_connection);
    }

    /**
     * @return Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    protected function targetConnection(): Builder
    {
        return Schema::connection($this->target_connection);
    }

    protected function setDatabaseConnections($app): void
    {
        $this->setDatabaseConnection($app, $this->source_connection, $this->defaultSourceConnectionName());
        $this->setDatabaseConnection($app, $this->target_connection, $this->defaultTargetConnectionName());
    }

    protected function setDatabaseConnection($app, string $connection, string $default_connection): void
    {
        $configurator = $this->getConfigurator($default_connection);

        $configurator->setDatabase($connection);

        $app->config->set('database.connections.' . $connection, $configurator->toArray());
    }

    protected function getConfigurator(string $driver): BaseConfiguration
    {
        return Manager::make()->get($driver);
    }
}
