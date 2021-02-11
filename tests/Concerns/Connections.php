<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

trait Connections
{
    protected $source = 'foo_db';

    protected $target = 'bar_db';

    /**
     * @return \Illuminate\Database\Schema\Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    protected function sourceConnection(): Builder
    {
        return Schema::connection($this->source);
    }

    /**
     * @return \Illuminate\Database\Schema\Builder|\Illuminate\Database\Schema\MySqlBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    protected function targetConnection(): Builder
    {
        return Schema::connection($this->target);
    }
}
