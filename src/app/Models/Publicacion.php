<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    use HasFactory;
    
    protected $table = "publicaciones";
    const CREATED_AT = "fecha_creacion";
    const UPDATED_AT = "fecha_actualizacion";
    
    # las categorias a las que pertenece esta publicacion
    public function categorias()
    {
        return $this->belongsToMany(
            Categoria::class,
            "categoria_publicacion",
            "publicacion_id",
            "categoria_id"
        );
    }
}
