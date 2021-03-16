<?php

namespace Helldar\MigrateDB\Console;

use Helldar\MigrateDB\Contracts\Database\Builder;
use Helldar\MigrateDB\Exceptions\InvalidArgumentException;
use Helldar\MigrateDB\Facades\BuilderManager;
use Helldar\Support\Facades\Helpers\Arr;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class Migrate extends Command
{
    protected $signature = 'db:migrate'
    . ' {--schema-from= : Source connection name}'
    . ' {--schema-to= : Target connection name}';

    protected $description = 'Data transfer from one database to another';

    /** @var \Helldar\MigrateDB\Contracts\Database\Builder */
    protected $source;

    /** @var \Helldar\MigrateDB\Contracts\Database\Builder */
    protected $target;

    public function handle()
    {
        $this->validateOptions();
        $this->resolveBuilders();
        $this->cleanTargetDatabase();
        $this->runMigrations();

        $this->disableForeign();
        $this->runTransfer();
        $this->enableForeign();
    }

    protected function runTransfer(): void
    {
        $this->info('Transferring data...');

        $this->withProgressBar($this->tables(), function (string $table) {
            $this->migrateTable($table, $this->source->getPrimaryKey($table));
        });
    }

    protected function migrateTable(string $table, string $column): void
    {
        $this->builder($this->source(), $table)
            ->orderBy($column)
            ->chunk(1000, function (Collection $items) use ($table) {
                $items = Arr::toArray($items);

                $this->builder($this->target(), $table)->insert($items);
            });
    }

    protected function tables(): array
    {
        return $this->source->getAllTables();
    }

    protected function cleanTargetDatabase(): void
    {
        $this->info('Clearing the target database...');

        $this->target->dropAllTables();
    }

    protected function runMigrations(): void
    {
        $this->info('Run migrations on the databases...');

        $this->call('migrate', ['--database' => $this->source()]);
        $this->call('migrate', ['--database' => $this->target()]);
    }

    protected function disableForeign(): void
    {
        $this->target->disableForeign();
    }

    protected function enableForeign(): void
    {
        $this->target->enableForeign();
    }

    protected function source(): string
    {
        return $this->validatedOption('schema-from');
    }

    protected function target(): string
    {
        return $this->validatedOption('schema-to');
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

    protected function resolveBuilder(string $connection): Builder
    {
        return BuilderManager::of($connection)->get();
    }

    protected function resolveBuilders(): void
    {
        $this->source = $this->resolveBuilder($this->source());
        $this->target = $this->resolveBuilder($this->target());
    }

    protected function builder(string $connection, string $table): QueryBuilder
    {
        return DB::connection($connection)->table($table);
    }
}
