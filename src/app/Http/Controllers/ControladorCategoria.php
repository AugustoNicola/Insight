<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models;

class ControladorCategoria extends Controller
{
    public function listarCategorias()
    {
        $categorias = Models\Categoria::withCount("publicaciones")->get();

        return view("paginas.categorias", [
            "categorias" => $categorias
        ]);
    }
}
