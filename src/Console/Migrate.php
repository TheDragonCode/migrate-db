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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class Migrate extends Command
{
    protected $signature = 'db:migrate'
        . ' {--schema-from= : Source connection name}'
        . ' {--schema-to= : Target connection name}'
        . ' {--exclude=* : Comma separated table names to exclude}'
        . ' {--tables=* : Comma separated table names to migrate only}';

    protected $description = 'Data transfer from one database to another';

    /** @var \DragonCode\Contracts\MigrateDB\Builder */
    protected $source;

    /** @var \DragonCode\Contracts\MigrateDB\Builder */
    protected $target;

    /** @var array */
    protected $tables;

    /** @var array */
    protected $excludes;

    /** @var bool */
    protected $retrive_tables_from_target = false;

    /** @var bool */
    protected $drop_target = false;

    /** @var bool */
    protected $truncate = false;

    /** @var string */
    protected  $target_connection = 'target';

    /** @var string */
    protected  $both_connection = 'both';

    /** @var string */
    protected  $none = 'none';

    /** @var array */
    protected  $choices = ['target', 'both', 'none'];

    /** @var array */
    protected $migrated = [];

    /** @var array */
    protected $tables_not_exists = [];

    /** @var array */
    protected $excluded = [];

    /** @var string */
    protected  $separator = ',';

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

        $this->displayMessage('Migrated Tables                            : ' . implode($this->separator, $this->migrated));
        $this->displayMessage('Excluded Tables                            : ' . implode($this->separator, $this->excluded));
        $this->displayMessage('Tables does not exist in source connection : ' . implode($this->separator, $this->tables_not_exists));
    }

    protected function displayMessage($message): void
    {
        $this->info($message);
    }

    protected function runTransfer(): void
    {
        $this->displayMessage('Transferring data...' . PHP_EOL);

        $this->withProgressBar($this->tables(), function (string $table) {
            if (in_array($table, $this->excludes)) {
                $this->excluded[] = $table;
                return;
            }

            if (!$this->hasTable($this->source(), $table)) {
                $this->tables_not_exists[] = $table;
                return;
            }

            $this->truncateTable($table);
            $this->migrateTable($table, $this->source->getPrimaryKey($table));
        });
        $this->displayMessage(PHP_EOL);
    }

    protected function truncateTable(string $table): void
    {
        if ($this->truncate) {
            $this->builder($this->target(), $table)->truncate();
        }
    }

    protected function migrateTable(string $table, string $column): void
    {
        Log::info('Transferring data from: ' . $table);

        $this->builder($this->source(), $table)
            ->when(
                $this->isSkippable($table, $column),
                function ($query) use ($table, $column) {
                    Log::info('last record: ' . ($lastRecord = $this->builder($this->target(), $table)->max($column) ?? 0));
                    return $query->where($column, '>', $lastRecord);
                }
            )
            ->orderBy($column)
            ->chunk(1000, function (Collection $items) use ($table) {
                $items = Arr::toArray($items);

                $this->builder($this->target(), $table)->insert($items);
            });

        $this->migrated[] = $table;
    }

    protected function isSkippable(string $table, string $column): bool
    {
        return !$this->truncate && $this->isNumericColumn($table, $column);
    }

    /**  if primary key is not string then skipping existing records */
    protected function isNumericColumn($table, $column): bool
    {
        return $this->getPrimaryKeyType($this->source(), $table, $column) !== 'string';
    }

    protected function tables(): array
    {
        if (!empty($this->tables)) {
            return $this->tables;
        }

        return  $this->retrive_tables_from_target
            ? $this->target->getAllTables()
            : $this->source->getAllTables();
    }

    protected function cleanTargetDatabase(): void
    {
        if (!$this->drop_target) {
            return;
        }

        $this->displayMessage('Clearing the target database...');

        $this->target->dropAllTables();
    }

    protected function runMigrations(): void
    {
        $run_migration_on = $this->getMigrationOption();

        if (!$this->isMigrationRequired($run_migration_on)) {
            return;
        }

        $this->displayMessage('Run migrations on the databases...');

        if ($this->shouldRunOnSource($run_migration_on)) {
            $this->migrate($this->source());
        }

        if ($this->shouldRunOnTarget($run_migration_on)) {
            $this->migrate($this->target());
        }
    }

    protected function isMigrationRequired($run_migration_on): bool
    {
        return $run_migration_on === $this->none;
    }

    protected function shouldRunOnTarget($run_migration_on): bool
    {
        return $this->drop_target === true || in_array($run_migration_on, [$this->target_connection, $this->both_connection]);
    }

    protected function shouldRunOnSource($run_migration_on): bool
    {
        return $run_migration_on === $this->both_connection;
    }

    protected function migrate($connection): void
    {
        $this->call('migrate', ['--database' => $connection]);
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

    protected function getTablesOption(): array
    {
        return $this->option('tables');
    }

    protected function getExcludeOption(): array
    {
        return $this->option('exclude');
    }

    protected function getMigrationOption(): string
    {
        return $this->choice('Please choose option to run migration on which connection?', $this->choices, 0);
    }

    protected function confirmTableListOption(): bool
    {
        return $this->confirm('Please confirm table list should be retrived from target connection? (incase if source connection does not support it)', false);
    }

    protected function confirmTruncateTableOption(): bool
    {
        return $this->confirm('Please confirm whether to truncate target table before transfer?', false);
    }

    protected function confirmDropOption(): bool
    {
        return $this->confirm('Please choose whether to drop target tables before migration?', false);
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

    protected function resolveOptions(): void
    {
        $this->tables                       = $this->getTablesOption();
        $this->excludes                     = $this->getExcludeOption();

        if (empty($this->tables) && $this->confirmTableListOption()) {
            $this->retrive_tables_from_target = true;
        }

        if ($this->confirmTruncateTableOption()) {
            $this->truncate = true;
        }

        if ((empty($this->tables) && empty($this->excludes) && $this->truncate && $this->confirmDropOption())) {
            $this->drop_target = true;
        }
    }

    protected function builder(string $connection, string $table): QueryBuilder
    {
        return DB::connection($connection)->table($table);
    }

    protected function hasTable(string $connection, string $table): bool
    {
        return Schema::connection($connection)->hasTable($table);
    }

    protected function getPrimaryKeyType(string $connection, string $table, string $column): string
    {
        return DB::connection($connection)->getDoctrineColumn($table, $column)->getType()->getName();
    }
}
