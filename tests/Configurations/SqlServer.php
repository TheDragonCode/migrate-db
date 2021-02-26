<?php

namespace Tests\Configurations;

use Helldar\MigrateDB\Constants\Drivers;

final class SqlServer extends BaseConfiguration
{
    protected $config = [
        'driver'         => Drivers::SQL_SERVER,
        'url'            => null,
        'host'           => '127.0.0.1',
        'port'           => '1433',
        'database'       => 'forge',
        'username'       => 'sa',
        'password'       => '',
        'charset'        => 'utf8',
        'prefix'         => '',
        'prefix_indexes' => true,
    ];

    protected function fill(): void
    {
        parent::fill();

        $this->configuration->setHost(env('SQLSRV_HOST', 'sqlsrv'));
    }
}
