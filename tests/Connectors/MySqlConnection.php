<?php

namespace Tests\Connectors;

use Illuminate\Database\Connectors\ConnectorInterface;
use Illuminate\Database\Connectors\MySqlConnector;

final class MySqlConnection extends BaseConnection
{
    protected function connector(): ConnectorInterface
    {
        return new MySqlConnector();
    }
}
