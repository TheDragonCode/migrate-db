<?php

declare(strict_types=1);

namespace Tests\Connectors;

use Illuminate\Database\Connectors\ConnectorInterface;
use Illuminate\Database\Connectors\PostgresConnector;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;

class PostgresConnection extends BaseConnection
{
    protected $default_database = 'postgres';

    public function dropDatabase(?string $name = null): BaseConnection
    {
        $name = $this->database($name);

        $this->query($this->dropSessions($name));
        $this->query($this->getGrammar()->compileDropDatabaseIfExists($name));

        return $this;
    }

    protected function dropSessions(string $name): string
    {
        return "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE pid <> pg_backend_pid() AND datname = '{$name}'";
    }

    protected function grammar(): Grammar
    {
        return new PostgresGrammar(
            $this->databaseConnection()
        );
    }

    protected function connector(): ConnectorInterface
    {
        return new PostgresConnector();
    }
}
