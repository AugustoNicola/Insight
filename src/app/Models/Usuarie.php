<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Usuarie extends Model implements AuthenticatableContract
{
    use Authenticatable, HasFactory, Notifiable;
    // use HasApiTokens;

    const CREATED_AT = "fecha_creacion";
    const UPDATED_AT = "fecha_actualizacion";
    protected $fillable = ["nombre", "contrasena", "imagen"];
    protected $hidden = ["contrasena"];

    # sobreescribimos este metodo para que se use nuestro campo de password "contrasena"
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

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
        return $this->belongsToMany(Publicacion::class, "reacciones")
            ->withPivot("relacion")
            ->using(Reaccion::class);
    }
}
