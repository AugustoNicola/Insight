<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaPublicacion extends Model
{
    use HasFactory;
    
    protected $table = "categoria_publicacion";
    public $timestamps = false;
}
