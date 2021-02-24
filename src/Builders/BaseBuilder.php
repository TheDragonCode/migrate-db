<?php

namespace Helldar\MigrateDB\Builders;

use Helldar\MigrateDB\Contracts\Database\Builder;
use Helldar\Support\Concerns\Makeable;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use stdClass;

abstract class BaseBuilder implements Builder
{
    use Makeable;

    protected $connection;

    public function __construct(string $connection)
    {
        $this->connection = $connection;
    }

    public function schema(): SchemaBuilder
    {
        return Schema::connection($this->connection);
    }

    public function getAllTables(): array
    {
        /** @var array $tables */
        $tables = $this->schema()->getAllTables();

        $key = $this->transferKey();

        return $this->pluckTables($this->filteredTables($tables, $key), $key);
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

    protected function getDatabaseName(): string
    {
        return Config::get('database.connections.' . $this->connection . '.database');
    }

    protected function transferKey(): string
    {
        return 'Tables_in_' . $this->getDatabaseName();
    }

    protected function filteredTables(array $tables, string $key): array
    {
        return array_filter($tables, static function (stdClass $table) use ($key) {
            return $table->{$key} !== 'migrations';
        });
    }

    protected function pluckTables(array $tables, string $key): array
    {
        return array_map(static function (stdClass $table) use ($key) {
            return $table->{$key};
        }, $tables);
    }
}
