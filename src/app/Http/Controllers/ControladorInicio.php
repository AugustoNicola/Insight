<?php

namespace App\Http\Controllers;

use App\Models;

class ControladorInicio extends Controller
{
    public function paginaInicio()
    {
        $categoriasDestacadas = Models\Categoria::withCount("publicaciones")->orderBy("publicaciones_count", "desc")->take(5)->get();

        $publicacionesDestacadas = Models\Publicacion::withCount("meGusta")->with("reacciones")->with("categorias")->orderBy("me_gusta_count", "desc")->take(6)->get();

        return view("paginas.inicio", [
            "publicacionesDestacadas" => $publicacionesDestacadas,
            "categoriasDestacadas" => $categoriasDestacadas
        ]);
    }
}
