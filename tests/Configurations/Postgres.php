<?php

namespace Tests\Configurations;

use Helldar\MigrateDB\Constants\Drivers;

final class Postgres extends BaseConfiguration
{
    protected function fill(): void
    {
        parent::fill();

        $this->configuration->setHost(env('PGSQL_HOST', 'postgres'));

        $this->configuration->setDriver(Drivers::POSTGRES);
        $this->configuration->setPort(5432);

        $this->configuration->setSchema('public');
        $this->configuration->setSslmode('prefer');
    }
}
