<?php

namespace Helldar\MigrateDB\Database;

final class ClickHouseBuilder extends Builder
{
    protected function tableNameColumn(): string
    {
        return 'Tables_in_' . $this->database();
    }

    public function getAllTables(): array
    {
        // $tables = $this->schema()->getAllTables();
        //
        // $key = $this->tableNameColumn();
        //
        // return $this->pluckTableNames($this->filteredTables($tables, $key), $key);
    }
}
