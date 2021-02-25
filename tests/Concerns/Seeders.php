<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\DB;

trait Seeders
{
    protected function fillTables(): void
    {
        $this->fillTable($this->table_foo);
        $this->fillTable($this->table_bar);
        $this->fillTable($this->table_baz);
    }

    protected function fillTable(string $table): void
    {
        DB::connection($this->source_connection)->table($table)->insert([
            ['value' => $table . '_1'],
            ['value' => $table . '_2'],
            ['value' => $table . '_3'],
        ]);
    }
}
