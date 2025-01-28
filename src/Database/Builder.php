<?php

namespace DragonCode\MigrateDB\Database;

use DragonCode\Contracts\MigrateDB\Builder as BuilderContract;
use DragonCode\Support\Concerns\Makeable;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Arr;
use stdClass;

abstract class Builder implements BuilderContract
{
    use Makeable;

    /** @var Connection */
    protected $connection;

    abstract protected function tableNameColumn(): string;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return SchemaBuilder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    public function schema(): SchemaBuilder
    {
        return $this->connection->getSchemaBuilder();
    }

    public function getAllTables(): array
    {
        $tables = method_exists($this->schema(), 'getAllTables')
            ? $this->schema()->getAllTables()
            : $this->schema()->getTables();

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

    protected function columns(string $table): array
    {
        return $this->schema()->getColumnListing($table);
    }

    protected function filteredTables(array $tables, string $key): array
    {
        return array_filter($tables, static function (array|stdClass $table) use ($key) {
            $name = is_array($table) ? $table['name'] : $table->{$key};

            return $name !== 'migrations';
        });
    }

    protected function pluckTableNames(array $tables, string $key): array
    {
        return array_map(static function (array|stdClass $table) use ($key) {
            return is_array($table) ? $table['name'] : $table->{$key};
        }, $tables);
    }

    protected function database(): string
    {
        return $this->connection->getDatabaseName();
    }
}
