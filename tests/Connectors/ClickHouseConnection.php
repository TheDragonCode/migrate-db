<?php

namespace Tests\Connectors;

use Helldar\MigrateDB\Connectors\ClickHouseConnector;
use Helldar\MigrateDB\Grammars\ClickHouseGrammar;
use Illuminate\Database\Connectors\ConnectorInterface;

final class ClickHouseConnection extends BaseConnection
{
    protected function grammar()
    {
        return new ClickHouseGrammar();
    }

    protected function connector(): ConnectorInterface
    {
        return new ClickHouseConnector($this->configuration->toArray());
    }
}
