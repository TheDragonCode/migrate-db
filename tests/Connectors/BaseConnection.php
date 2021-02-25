<?php

namespace Tests\Connectors;

use Helldar\Support\Concerns\Makeable;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectorInterface;
use Illuminate\Database\Schema\Grammars\Grammar;
use PDO;
use Tests\Configurations\BaseConfiguration;

abstract class BaseConnection
{
    use Makeable;

    /** @var \Tests\Configurations\BaseConfiguration */
    protected $configuration;

    protected $default_database;

    protected $database;

    protected $driver;

    protected $grammar;

    public function of(string $database, string $driver): self
    {
        $this->database = $database;
        $this->driver   = $driver;

        return $this;
    }

    public function configuration(BaseConfiguration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function dropDatabase(string $name = null): self
    {
        $name = $this->database($name);

        $this->query($this->getGrammar()->compileDropDatabaseIfExists($name));

        return $this;
    }

    public function createDatabase(string $name = null): self
    {
        $name = $this->database($name);

        $this->query($this->getGrammar()->compileCreateDatabase($name, $this->databaseConnection()));

        return $this;
    }

    abstract protected function grammar(): Grammar;

    abstract protected function connector(): ConnectorInterface;

    protected function query(string $query): void
    {
        $this->connection()->query($query);
    }

    protected function connection(): PDO
    {
        $config = $this->config();

        return $this->connector()->connect($config);
    }

    protected function config(): array
    {
        $this->configuration->setDatabase($this->default_database);

        return $this->configuration->toArray();
    }

    protected function database(string $name = null): string
    {
        return $name ?: $this->database;
    }

    protected function getGrammar(): Grammar
    {
        if ($this->grammar) {
            return $this->grammar;
        }

        return $this->grammar = $this->grammar();
    }

    protected function databaseConnection(): Connection
    {
        return new Connection($this->connection(), '', '', $this->config());
    }
}
