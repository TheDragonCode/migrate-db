<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\Migration;

class CreateUuidPrimaryKey extends Migration
{
    protected $table = 'uuid_key';

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid()->primary();

            $table->string('value');
        });
    }
}
