<?php

namespace Tests\Concerns;

use Helldar\MigrateDB\Constants\Types;
use Tests\Connectors\MySqlConnection;
use Tests\Connectors\PostgresConnection;
use Tests\Connectors\SqlServerConnection;

/** @mixin \Tests\Concerns\Connections */
trait Database
{
    use Seeders;

    protected $connectors = [
        Types::SQLSRV   => SqlServerConnection::class,
        Types::MYSQL    => MySqlConnection::class,
        Types::POSTGRES => PostgresConnection::class,
    ];

    protected $table_foo = 'foo';

    protected $table_bar = 'bar';

    protected $table_baz = 'baz';

    protected function setDatabases($app): void
    {
        $this->setDatabaseConnections($app);
    }

    protected function freshDatabase(): void
    {
        $this->createDatabases();

        $this->cleanTestDatabase();

        $this->fillTables();
    }

    protected function createDatabases(): void
    {
        $this->createDatabase($this->source_connection, $this->defaultSourceConnectionName());
        $this->createDatabase($this->target_connection, $this->defaultTargetConnectionName());
    }

    protected function createDatabase(string $database, string $connection): void
    {
        $instance = $this->getDatabaseConnector($connection);

        $instance::make()
            ->of($database, $connection)
            ->dropDatabase()
            ->createDatabase();
    }

    /**
     * @param  string  $connection
     *
     * @return string|\Tests\Connectors\BaseConnection
     */
    protected function getDatabaseConnector(string $connection): string
    {
        return $this->connectors[$connection];
    }

    protected function cleanTestDatabase(): void
    {
        $this->artisan('migrate', ['--database' => $this->source_connection])->run();
    }
}
