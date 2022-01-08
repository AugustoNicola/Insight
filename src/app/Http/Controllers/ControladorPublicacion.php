<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models;

class ControladorPublicacion extends Controller
{
    public function listarPublicaciones(Request $request)
    {
        $categoriasDestacadas = Models\Categoria::query()->take(5)->get();

        $publicaciones = Models\Publicacion::query();
        if ($request->query("titulo")) {
            $publicaciones = $publicaciones->where("titulo", "like", $request->query("titulo") . "%");
        }
        $publicaciones = $publicaciones->get();

        return view("paginas.publicaciones", [
            "publicaciones" => $publicaciones,
            "categoriasDestacadas" => $categoriasDestacadas
        ]);
    }
}
