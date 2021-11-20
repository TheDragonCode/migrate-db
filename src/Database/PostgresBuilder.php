<?php

namespace DragonCode\MigrateDB\Database;

class PostgresBuilder extends Builder
{
    protected function tableNameColumn(): string
    {
        return 'tablename';
    }
}
