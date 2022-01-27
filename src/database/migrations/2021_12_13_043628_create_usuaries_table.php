<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuaries', function (Blueprint $table) {
            $table->id("id");                          # PK, UNSIGNED BIGINT 
            $table->string("nombre", 100);             # VARCHAR(100)
            $table->string("contrasena", 191);         # VARCHAR(191)
            $table->string("imagen", 100)->nullable(); # VARCHAR(100)
            $table->timestamp("fecha_creacion")->nullable();       # TIMESTAMP
            $table->timestamp("fecha_actualizacion")->nullable();  # TIMESTAMP
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuaries');
    }
}
