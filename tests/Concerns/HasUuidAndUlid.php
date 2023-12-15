<?php

declare(strict_types=1);

namespace Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

trait HasUuidAndUlid
{
    protected function hasUuid()
    {
        return method_exists(Blueprint::class, 'uuid')
            && method_exists(Str::class, 'uuid');
    }

    protected function hasUlid()
    {
        return method_exists(Blueprint::class, 'ulid')
            && method_exists(Str::class, 'ulid');
    }
}
