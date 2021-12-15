<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    
    const CREATED_AT = "fecha_creacion";
    const UPDATED_AT = null;
    protected $guarded = [];
    
    # las publicaciones que pertenecen a esta categoria
    public function publicaciones()
    {
        return $this->belongsToMany(
            Publicacion::class,
            "categoria_publicacion",
            "categoria_id",
            "publicacion_id"
        )->using(CategoriaPublicacion::class);
    }
}
