<?php

namespace Helldar\MigrateDB\Database;

use Helldar\MigrateDB\Constants\Types;
use Helldar\MigrateDB\Contracts\Database\Builder as BuilderContract;
use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

final class Manager
{
    protected $connection;

    protected $builders = [
        Types::SQLSRV   => SqlServerBuilder::class,
        Types::MYSQL    => MySQLBuilder::class,
        Types::POSTGRES => PostgresBuilder::class,
    ];

    public function of(string $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function get(): BuilderContract
    {
        $builder = $this->getBuilder();

        return $builder::make($this->connection());
    }

    /**
     * @return \Helldar\MigrateDB\Database\Builder|string
     */
    protected function getBuilder(): string
    {
        return $this->builders[$this->driver()];
    }

    protected function connection(): Connection
    {
        return $this->factory()->make($this->config());
    }

    protected function factory(): ConnectionFactory
    {
        return new ConnectionFactory($this->container());
    }

    protected function container(): Container
    {
        return Container::getInstance();
    }

    protected function config(): array
    {
        $key = 'database.connections.' . $this->connection;

        if (Config::has($key)) {
            return Config::get($key);
        }

        throw new InvalidArgumentException("Unsupported driver [{$this->connection}].");
    }

    protected function driver(): string
    {
        return Arr::get($this->config(), 'driver');
    }
}
