<?php

namespace Tests\Connectors;

use Illuminate\Database\Connectors\ConnectorInterface;
use Illuminate\Database\Connectors\PostgresConnector;

final class PostgresConnection extends BaseConnection
{
    protected function connector(): ConnectorInterface
    {
        return new PostgresConnector();
    }
}
