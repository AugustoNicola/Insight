<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuarie extends Model
{
    use HasFactory;
    
    const CREATED_AT = "fecha_creacion";
    const UPDATED_AT = "fecha_actualizacion";
    protected $guarded = [];
    
    # las publicaciones escritos por este usuarie
    public function publicaciones()
    {
        return $this->hasMany(Publicacion::class, "usuarie_id");
    }
    
    # los comentarios escritos por este usuarie
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, "usuarie_id");
    }
    
    # las reacciones que este usuarie ha realizado
    public function reacciones()
    {
        return $this->belongsToMany(
            Publicacion::class,
            "reacciones",
            "usuarie_id",
            "publicacion_id"
        )->using(Reaccion::class);
    }
}
