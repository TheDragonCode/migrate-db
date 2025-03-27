<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\Migration;

class CreateUuidPrimaryKey extends Migration
{
    protected $table = 'uuid_table';

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            if ($this->hasUuid()) {
                $table->uuid('uuid')->primary();
            }

            $table->string('value');
        });
    }
}
