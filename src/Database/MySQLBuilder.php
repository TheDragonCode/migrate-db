<?php

namespace DragonCode\MigrateDB\Database;

class MySQLBuilder extends Builder
{
    protected function tableNameColumn(): string
    {
        return 'Tables_in_' . $this->database();
    }
}
