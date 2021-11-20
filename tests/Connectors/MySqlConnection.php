<?php

namespace Tests\Connectors;

use Illuminate\Database\Connectors\ConnectorInterface;
use Illuminate\Database\Connectors\MySqlConnector;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;

class MySqlConnection extends BaseConnection
{
    protected function grammar(): Grammar
    {
        return new MySqlGrammar();
    }

    protected function connector(): ConnectorInterface
    {
        return new MySqlConnector();
    }
}
