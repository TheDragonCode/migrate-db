<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\Migration;

class CreateUlidPrimaryKey extends Migration
{
    protected $table = 'ulid_table';

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            if ($this->hasUlid()) {
                $table->ulid('ulid')->primary();
            }

            $table->string('value');
        });
    }
}
