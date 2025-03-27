<?php

declare(strict_types=1);

namespace Tests\Concerns;

use Illuminate\Database\Migrations\Migration as BaseMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class Migration extends BaseMigration
{
    use HasUuidAndUlid;

    protected $table;

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->string('value');
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
