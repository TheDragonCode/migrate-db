<?php

declare(strict_types=1);

namespace Tests\Connectors;

use Illuminate\Database\Connectors\ConnectorInterface;
use Illuminate\Database\Connectors\SqlServerConnector;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Database\Schema\Grammars\SqlServerGrammar;

class SqlServerConnection extends BaseConnection
{
    protected function grammar(): Grammar
    {
        return new SqlServerGrammar(
            $this->databaseConnection()
        );
    }

    protected function connector(): ConnectorInterface
    {
        return new SqlServerConnector();
    }
}
