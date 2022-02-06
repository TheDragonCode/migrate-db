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
        . ' {--exclude-tables=* : Comma separated table names to exclude}'
        . ' {--tables=* : Comma separated table names to migrate only}';

    protected $description = 'Data transfer from one database to another';

    /** @var \DragonCode\Contracts\MigrateDB\Builder */
    protected $source;

    /** @var \DragonCode\Contracts\MigrateDB\Builder */
    protected $target;

    /** @var array */
    protected $table_names;

    /** @var array */
    protected $exclude_tables;

    /** @var bool */
    protected $retrive_tables_from_target;

    /** @var bool */
    protected $drop_target_tables;

    /** @var bool */
    protected $truncate_tables;

    /** @var string */
    protected static $target_connection = 'target';

    /** @var string */
    protected static $both_connection = 'both';

    /** @var string */
    protected static $none = 'none';

    /** @var array */
    protected static $choices = ['target', 'both', 'none'];

    /** @var array */
    protected $migrated_tables = [];

    /** @var array */
    protected $table_not_exists = [];

    /** @var array */
    protected $excluded_tables = [];

    /** @var string */
    protected static $separator = ',';

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

        $this->info('');

        $this->info('');
        $this->info('Migrated Tables');
        $this->info(implode(self::$separator, $this->migrated_tables));


        $this->info('');
        $this->info('Excluded Tables');
        $this->info(implode(self::$separator, $this->excluded_tables));

        $this->info('');
        $this->info('Tables Does not Exists in source connection');
        $this->info(implode(self::$separator, $this->table_not_exists));
    }

    protected function runTransfer(): void
    {
        $this->info('Transferring data...');

        $this->withProgressBar($this->tables(), function (string $table) {

            if (in_array($table, $this->exclude_tables)) {
                $this->excluded_tables[] = $table;
                return;
            }

            if (!$this->hasTable($this->source(), $table)) {
                $this->table_not_exists[] = $table;
                return;
            }

            $this->migrateTable($table, $this->source->getPrimaryKey($table));
            $this->migrated_tables[] = $table;
        });
    }

    protected function migrateTable(string $table, string $column): void
    {
        Log::info('Transferring data from:' . $table);

        if ($this->truncate_tables) {
            $this->builder($this->target(), $table)->truncate();
        }

        $this->builder($this->source(), $table)
            ->when(
                !$this->truncate_tables
                    && $this->isNumericColumn($table, $column),
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

    /**  if primary key is not string then skipping existing records */
    protected function isNumericColumn($table, $column)
    {
        return $this->getPrimaryKeyType($this->source(), $table, $column) !== 'string';
    }

    protected function tables(): array
    {
        if (!empty($this->table_names)) {
            return $this->table_names;
        }

        return  $this->retrive_tables_from_target
            ? $this->target->getAllTables()
            : $this->source->getAllTables();
    }

    protected function cleanTargetDatabase(): void
    {
        if (!$this->drop_target_tables) {
            return;
        }

        $this->info('Clearing the target database...');

        $this->target->dropAllTables();
    }

    protected function runMigrations(): void
    {
        $runMigrationOn = $this->getMigrationOption();

        if ($runMigrationOn === self::$none) {
            return;
        }

        $this->info('Run migrations on the databases...');

        if ($runMigrationOn === self::$both_connection) {
            $this->call('migrate', ['--database' => $this->source()]);
        }

        if ($this->drop_target_tables === true || in_array($runMigrationOn, [self::$target_connection, self::$both_connection])) {
            $this->call('migrate', ['--database' => $this->target()]);
        }
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
        return $this->option('tables');
    }

    protected function excludeTables(): array
    {
        return $this->option('exclude-tables');
    }

    protected function getMigrationOption()
    {
        return $this->choice('Please choose option to run migration on which connection?', self::$choices, 0);
    }

    protected function resolveTableListOption()
    {
        return $this->confirm('Retrive all table list from target connection? (incase if source connection does not support it)', false);
    }

    protected function resolveTruncateTableOption()
    {
        return $this->confirm('Please choose option whether to truncate target table before transfer?', false);
    }

    protected function resolveDropOption()
    {
        return $this->confirm('Please choose option whether to drop target tables before migration?', false);
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
        $this->table_names                  = $this->tableNames();
        $this->exclude_tables               = $this->excludeTables();
        $this->retrive_tables_from_target   = false;
        $this->truncate_tables              = false;
        $this->drop_target_tables           = false;

        if (empty($this->table_names) && $this->resolveTableListOption()) {
            $this->retrive_tables_from_target = true;
        }

        if ($this->resolveTruncateTableOption()) {
            $this->truncate_tables = true;
        }

        if ((empty($this->table_names) && empty($this->exclude_tables) && $this->truncate_tables && $this->resolveDropOption())) {
            $this->drop_target_tables = true;
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
