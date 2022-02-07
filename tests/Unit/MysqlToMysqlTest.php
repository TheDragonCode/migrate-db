<?php

namespace Tests\Unit;

use DragonCode\MigrateDB\Constants\Drivers;
use DragonCode\MigrateDB\Exceptions\InvalidArgumentException;
use DragonCode\Support\Facades\Helpers\Arr;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MysqlToMysqlTest extends TestCase
{
    public function testFillable()
    {
        $this->assertNotEmpty($this->sourceConnection()->getAllTables());
        $this->assertEmpty($this->targetConnection()->getAllTables());

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source_connection,
            '--schema-to'   => $this->target_connection,
        ])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'yes')
            ->expectsChoice('Please choose option to run migration on which connection?', 'both')
            ->assertExitCode(0)
            ->run();

        $this->assertNotEmpty($this->sourceConnection()->getAllTables());
        $this->assertNotEmpty($this->targetConnection()->getAllTables());
    }

    public function testCount()
    {
        $this->assertDatabaseCount($this->table_foo, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_bar, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_baz, 3, $this->source_connection);

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source_connection,
            '--schema-to'   => $this->target_connection,
        ])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'yes')
            ->expectsChoice('Please choose option to run migration on which connection?', 'both')
            ->assertExitCode(0)
            ->run();

        $this->assertDatabaseCount($this->table_foo, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_bar, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_baz, 3, $this->source_connection);

        $this->assertDatabaseCount($this->table_foo, 3, $this->target_connection);
        $this->assertDatabaseCount($this->table_bar, 3, $this->target_connection);
        $this->assertDatabaseCount($this->table_baz, 3, $this->target_connection);
    }

    public function testTablesOption()
    {
        $this->assertDatabaseCount($this->table_foo, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_bar, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_baz, 3, $this->source_connection);

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source_connection,
            '--schema-to'   => $this->target_connection,
            '--tables'      => [$this->table_foo, $this->table_bar],
        ])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'no')
            ->expectsChoice('Please choose option to run migration on which connection?', 'target')
            ->assertExitCode(0)
            ->run();

        $this->assertDatabaseCount($this->table_foo, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_bar, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_baz, 3, $this->source_connection);

        $this->assertDatabaseCount($this->table_foo, 3, $this->target_connection);
        $this->assertDatabaseCount($this->table_bar, 3, $this->target_connection);
        $this->assertDatabaseCount($this->table_baz, 0, $this->target_connection);
    }

    public function testTruncateTablesOption()
    {
        $this->assertDatabaseCount($this->table_foo, 3, $this->source_connection);

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source_connection,
            '--schema-to'   => $this->target_connection,
            '--tables'      => $this->table_foo,
        ])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'yes')
            ->expectsChoice('Please choose option to run migration on which connection?', 'target')
            ->assertExitCode(0)
            ->run();

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source_connection,
            '--schema-to'   => $this->target_connection,
            '--tables'      => $this->table_foo,
        ])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'yes')
            ->expectsChoice('Please choose option to run migration on which connection?', 'target')
            ->assertExitCode(0)
            ->run();

        $this->assertDatabaseCount($this->table_foo, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_foo, 3, $this->target_connection);
    }

    public function testDoNotTruncateTablesOption()
    {
        $this->assertDatabaseCount($this->table_foo, 3, $this->source_connection);

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source_connection,
            '--schema-to'   => $this->target_connection,
            '--tables'      => $this->table_foo,
        ])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'yes')
            ->expectsChoice('Please choose option to run migration on which connection?', 'target')
            ->assertExitCode(0)
            ->run();

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source_connection,
            '--schema-to'   => $this->target_connection,
            '--tables'      => $this->table_foo,
        ])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'no')
            ->expectsChoice('Please choose option to run migration on which connection?', 'target')
            ->assertExitCode(0)
            ->run();

        $this->assertDatabaseCount($this->table_foo, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_foo, 6, $this->target_connection);
    }

    public function testExcludeTablesOption()
    {
        $this->assertDatabaseCount($this->table_foo, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_bar, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_baz, 3, $this->source_connection);

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source_connection,
            '--schema-to'   => $this->target_connection,
            '--exclude'      => [$this->table_foo, $this->table_bar],
        ])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'no')
            ->expectsChoice('Please choose option to run migration on which connection?', 'target')
            ->assertExitCode(0)
            ->run();

        $this->assertDatabaseCount($this->table_foo, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_bar, 3, $this->source_connection);
        $this->assertDatabaseCount($this->table_baz, 3, $this->source_connection);

        $this->assertDatabaseCount($this->table_foo, 0, $this->target_connection);
        $this->assertDatabaseCount($this->table_bar, 0, $this->target_connection);
        $this->assertDatabaseCount($this->table_baz, 1, $this->target_connection);
    }

    public function testData()
    {
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_1'], $this->source_connection);
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_2'], $this->source_connection);
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_3'], $this->source_connection);

        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_1'], $this->source_connection);
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_2'], $this->source_connection);
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_3'], $this->source_connection);

        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_1'], $this->source_connection);
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_2'], $this->source_connection);
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_3'], $this->source_connection);

        $this->artisan('db:migrate', [
            '--schema-from' => $this->source_connection,
            '--schema-to'   => $this->target_connection,
        ])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'yes')
            ->expectsChoice('Please choose option to run migration on which connection?', 'both')
            ->assertExitCode(0)
            ->run();

        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_1'], $this->target_connection);
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_2'], $this->target_connection);
        $this->assertDatabaseHas($this->table_foo, ['value' => 'foo_3'], $this->target_connection);

        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_1'], $this->target_connection);
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_2'], $this->target_connection);
        $this->assertDatabaseHas($this->table_bar, ['value' => 'bar_3'], $this->target_connection);

        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_1'], $this->target_connection);
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_2'], $this->target_connection);
        $this->assertDatabaseHas($this->table_baz, ['value' => 'baz_3'], $this->target_connection);
    }

    public function testSame()
    {
        $this->artisan('db:migrate', [
            '--schema-from' => $this->source_connection,
            '--schema-to'   => $this->target_connection,
        ])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'yes')
            ->expectsChoice('Please choose option to run migration on which connection?', 'both')
            ->assertExitCode(0)
            ->run();

        $this->assertSame(
            $this->tableData($this->source_connection, $this->table_foo),
            $this->tableData($this->target_connection, $this->table_foo)
        );

        $this->assertSame(
            $this->tableData($this->source_connection, $this->table_bar),
            $this->tableData($this->target_connection, $this->table_bar)
        );

        $this->assertSame(
            $this->tableData($this->source_connection, $this->table_baz),
            $this->tableData($this->target_connection, $this->table_baz)
        );
    }

    public function testFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "schema-from" option does not exist.');

        $this->artisan('db:migrate')
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'yes')
            ->expectsChoice('Please choose option to run migration on which connection?', 'both')
            ->run();
    }

    public function testFromFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "schema-from" option does not exist.');

        $this->artisan('db:migrate', ['--schema-to' => $this->target_connection])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'yes')
            ->expectsChoice('Please choose option to run migration on which connection?', 'both')
            ->run();
    }

    public function testToFailed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "schema-to" option does not exist.');

        $this->artisan('db:migrate', ['--schema-from' => $this->source_connection])->run();
    }

    public function testFailedFromConnectionName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported driver [qwerty].');

        $this->artisan('db:migrate', ['--schema-from' => 'qwerty', '--schema-to' => $this->target_connection])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'yes')
            ->expectsChoice('Please choose option to run migration on which connection?', 'both')
            ->run();
    }

    public function testFailedToConnectionName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported driver [qwerty].');

        $this->artisan('db:migrate', ['--schema-from' => $this->source_connection, '--schema-to' => 'qwerty'])
            ->expectsConfirmation('Retrive all table list from target connection? (incase if source connection does not support it)', 'no')
            ->expectsConfirmation('Please choose option whether to truncate target table before transfer?', 'yes')
            ->expectsChoice('Please choose option to run migration on which connection?', 'both')
            ->run();
    }

    protected function tableData(string $connection, string $table): array
    {
        $items = DB::connection($connection)->table($table)->get();

        return Arr::toArray($items);
    }

    protected function defaultSourceConnectionName(): string
    {
        return Drivers::MYSQL;
    }

    protected function defaultTargetConnectionName(): string
    {
        return Drivers::MYSQL;
    }
}
