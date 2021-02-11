<?php

namespace Helldar\MigrateDB\Console;

use Helldar\MigrateDB\Exceptions\InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        $this->disableForeign();
        $this->runTransfer();
        $this->enableForeign();
    }

    protected function runTransfer(): void
    {
        $this->info('Transferring data...');

        $this->withProgressBar($this->tables(), function (string $table) {
            $this->migrateTable($table);
        });
    }

    protected function migrateTable(string $table): void
    {
        DB::connection($this->source())->table($table)->chunk(1000, function ($items) use ($table) {
            DB::connection($this->target())->table($table)->insert($items);
        });
    }

    protected function tables(): array
    {
        return $this->sourceConnection()->getAllTables();
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
