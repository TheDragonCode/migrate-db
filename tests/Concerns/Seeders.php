<?php

namespace Tests\Concerns;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait Seeders
{
    protected function fillTables(): void
    {
        $this->fillTable($this->table_foo);
        $this->fillTable($this->table_bar);
        $this->fillTable($this->table_baz);

        if ($this->hasUlid()) {
            $this->fillUlidTable($this->table_ulid);
        }

        if ($this->hasUuid()) {
            $this->fillUuidTable($this->table_uuid);
        }
    }

    protected function fillTable(string $table): void
    {
        DB::connection($this->source_connection)->table($table)->insert([
            ['value' => $table . '_1'],
            ['value' => $table . '_2'],
            ['value' => $table . '_3'],
        ]);
    }

    protected function fillUlidTable(string $table): void
    {
        $ulids = [];

        for ($i = 0; $i < 3; $i++) {
            $ulids[$i] = (string) Str::ulid();

            if (Str::startsWith(Application::VERSION, '12.')) {
                usleep(500);
            }
        }

        DB::connection($this->source_connection)->table($table)->insert([
            ['value' => $table . '_1', 'ulid' => $ulids[0]],
            ['value' => $table . '_2', 'ulid' => $ulids[1]],
            ['value' => $table . '_3', 'ulid' => $ulids[2]],
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
}
