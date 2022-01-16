<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models;
use Illuminate\Support\Facades\Auth;

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

    public function informacionPublicacion(Request $request, $id)
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

        //# inyectando errores por query string y de comentario
        switch ($request->query("error")) {
            case "auth":
                $errorQueryString = ["reaccion" => "Para reaccionar a una publicación es necesario iniciar sesión."];
                break;

            case "publicacion":
            case "desconocido":
                $errorQueryString = ["reaccion" => "Ocurrió un error al intentar reaccionar a la publicación."];
                break;

            case null:
            default:
                $errorQueryString = [];
                break;
        }

        $erroresComentario = session()->get("errors") != null ? session()->get("errors")->toArray() : [];

        $errores = array_merge($errorQueryString, $erroresComentario);


        //# informacion acerca de que reacciones ya han sido realizadas por le usuarie
        if (!Auth::guest()) {
            $dadoMeGusta = Models\Usuarie::Find(Auth::id())
                ->reacciones()
                ->where("publicacion_id", $publicacion->id)
                ->where("relacion", "me_gusta")
                ->exists();

            $dadoGuardar = Models\Usuarie::Find(Auth::id())
                ->reacciones()
                ->where("publicacion_id", $publicacion->id)
                ->where("relacion", "guardar")
                ->exists();
        }

        //* finalmente devuelve view con info de publicacion, errores y reacciones tomadas
        return view("paginas.publicacion", [
            "publicacion" => $publicacion,
            "dadoMeGusta" => $dadoMeGusta,
            "dadoGuardar" => $dadoGuardar
        ])->withErrors($errores);
    }
}
