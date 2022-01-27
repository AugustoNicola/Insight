<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id("id");                             # PK, UNSIGNED BIGINT
            $table->longText("cuerpo");                   # LONGTEXT
            $table->unsignedBigInteger("publicacion_id"); # FK, UNSIGNED BIGINT
            $table->unsignedBigInteger("usuarie_id");     # FK, UNSIGNED BIGINT
            $table->timestamp("fecha_creacion")->nullable();          # TIMESTAMP

            $table->foreign("publicacion_id")->references("id")->on("publicaciones");
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
        Schema::dropIfExists('comentarios');
    }
}
