<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    protected function fillUuidTable(string $table): void
    {
        DB::connection($this->source_connection)->table($table)->insert([
            ['value' => $table . '_1', 'uuid' => Str::uuid()->toString()],
            ['value' => $table . '_2', 'uuid' => Str::uuid()->toString()],
            ['value' => $table . '_3', 'uuid' => Str::uuid()->toString()],
        ]);
    }

    protected function fillUlidTable(string $table): void
    {
        DB::connection($this->source_connection)->table($table)->insert([
            ['value' => $table . '_1', 'ulid' => (string) Str::ulid()],
            ['value' => $table . '_2', 'ulid' => (string) Str::ulid()],
            ['value' => $table . '_3', 'ulid' => (string) Str::ulid()],
        ]);
    }
}
