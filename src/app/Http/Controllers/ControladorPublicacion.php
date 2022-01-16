<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    public function publicarPublicacion(Request $request)
    {
        $validador = Validator::make(
            $request->only(["titulo", "categorias", "cuerpo", "imagen"]),
            [
                "titulo" => "required|between:3,110",
                "categorias" => "required",
                "cuerpo" => "required|between:3,2000",
                "imagen" => "bail|image|mimes:jpg,png,jpeg|max:2048|dimensions:min_width=100,min_height=100,max_width=5000,max_height=5000"
            ],
            [
                "titulo.required" => "El campo titulo es obligatorio.",
                "titulo.between" => "El campo titulo debe tener entre :min y :max caracteres.",

                "categorias.required" => "Es necesario seleccionar al menos una categoría.",

                "cuerpo.required" => "El campo cuerpo es obligatorio.",
                "cuerpo.between" => "El campo cuerpo debe tener entre :min y :max caracteres.",

                "imagen.image" => "El archivo subido debe ser una imagen.",
                "imagen.mimes" => "El archivo subido es de una extensión no soportada.",
                "imagen.max" => "El archivo subido es demasiado pesado.",
                "imagen.dimensions" => "La imagen subida es debe medir entre 100x100 y 5000x5000 pixeles."
            ]
        );

        if ($validador->fails()) {
            // ? campos invalidos
            return back(303)->withErrors($validador)->withInput(); // 303: See Other
        }

        //* campos validos
        $camposValidados = $validador->validated();

        $publicacionCreada = new Models\Publicacion;
        $publicacionCreada->titulo = $camposValidados["titulo"];
        $publicacionCreada->categorias()->attach($camposValidados["categorias"]);
        $publicacionCreada->cuerpo = $camposValidados["cuerpo"];

        if ($request->hasFile("imagen")) {
            // # imagen cargada
            $request->file("imagen")->store("/public/publicaciones"); // /storage/app/public/publicaciones/img.xyz
            $publicacionCreada->imagen = $request->file("imagen")->hashName();
        }

        $publicacionCreada->save(); // cargamos usuarie a la BBDD

        return redirect("/publicaciones/" . $publicacionCreada->id, 302); // 302: Found
    }
}
