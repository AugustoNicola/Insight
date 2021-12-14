<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriaPublicacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categoria_publicacion', function (Blueprint $table) {
            $table->unsignedBigInteger("categoria_id");   # CK, FK, UNSIGNED BIGINT
            $table->unsignedBigInteger("publicacion_id"); # CK, FK, UNSIGNED BIGINT
            
            $table->foreign("categoria_id")->references("id")->on("categorias");
            $table->foreign("publicacion_id")->references("id")->on("publicaciones");  
            $table->index(['categoria_id', 'publicacion_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categoria_publicacion');
    }
}
