<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

trait Connections
{
    protected $source = 'foo_db';

    protected $target = 'bar_db';

    /**
     * @return \Illuminate\Database\Schema\Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    protected function sourceConnection(): Builder
    {
        return Schema::connection($this->source);
    }

    /**
     * @return \Illuminate\Database\Schema\Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    protected function targetConnection(): Builder
    {
        return Schema::connection($this->target);
    }

    protected function setDatabaseConnections($app): void
    {
        $this->setDatabaseConnection($app, $this->source);
        $this->setDatabaseConnection($app, $this->target);
    }

    protected function setDatabaseConnection($app, string $name): void
    {
        $default = $this->getDefaultConfig($app);

        Arr::set($default, 'database', $name);

        $app->config->set('database.connections.' . $name, $default);
    }

    protected function getDefaultConfig($app): array
    {
        return $app->config->get('database.connections.mysql');
    }
}
