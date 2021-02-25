<?php

namespace Helldar\MigrateDB\Database;

final class MySQLBuilder extends Builder
{
    protected function tableNameColumn(): string
    {
        return 'Tables_in_' . $this->database();
    }
}
