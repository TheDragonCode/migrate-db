<?php

namespace Helldar\MigrateDB\Connectors;

use Illuminate\Database\Connectors\ConnectorInterface;
use Tinderbox\ClickhouseBuilder\Integrations\Laravel\Connection;

final class ClickHouseConnector extends Connection implements ConnectorInterface
{
    public function connect(array $config)
    {
        self::__construct($config);

        return $this;
    }
}
