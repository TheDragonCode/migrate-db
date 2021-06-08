<?php

namespace Helldar\MigrateDB\Contracts\Database;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder as SchemaBuilder;

interface Builder
{
    public function __construct(Connection $connection);

    /** @return \Illuminate\Database\Schema\Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder */
    public function schema(): SchemaBuilder;

    public function getPrimaryKey(string $table): string;

    public function getAllTables(): array;

    public function dropAllTables(): void;

    public function disableForeign(): void;

    public function enableForeign(): void;
}
