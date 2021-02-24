<?php

namespace Tests\Unit;

use Helldar\MigrateDB\Exceptions\InvalidArgumentException;
use Helldar\Support\Facades\Helpers\Arr;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class MysqlToMysqlTest extends TestCase
{
    public function testFillable()
    {
        $this->assertNotEmpty($this->sourceConnection()->getAllTables());
        $this->assertEmpty($this->targetConnection()->getAllTables());

        $this->artisan('db:migrate', [
            '--schema-from' => $this->currentSourceConnection(),
            '--schema-to'   => $this->currentTargetConnection(),
        ])->assertExitCode(0)->run();

        $this->assertNotEmpty($this->sourceConnection()->getAllTables());
        $this->assertNotEmpty($this->targetConnection()->getAllTables());
    }

    public function testCount()
    {
        $this->assertDatabaseCount($this->table_foo, 3, $this->currentSourceConnection());
        $this->assertDatabaseCount($this->table_bar, 3, $this->currentSourceConnection());
        $this->assertDatabaseCount($this->table_baz, 3, $this->currentSourceConnection());

        $this->artisan('db:migrate', [
            '--schema-from' => $this->currentSourceConnection(),
            '--schema-to'   => $this->currentTargetConnection(),
        ])->assertExitCode(0)->run();

        $this->assertDatabaseCount($this->table_foo, 3, $this->currentSourceConnection());
        $this->assertDatabaseCount($this->table_bar, 3, $this->currentSourceConnection());
        $this->assertDatabaseCount($this->table_baz, 3, $this->currentSourceConnection());

        $this->assertDatabaseCount($this->table_foo, 3, $this->currentTargetConnection());
        $this->assertDatabaseCount($this->table_bar, 3, $this->currentTargetConnection());
        $this->assertDatabaseCount($this->table_baz, 3, $this->currentTargetConnection());
    }

    public function testData()
    {
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_1'], $this->currentSourceConnection());
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_2'], $this->currentSourceConnection());
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_3'], $this->currentSourceConnection());

        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_1'], $this->currentSourceConnection());
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_2'], $this->currentSourceConnection());
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_3'], $this->currentSourceConnection());

        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_1'], $this->currentSourceConnection());
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_2'], $this->currentSourceConnection());
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_3'], $this->currentSourceConnection());

        $this->artisan('db:migrate', [
            '--schema-from' => $this->currentSourceConnection(),
            '--schema-to'   => $this->currentTargetConnection(),
        ])->assertExitCode(0)->run();

        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_1'], $this->currentTargetConnection());
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_2'], $this->currentTargetConnection());
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_3'], $this->currentTargetConnection());

        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_1'], $this->currentTargetConnection());
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_2'], $this->currentTargetConnection());
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_3'], $this->currentTargetConnection());

        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_1'], $this->currentTargetConnection());
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_2'], $this->currentTargetConnection());
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_3'], $this->currentTargetConnection());
    }

    public function testSame()
    {
        $this->artisan('db:migrate', [
            '--schema-from' => $this->currentSourceConnection(),
            '--schema-to'   => $this->currentTargetConnection(),
        ])->assertExitCode(0)->run();

        $this->assertSame(
            $this->tableData($this->currentSourceConnection(), $this->table_foo),
            $this->tableData($this->currentTargetConnection(), $this->table_foo)
        );

        $this->assertSame(
            $this->tableData($this->currentSourceConnection(), $this->table_bar),
            $this->tableData($this->currentTargetConnection(), $this->table_bar)
        );

        $this->assertSame(
            $this->tableData($this->currentSourceConnection(), $this->table_baz),
            $this->tableData($this->currentTargetConnection(), $this->table_baz)
        );
    }

    public function testFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "schema-from" option does not exist.');

        $this->artisan('db:migrate')->run();
    }

    public function testFromFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "schema-from" option does not exist.');

        $this->artisan('db:migrate', ['--schema-to' => $this->currentTargetConnection()])->run();
    }

    public function testToFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "schema-to" option does not exist.');

        $this->artisan('db:migrate', ['--schema-from' => $this->currentSourceConnection()])->run();
    }

    public function testFailedFromConnectionName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported driver [qwerty].');

        $this->artisan('db:migrate', ['--schema-from' => 'qwerty', '--schema-to' => $this->currentTargetConnection()])->run();
    }

    public function testFailedToConnectionName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported driver [qwerty].');

        $this->artisan('db:migrate', ['--schema-from' => $this->currentSourceConnection(), '--schema-to' => 'qwerty'])->run();
    }

    protected function tableData(string $connection, string $table): array
    {
        $items = DB::connection($connection)->table($table)->get();

        return Arr::toArray($items);
    }

    protected function currentSourceConnection(): string
    {
        return 'mysql_foo';
    }

    protected function currentTargetConnection(): string
    {
        return 'mysql_bar';
    }

    protected function defaultSourceConnectionName(): string
    {
        return 'mysql';
    }

    protected function defaultTargetConnectionName(): string
    {
        return 'mysql';
    }
}
