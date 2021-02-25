<?php

namespace Tests\Configurations;

use Helldar\MigrateDB\Constants\Drivers;

final class SqlServer extends BaseConfiguration
{
    protected function fill(): void
    {
        parent::fill();

        $this->configuration->setHost(env('SQLSRV_HOST', 'sqlsrv'));

        $this->configuration->setDriver(Drivers::SQLSRV);
        $this->configuration->setPort(1433);

        $this->configuration->setUsername('sa');
    }
}
