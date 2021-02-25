<?php

namespace Helldar\MigrateDB\Database;

final class SqlServerBuilder extends Builder
{
    protected function tableNameColumn(): string
    {
        return 'Tables_in_' . $this->database();
    }
}
