<?php

namespace Tests;

use Helldar\MigrateDB\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Concerns\Connections;
use Tests\Concerns\Database;

abstract class TestCase extends BaseTestCase
{
    use Connections;
    use Database;

    protected function setUp(): void
    {
        $this->freshDatabase();

        parent::setUp();
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->setDatabases($app);
    }

    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }
}
