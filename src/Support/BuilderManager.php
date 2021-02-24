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
        $type       = $this->getType();
        $connection = $this->connection;

        switch ($type) {
            case 'mysql':
                return MySQLBuilder::make($connection);

            default:
                throw new UnknownDatabaseDriverException($type);
        }
    }

    protected function getType(): ?string
    {
        return Config::get("database.connections.{$this->connection}.driver", $this->connection);
    }
}
