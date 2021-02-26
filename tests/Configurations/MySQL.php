<?php

namespace Tests\Configurations;

use Helldar\MigrateDB\Constants\Drivers;

final class MySQL extends BaseConfiguration
{
    protected $config = [
        'driver'         => Drivers::MYSQL,
        'url'            => null,
        'host'           => '127.0.0.1',
        'port'           => '3306',
        'database'       => 'forge',
        'username'       => 'root',
        'password'       => 'root',
        'unix_socket'    => '',
        'charset'        => 'utf8mb4',
        'collation'      => 'utf8mb4_unicode_ci',
        'prefix'         => '',
        'prefix_indexes' => true,
        'strict'         => true,
        'engine'         => null,
        'options'        => [],
    ];

    protected function fill(): void
    {
        parent::fill();

        $this->configuration->setHost(env('MYSQL_HOST', '127.0.0.1'));

        $this->configuration->setUsername(env('DB_USERNAME'));
    }
}
