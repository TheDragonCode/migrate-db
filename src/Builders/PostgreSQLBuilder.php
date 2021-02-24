<?php

namespace Helldar\MigrateDB\Builders;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class PostgreSQLBuilder extends BaseBuilder
{
    public function getPrimaryKey(string $table): string
    {
        if ($primary = $this->getPrimary($table)) {
            return $primary->{'Column_name'};
        }

        return $this->getFirstColumn($table);
    }

    protected function getPrimary(string $table)
    {
        return DB::select(DB::raw("SHOW KEYS FROM `{$table}` WHERE Key_name = 'PRIMARY'"));
    }

    protected function getColumns(string $table)
    {
        return DB::select(DB::raw("SHOW COLUMNS FROM `{$table}`"));
    }

    protected function getFirstColumn(string $table): string
    {
        $column = Arr::first($this->getColumns($table));

        return $column->Field;
    }
}
