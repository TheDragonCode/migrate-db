<?php

namespace Helldar\MigrateDB\Support;

use Helldar\MigrateDB\Builders\MySQLBuilder;
use Helldar\MigrateDB\Contracts\Database\Builder;
use Helldar\MigrateDB\Exceptions\UnknownDatabaseDriverException;
use Illuminate\Support\Facades\Config;

final class BuilderManager
{
    protected $connection;

    public function of(string $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function resolve(): Builder
    {
        switch ($this->getType()) {
            case 'mysql':
                return new MySQLBuilder();

            default:
                throw new UnknownDatabaseDriverException($this->getType());
        }
    }

    protected function getType(): string
    {
        return Config::get("database.connections.{$this->connection}.driver");
    }
}
