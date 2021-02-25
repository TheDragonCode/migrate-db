<?php

namespace Helldar\MigrateDB\Database;

final class PostgresBuilder extends Builder
{
    protected function tableNameColumn(): string
    {
        return 'tablename';
    }
}
