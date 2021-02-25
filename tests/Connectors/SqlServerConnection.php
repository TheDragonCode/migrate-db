<?php

namespace Tests\Connectors;

use Illuminate\Database\Connectors\ConnectorInterface;
use Illuminate\Database\Connectors\SqlServerConnector;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Database\Schema\Grammars\SqlServerGrammar;

final class SqlServerConnection extends BaseConnection
{
    protected function grammar(): Grammar
    {
        return new SqlServerGrammar();
    }

    protected function connector(): ConnectorInterface
    {
        return new SqlServerConnector();
    }
}
