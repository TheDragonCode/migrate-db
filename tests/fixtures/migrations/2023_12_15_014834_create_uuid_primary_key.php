<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\Migration;

class CreateUuidPrimaryKey extends Migration
{
    protected $table = 'uuid_table';

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            if (method_exists(Blueprint::class, 'uuid')) {
                $table->uuid('uuid')->primary();
            }

            $table->string('value');
        });
    }
}
