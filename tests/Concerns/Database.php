<?php

namespace Tests\Concerns;

use DragonCode\MigrateDB\Constants\Drivers;
use Illuminate\Support\Facades\Config;
use Tests\Configurations\BaseConfiguration;
use Tests\Configurations\Manager;
use Tests\Connectors\MySqlConnection;
use Tests\Connectors\PostgresConnection;
use Tests\Connectors\SqlServerConnection;

/** @mixin \Tests\Concerns\Connections */
trait Database
{
    use Seeders;

    protected $connectors = [
        Drivers::MYSQL      => MySqlConnection::class,
        Drivers::POSTGRES   => PostgresConnection::class,
        Drivers::SQL_SERVER => SqlServerConnection::class,
    ];

    protected $table_foo = 'foo';

    protected $table_bar = 'bar';

    protected $table_baz = 'baz';

    protected $ulid_key = 'ulid_key';

    protected $uuid_key = 'uuid_key';

    protected $choice_target = 'target';

    protected $choice_source = 'source';

    protected $choices = [
        'target',
        'source',
        'none',
    ];

    protected function setDatabases($app): void
    {
        $this->setDatabaseConnections($app);
    }

    protected function freshDatabase(): void
    {
        $this->createDatabases();

        $this->runMigrations();

        $this->fillTables();
    }

    protected function createDatabases(): void
    {
        $this->createDatabase($this->source_connection, $this->defaultSourceConnectionName());
        $this->createDatabase($this->target_connection, $this->defaultTargetConnectionName());
    }

    protected function createDatabase(string $database, string $driver): void
    {
        $instance = $this->getDatabaseConnector($driver);

        $config = $this->getConnectionConfiguration($database, $driver);

        $instance::make()
            ->of($database, $driver)
            ->configuration($config)
            ->dropDatabase()
            ->createDatabase();
    }

    /**
     * @return string|\Tests\Connectors\BaseConnection
     */
    protected function getDatabaseConnector(string $connection): string
    {
        return $this->connectors[$connection];
    }

    protected function getConnectionConfiguration(string $connection, string $driver): BaseConfiguration
    {
        $config = Config::get('database.connections.' . $connection);

        return Manager::make()
            ->get($driver)
            ->merge($config)
            ->setDatabase();
    }

    protected function runMigrations(): void
    {
        $this->artisan('migrate', ['--database' => $this->source_connection])->run();
    }
}
