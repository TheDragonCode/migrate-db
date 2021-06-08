<?php

namespace Tests\Connectors;

use Helldar\Support\Concerns\Makeable;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectorInterface;
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

    abstract protected function grammar();

    abstract protected function connector(): ConnectorInterface;

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

    protected function query(string $query): void
    {
        $this->connection()->query($query);
    }

    protected function connection()
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

    /**
     * @return \Illuminate\Database\Schema\Grammars\Grammar|\Tinderbox\ClickhouseBuilder\Query\Grammar
     */
    protected function getGrammar()
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
