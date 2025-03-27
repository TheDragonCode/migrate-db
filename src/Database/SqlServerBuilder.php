<?php

declare(strict_types=1);

namespace DragonCode\MigrateDB\Database;

class SqlServerBuilder extends Builder
{
    protected function tableNameColumn(): string
    {
        return 'name';
    }
}
