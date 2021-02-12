<?php

namespace Helldar\MigrateDB\Console;

use Helldar\MigrateDB\Exceptions\InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use stdClass;

final class Migrate extends Command
{
    protected $signature = 'db:migrate'
    . ' {--schema-from : Source connection name}'
    . ' {--schema-to : Target connection name}';

    protected $description = 'Data transfer from one database to another';

    public function handle()
    {
        $this->validateOptions();
        $this->cleanTargetDatabase();
        $this->runMigrations();

        // $this->disableForeign();
        // $this->runTransfer();
        // $this->enableForeign();
    }

    protected function runTransfer(): void
    {
        $this->info('Transferring data...');

        $key = $this->transferKey($this->source());

        $this->withProgressBar($this->tables(), function (stdClass $table) use ($key) {
            $name = $table->{$key};

            $this->migrateTable($name, $this->primaryKey($name));
        });
    }

    protected function migrateTable(string $table, string $column): void
    {
        DB::connection($this->source())
            ->table($table)
            ->orderBy($column)
            ->chunk(1000, function (Collection $items) use ($table) {
                DB::connection($this->target())->table($table)->insert($items->toArray());
            });
    }

    protected function tables(): array
    {
        $tables = $this->sourceConnection()->getAllTables();

        $key = $this->transferKey($this->source());

        return array_filter($tables, static function ($table) use ($key) {
            return $table->{$key} !== 'migrations';
        });
    }

    protected function cleanTargetDatabase(): void
    {
        $this->info('Clearing the target database...');

        $this->targetConnection()->dropAllTables();
    }

    protected function runMigrations(): void
    {
        $this->info('Run migrations on the target database...');

        $this->call('migrate', ['--database' => $this->target()]);
    }

    protected function disableForeign(): void
    {
        $this->targetConnection()->disableForeignKeyConstraints();
    }

    protected function enableForeign(): void
    {
        $this->targetConnection()->enableForeignKeyConstraints();
    }

    /**
     * @return \Illuminate\Database\Schema\Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    protected function sourceConnection(): Builder
    {
        return Schema::connection($this->source());
    }

    protected function targetConnection(): Builder
    {
        return Schema::connection($this->target());
    }

    protected function source(): string
    {
        return $this->validatedOption('schema-from');
    }

    protected function target(): string
    {
        return $this->validatedOption('schema-to');
    }

    protected function transferKey(string $database): string
    {
        return 'Tables_in_' . $database;
    }

    protected function primaryKey(string $table)
    {
        $primary = DB::select(DB::raw("SHOW KEYS FROM `{$table}` WHERE Key_name = 'PRIMARY'"));

        if (! empty($primary)) {
            return $primary->Column_name;
        }

        $columns = DB::select(DB::raw("SHOW COLUMNS FROM `{$table}`"));

        $column = Arr::first($columns);

        return $column->Field;
    }

    protected function validatedOption(string $key): string
    {
        if ($schema = $this->option($key)) {
            return $schema;
        }

        throw new InvalidArgumentException($key);
    }

    protected function validateOptions(): void
    {
        $this->validatedOption('schema-from');
        $this->validatedOption('schema-to');
    }
}
