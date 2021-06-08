<?php

namespace Tests\Configurations;

use Helldar\MigrateDB\Constants\Drivers;

final class Clickhouse extends BaseConfiguration
{
    protected $config = [
        'driver'   => Drivers::CLICKHOUSE,
        'url'      => null,
        'host'     => '127.0.0.1',
        'port'     => '8123',
        'database' => 'forge',
        'username' => '',
        'password' => '',
        'options'  => [
            'timeout'  => 10,
            'protocol' => 'http',
        ],
    ];
}
