<?php

namespace Tests\Concerns;

use Illuminate\Database\Migrations\Migration as BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

abstract class Migration extends BaseMigration
{
    protected $connection = 'foo_db';

    protected $table;

    public function up()
    {
        $this->connection()->create($this->table, function (Blueprint $table) {
            $table->string('value');
        });
    }

    public function down()
    {
        $this->connection()->dropIfExists($this->table);
    }

    protected function connection(): Builder
    {
        return Schema::connection($this->connection);
    }
}
