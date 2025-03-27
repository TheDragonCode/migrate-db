<?php

declare(strict_types=1);

namespace DragonCode\MigrateDB\Database;

class PostgresBuilder extends Builder
{
    protected function tableNameColumn(): string
    {
        return 'tablename';
    }
}
