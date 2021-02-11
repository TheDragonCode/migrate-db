<?php

namespace Tests\Unit;

use Helldar\MigrateDB\Exceptions\InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException as BaseInvalidArgumentException;
use Tests\TestCase;

final class MigrateTest extends TestCase
{
    public function testEmpty()
    {
        $this->assertNotEmpty($this->sourceConnection()->getAllTables());
        $this->assertEmpty($this->targetConnection()->getAllTables());

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source,
            '--schema-to'   => $this->target,
        ])->assertExitCode(0)->run();

        $this->assertNotEmpty($this->sourceConnection()->getAllTables());
        $this->assertNotEmpty($this->targetConnection()->getAllTables());
    }

    public function testCount()
    {
        $this->assertDatabaseCount($this->table_foo, 3, $this->source);
        $this->assertDatabaseCount($this->table_bar, 3, $this->source);
        $this->assertDatabaseCount($this->table_baz, 3, $this->source);

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source,
            '--schema-to'   => $this->target,
        ])->assertExitCode(0)->run();

        $this->assertDatabaseCount($this->table_foo, 3, $this->source);
        $this->assertDatabaseCount($this->table_bar, 3, $this->source);
        $this->assertDatabaseCount($this->table_baz, 3, $this->source);

        $this->assertDatabaseCount($this->table_foo, 3, $this->target);
        $this->assertDatabaseCount($this->table_bar, 3, $this->target);
        $this->assertDatabaseCount($this->table_baz, 3, $this->target);
    }

    public function testData()
    {
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_q1'], $this->source);
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_q2'], $this->source);
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_q3'], $this->source);

        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_q1'], $this->source);
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_q2'], $this->source);
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_q3'], $this->source);

        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_q1'], $this->source);
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_q2'], $this->source);
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_q3'], $this->source);

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source,
            '--schema-to'   => $this->target,
        ])->assertExitCode(0)->run();

        $this->assertSame(
            $this->tableData($this->source, $this->table_foo),
            $this->tableData($this->target, $this->table_foo)
        );

        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_q1'], $this->target);
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_q2'], $this->target);
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_q3'], $this->target);

        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_q1'], $this->target);
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_q2'], $this->target);
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_q3'], $this->target);

        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_q1'], $this->target);
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_q2'], $this->target);
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_q3'], $this->target);
    }

    public function testSame()
    {
        $this->artisan('db:migrate', [
            '--schema-from' => $this->source,
            '--schema-to'   => $this->target,
        ])->assertExitCode(0)->run();

        $this->assertSame(
            $this->tableData($this->source, $this->table_foo),
            $this->tableData($this->target, $this->table_foo)
        );

        $this->assertSame(
            $this->tableData($this->source, $this->table_bar),
            $this->tableData($this->target, $this->table_bar)
        );

        $this->assertSame(
            $this->tableData($this->source, $this->table_baz),
            $this->tableData($this->target, $this->table_baz)
        );
    }

    public function testFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "schema-from" option does not exist.');

        $this->artisan('db:migrate');
    }

    public function testFromFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "schema-from" option does not exist.');

        $this->artisan('db:migrate', ['--schema-to' => $this->target]);
    }

    public function testToFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "schema-to" option does not exist.');

        $this->artisan('db:migrate', ['--schema-from' => $this->source]);
    }

    public function testFailedFromConnectionName()
    {
        $this->expectException(BaseInvalidArgumentException::class);
        $this->expectExceptionMessage('Database connection [qwerty] not configured.');

        $this->artisan('db:migrate', ['--schema-from' => 'qwerty', '--schema-to' => $this->target])->run();
    }

    public function testFailedToConnectionName()
    {
        $this->expectException(BaseInvalidArgumentException::class);
        $this->expectExceptionMessage('Database connection [qwerty] not configured.');

        $this->artisan('db:migrate', ['--schema-from' => $this->source, '--schema-to' => 'qwerty'])->run();
    }

    protected function tableData(string $connection, string $table): array
    {
        return DB::connection($connection)->table($table)->get()->toArray();
    }
}
