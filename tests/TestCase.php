<?php

namespace Tests;

use DragonCode\MigrateDB\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Concerns\Connections;
use Tests\Concerns\Database;
use Tests\Providers\TestServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use Connections;
    use Database;

    protected function setUp(): void
    {
        parent::setUp();

        $this->freshDatabase();
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->setDatabases($app);
    }

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
            TestServiceProvider::class,
        ];
    }
}
