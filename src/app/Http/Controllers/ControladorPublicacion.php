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

    public function informacionPublicacion($id)
    {
        $publicacion = Models\Publicacion::with("reacciones")
            ->withCount("comentarios")
            ->where("id", $id)
            ->first();

        if ($publicacion === null) {
            // ? publicacion no encontrada en la BBDD
            return abort(404);
        }

        // agregamos un campo al objeto devuelto con el calculo de cantidad de me gusta
        $publicacion->cantidad_me_gusta = array_reduce(
            $publicacion
                ->reacciones // buscamos todas las reacciones
                ->pluck("pivot.relacion")->toArray() // filtramos solo a tipo de reaccion ("me gusta" | "guardar")
            ,
            function ($valorPrevio, $reaccion) {
                // contamos cuantos "me_gusta" hay
                return $reaccion == "me_gusta" ? $valorPrevio + 1 : $valorPrevio;
            },
            0
        );

        // * publicacion encontrada! Devolviendo informacion a la view

        return view("paginas.publicacion", [
            "publicacion" => $publicacion
        ]);
    }
}
