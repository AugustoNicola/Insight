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
    protected $guarded = [];

    # le usuarie que escribio la publicacion
    public function autore()
    {
        return $this->belongsTo(Usuarie::class, "usuarie_id");
    }

    # los comentarios que tiene esta publicacion
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, "publicacion_id");
    }

    # las categorias a las que pertenece esta publicacion
    public function categorias()
    {
        return $this->belongsToMany(
            Categoria::class,
            "categoria_publicacion",
            "publicacion_id",
            "categoria_id"
        )->using(CategoriaPublicacion::class);
    }

    # las reacciones (me gusta, guardar) que tiene esta publicacion
    public function reacciones()
    {
        return $this->belongsToMany(Usuarie::class, "reacciones")
            ->withPivot("relacion")
            ->using(Reaccion::class);
    }

    public function meGusta()
    {
        return $this->belongsToMany(Usuarie::class, "reacciones")
            ->withPivot("relacion")
            ->wherePivot("relacion", "me_gusta")
            ->using(Reaccion::class);
    }
}
