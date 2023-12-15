<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\Migration;

class CreateUlidPrimaryKey extends Migration
{
    protected $table = 'ulid_key';

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->ulid()->primary();

            $table->string('value');
        });
    }
}
