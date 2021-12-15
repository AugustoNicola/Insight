<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;
    
    const CREATED_AT = "fecha_creacion";
    const UPDATED_AT = null;
    protected $guarded = [];
    
    # la publicacion en la que fue escrito este comentario
    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class, "publicacion_id");
    }
    
    # le usuarie que escribio este comentario
    public function usuarie()
    {
        return $this->belongsTo(Usuarie::class, "usuarie_id");
    }
}
