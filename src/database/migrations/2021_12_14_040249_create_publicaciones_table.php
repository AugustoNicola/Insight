<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id("id");                           # PK, UNSIGNED BIGINT
            $table->string("titulo", 180);              # VARCHAR(180)
            $table->string("portada", 100)->nullable(); # VARCHAR(100)
            $table->longText("cuerpo");                 # LONGTEXT
            $table->unsignedBigInteger("usuarie_id");   # FK, UNSIGNED BIGINT
            $table->timestamp("fecha_creacion")->nullable();        # TIMESTAMP
            $table->timestamp("fecha_actualizacion")->nullable();   # TIMESTAMP

            $table->foreign("usuarie_id")->references("id")->on("usuaries");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publicaciones');
    }
}
