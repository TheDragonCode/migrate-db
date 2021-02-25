<?php

namespace Tests\Configurations;

use Helldar\MigrateDB\Constants\Drivers;

final class MySQL extends BaseConfiguration
{
    protected function fill(): void
    {
        parent::fill();

        $this->configuration->setHost(env('MYSQL_HOST', 'mysql'));

        $this->configuration->setDriver(Drivers::MYSQL);
        $this->configuration->setPort(3306);
    }
}
