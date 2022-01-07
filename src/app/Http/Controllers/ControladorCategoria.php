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

    public function informacionCategoria($id)
    {
        $categoria = Models\Categoria::with("publicaciones")
            ->withCount("publicaciones")
            ->where("id", $id)
            ->first();

        if ($categoria === null) {
            // ? categoria no encontrada en la BBDD
            return abort(404);
        }

        // * categoria encontrada! Devolviendo informacion a la view

        return view("paginas.categoria", [
            "categoria" => $categoria
        ]);
    }
}
