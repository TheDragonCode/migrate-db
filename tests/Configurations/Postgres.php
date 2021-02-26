<?php

namespace Tests\Configurations;

use Helldar\MigrateDB\Constants\Drivers;

final class Postgres extends BaseConfiguration
{
    protected $config = [
        'driver'         => Drivers::POSTGRES,
        'url'            => null,
        'host'           => '127.0.0.1',
        'port'           => '5432',
        'database'       => 'forge',
        'username'       => 'root',
        'password'       => 'root',
        'charset'        => 'utf8',
        'prefix'         => '',
        'prefix_indexes' => true,
        'schema'         => 'public',
        'sslmode'        => 'prefer',
    ];

    protected function fill(): void
    {
        parent::fill();

        $this->configuration->setHost(env('PGSQL_HOST', '127.0.0.1'));
    }
}
