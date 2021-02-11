<?php

namespace Tests\Concerns;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

trait Database
{
    use Seeders;
    use RefreshDatabase;

    protected $table_foo = 'foo';

    protected $table_bar = 'bar';

    protected $table_baz = 'baz';

    protected function setDatabases($app): void
    {
        $app->config->set('database.default', $this->source);

        $this->setDatabaseConnection($app, $this->source);
        $this->setDatabaseConnection($app, $this->target);
    }

    protected function setDatabaseConnection($app, string $name): void
    {
        $app->config->set('database.connections.' . $name, [
            'driver' => 'mysql',

            'database' => $name,

            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '3306'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),

            "unix_socket"    => '',
            'charset'        => 'utf8mb4',
            'collation'      => 'utf8mb4_unicode_ci',
            'prefix'         => '',
            'prefix_indexes' => true,
            'strict'         => true,
            'engine'         => null,
            'options'        => [],
        ]);
    }

    protected function freshDatabase(): void
    {
        $this->createDatabases();
        $this->cleanTestDatabase();
        $this->loadMigrations();

        $this->fillTables();
    }

    protected function createDatabases(): void
    {
        $this->createDatabase($this->source);
        $this->createDatabase($this->target);
    }

    protected function createDatabase(string $name): void
    {
        Schema::dropDatabaseIfExists($name);
        Schema::createDatabase($name);
    }

    protected function cleanTestDatabase(): void
    {
        $this->artisan('migrate:fresh', $this->migrateFreshUsing())->run();

        $this->app[Kernel::class]->setArtisan(null);

        $this->beginDatabaseTransaction();
    }

    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(
            __DIR__ . '/../fixtures/migrations'
        );
    }
}
