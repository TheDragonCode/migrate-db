<?php

namespace Helldar\MigrateDB\Database;

use Helldar\MigrateDB\Contracts\Database\Builder as BuilderContract;
use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

final class Manager
{
    protected $connection;

    public function of(string $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function get(): BuilderContract
    {
        return Builder::make($this->connection());
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
}
