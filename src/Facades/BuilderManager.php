<?php

namespace Helldar\MigrateDB\Facades;

use Helldar\MigrateDB\Contracts\Database\Builder;
use Helldar\MigrateDB\Database\Manager as Support;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Support of(string $connection)
 * @method static Builder resolve()
 */
final class BuilderManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Support::class;
    }
}
