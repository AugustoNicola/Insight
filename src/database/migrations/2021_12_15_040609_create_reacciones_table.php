<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReaccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("reacciones", function (Blueprint $table) {
            $table->id("id");
            $table->unsignedBigInteger("publicacion_id");   # FK, UNSIGNED BIGINT
            $table->unsignedBigInteger("usuarie_id"); # FK, UNSIGNED BIGINT
            $table->enum("relacion", ["me_gusta", "guardar"]); # ENUM
            
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
        Schema::dropIfExists("reacciones");
    }
}
