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
     * @return \Illuminate\Database\Schema\Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    protected function sourceConnection(): Builder
    {
        return Schema::connection($this->source_connection);
    }

    /**
     * @return \Illuminate\Database\Schema\Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
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

    protected function setDatabaseConnection($app, string $name, string $connection): void
    {
        $configurator = $this->getConfigurator($connection);

        $configurator->setDatabase($name);

        $app->config->set('database.connections.' . $name, $configurator->toArray());
    }

    protected function getConfigurator(string $connection): BaseConfiguration
    {
        return Manager::make()->get($connection);
    }
}
