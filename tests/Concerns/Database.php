<?php

namespace Tests\Concerns;

use Tests\Services\MySqlConnection;

trait Database
{
    use Seeders;

    protected $table_foo = 'foo';

    protected $table_bar = 'bar';

    protected $table_baz = 'baz';

    protected function setDatabases($app): void
    {
        $app->config->set('database.default', $this->source);

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
        $this->createDatabase($this->source);
        $this->createDatabase($this->target);
    }

    protected function createDatabase(string $name): void
    {
        MySqlConnection::make()
            ->of($name)
            ->dropDatabase()
            ->createDatabase();
    }

    protected function cleanTestDatabase(): void
    {
        $this->artisan('migrate')->run();
    }
}
