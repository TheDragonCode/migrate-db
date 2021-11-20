<?php

namespace DragonCode\MigrateDB\Facades;

use DragonCode\Contracts\MigrateDB\Builder;
use DragonCode\MigrateDB\Database\Manager as Support;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Support of(string $connection)
 * @method static Builder resolve()
 */
class BuilderManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Support::class;
    }
}
