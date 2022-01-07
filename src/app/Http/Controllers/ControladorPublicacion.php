<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models;

class ControladorPublicacion extends Controller
{
    public function listarPublicaciones()
    {
        $publicaciones = Models\Publicacion::all();

        return view("paginas.publicaciones", [
            "publicaciones" => $publicaciones
        ]);
    }
}
