<?php

namespace Tests\Configurations;

use Helldar\MigrateDB\Constants\Drivers;
use PDO;

final class MySQL extends BaseConfiguration
{
    protected function fill(): void
    {
        parent::fill();

        $this->configuration->setHost(env('MYSQL_HOST', 'mysql'));

        $this->configuration->setDriver(Drivers::MYSQL);
        $this->configuration->setPort(3306);

        $this->configuration->setOptions(extension_loaded('pdo_mysql') ? array_filter([
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        ]) : []);
    }
}
