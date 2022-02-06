<?php

namespace DragonCode\MigrateDB\Console;

use DragonCode\Contracts\MigrateDB\Builder;
use DragonCode\MigrateDB\Exceptions\InvalidArgumentException;
use DragonCode\MigrateDB\Facades\BuilderManager;
use DragonCode\Support\Facades\Helpers\Arr;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Migrate extends Command
{
    protected $signature = 'db:migrate'
        . ' {--schema-from= : Source connection name}'
        . ' {--schema-to= : Target connection name}'
        . ' {--truncate-tables=false : Truncate table before transfer or append new records only}'
        . ' {--drop-tables=true : Drop Target Tables}'
        . ' {--get-all-tables-from-target=false : Get all tables from target connection if source connection do not support.}'
        . ' {--exclude-tables=- : Comma separated table names to exclude}'
        . ' {--tables=- : Comma separated table names to migrate only}';

    protected $description = 'Data transfer from one database to another';

    /** @var \DragonCode\Contracts\MigrateDB\Builder */
    protected $source;

    /** @var \DragonCode\Contracts\MigrateDB\Builder */
    protected $target;

    /** @var array */
    protected array $tableNames;

    /** @var array */
    protected array $excludeTables;

    /** @var bool */
    protected bool $getTablesFromTarget = false;

    /** @var bool */
    protected bool $dropTables = false;

    /** @var bool */
    protected bool $truncateTables = false;

    public function handle()
    {
        $this->validateOptions();
        $this->resolveBuilders();
        $this->resolveOptions();
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
            $this->info('');

            if (in_array($table, $this->excludeTables)) {
                $this->info($table . ' excluded');
                return;
            }

            $this->migrateTable($table, $this->source->getPrimaryKey($table));
        });
    }

    protected function migrateTable(string $table, string $column): void
    {

        $this->info('Transferring data from:' . $table);
        Log::info('Transferring data from:' . $table);

        if ($this->truncateTables)
            $this->builder($this->target(), $table)->truncate();

        $this->builder($this->source(), $table)
            ->when(
                !$this->truncateTables &&
                    $this->getPrimaryKeyType($this->source(), $table, $column) != 'string', // if primary key is integer than skipping existing records
                function ($query) use ($column, $table) {
                    Log::info('last record:' . ($lastRecord = $this->builder($this->target(), $table)->max($column) ?? 0));
                    return $query->where($column, '>', $lastRecord);
                }
            )
            ->orderBy($column)
            ->chunk(1000, function (Collection $items) use ($table) {
                $items = Arr::toArray($items);

                $this->builder($this->target(), $table)->insert($items);
            });
    }

    protected function tables(): array
    {
        return $this->tableNames[0] != '-'
            ? $this->tableNames
            : ($this->getTablesFromTarget
                ? $this->target->getAllTables()
                : $this->source->getAllTables()
            );
    }

    protected function cleanTargetDatabase(): void
    {
        if (!$this->dropTables)
            return;

        $this->info('Clearing the target database...');

        $this->target->dropAllTables();
    }

    protected function runMigrations(): void
    {
        $runMigrationOn = $this->choice(
            'Please choose option to run migration on which connection?',
            ['target', 'both', 'none'],
            0
        );

        if ($runMigrationOn == 'none')
            return;

        $this->info('Run migrations on the databases...');

        if ($runMigrationOn == 'both')
            $this->call('migrate', ['--database' => $this->source()]);

        if (in_array($runMigrationOn, ['target', 'both']))
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

    protected function tableNames(): array
    {
        return explode(',', $this->validatedOption('tables'));
    }

    protected function excludeTables(): array
    {
        return explode(',', $this->validatedOption('exclude-tables'));
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
        $this->validatedOption('tables');
        $this->validatedOption('exclude-tables');
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

    protected function resolveOptions(): void
    {
        $this->tableNames =   $this->tableNames();
        $this->excludeTables =   $this->excludeTables();

        if ($this->tableNames[0] == '-' && $this->confirm('Get all table list from target connection(incase if source connection does not support it)?', false))
            $this->getTablesFromTarget = true;

        if ($this->confirm('Please choose option whether to truncate target table before transfer?', false))
            $this->truncateTables = true;

        if (($this->tableNames[0] == '-'
            && $this->excludeTables[0] == '-'
            && $this->truncateTables
            && $this->confirm('Please choose option whether to drop target tables before migration?', false)))
            $this->dropTables = true;
    }

    protected function builder(string $connection, string $table): QueryBuilder
    {
        return DB::connection($connection)->table($table);
    }

    protected function getPrimaryKeyType(string $connection, string $table, string $column): string
    {
        return DB::connection($connection)->getDoctrineColumn($table, $column)->getType()->getName();
    }
}
