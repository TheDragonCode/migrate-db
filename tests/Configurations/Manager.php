<?php

namespace Tests\Configurations;

use Helldar\MigrateDB\Constants\Drivers;
use Helldar\Support\Concerns\Makeable;

final class Manager
{
    use Makeable;

    protected $available = [
        Drivers::MYSQL    => MySQL::class,
        Drivers::POSTGRES => Postgres::class,
        Drivers::SQLSRV   => SqlServer::class,
    ];

    public function get(string $driver): BaseConfiguration
    {
        $config = $this->available[$driver];

        return $this->resolve($config);
    }

    /**
     * @param  \Tests\Configurations\BaseConfiguration|string  $instance
     *
     * @return \Tests\Configurations\BaseConfiguration
     */
    protected function resolve(string $instance): BaseConfiguration
    {
        return $instance::make($this->configuration());
    }

    protected function configuration(): Configuration
    {
        return Configuration::make();
    }
}
