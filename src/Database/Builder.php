<?php

namespace Helldar\MigrateDB\Database;

use Helldar\MigrateDB\Contracts\Database\Builder as BuilderContract;
use Helldar\Support\Concerns\Makeable;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Arr;
use stdClass;

abstract class Builder implements BuilderContract
{
    use Makeable;

    /** @var \Illuminate\Database\Connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Illuminate\Database\Schema\Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    public function schema(): SchemaBuilder
    {
        return $this->connection->getSchemaBuilder();
    }

    public function getAllTables(): array
    {
        $tables = $this->schema()->getAllTables();

        $key = $this->tableNameColumn();

        return $this->pluckTableNames($this->filteredTables($tables, $key), $key);
    }

    public function dropAllTables(): void
    {
        $this->schema()->dropAllTables();
    }

    public function disableForeign(): void
    {
        $this->schema()->disableForeignKeyConstraints();
    }

    public function enableForeign(): void
    {
        $this->schema()->enableForeignKeyConstraints();
    }

    public function getPrimaryKey(string $table): string
    {
        $columns = $this->columns($table);

        return Arr::first($columns);
    }

    abstract protected function tableNameColumn(): string;

    protected function columns(string $table): array
    {
        return $this->schema()->getColumnListing($table);
    }

    protected function filteredTables(array $tables, string $key): array
    {
        return array_filter($tables, static function (stdClass $table) use ($key) {
            return $table->{$key} !== 'migrations';
        });
    }

    protected function pluckTableNames(array $tables, string $key): array
    {
        return array_map(static function ($table) use ($key) {
            return $table->{$key};
        }, $tables);
    }

    protected function database(): string
    {
        return $this->connection->getDatabaseName();
    }
}
