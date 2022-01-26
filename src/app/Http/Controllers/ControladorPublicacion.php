<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


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
            "categoriasDestacadas" => $categoriasDestacadas,
            "query" => $request->query("titulo", "")
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
        $dadoMeGusta = false;
        $dadoGuardar = false;
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

    public function vistaPublicarPublicacion()
    {
        if (Auth::guest()) {
            return redirect("/entrar")->withErrors([
                "autenticacion" => "Para poder escribir una publicación primero es necesario iniciar sesión."
            ])->withInput(); // 303: See Other
        }

        $categorias = Models\Categoria::All();

        return view("paginas.escribir", [
            "categorias" => $categorias
        ]);
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
                "imagen.dimensions" => "La imagen subida debe medir entre 100x100 y 5000x5000 pixeles."
            ]
        );

        if ($validador->fails()) {
            // ? campos invalidos
            return back(303)->withErrors($validador)->withInput(); // 303: See Other
        }

        if (Auth::guest()) {
            //? usuarie no autenticade, no puede reaccionar
            return back(303)->withErrors([
                "autenticacion" => "Para escribir una publicación es necesario iniciar sesión."
            ])->withInput(); // 303: See Other
        }

        //* campos validos
        $camposValidados = $validador->validated();

        $publicacionCreada = new Models\Publicacion;
        $publicacionCreada->titulo = $camposValidados["titulo"];
        $publicacionCreada->usuarie_id = Auth::id();
        $publicacionCreada->cuerpo = $camposValidados["cuerpo"];

        if ($request->hasFile("imagen")) {
            // # imagen cargada
            $request->file("imagen")->store("/public/publicaciones"); // /storage/app/public/publicaciones/img.xyz
            $publicacionCreada->portada = $request->file("imagen")->hashName();
        }

        $publicacionCreada->save(); // cargamos usuarie a la BBDD
        $publicacionCreada->categorias()->attach($camposValidados["categorias"]);

        return redirect("/publicaciones/" . $publicacionCreada->id, 302); // 302: Found
    }

    public function vistaEditarPublicacion($id)
    {
        $publicacion = Models\Publicacion::with("categorias")
            ->where("id", $id)
            ->first();

        if ($publicacion === null) {
            // ? publicacion no encontrada en la BBDD
            return abort(404);
        }

        if (Auth::guest() || Auth::id() != $publicacion->usuarie_id) {
            //? usuarie no autenticade como autore, no puede editar
            return redirect("/publicaciones/" . $publicacion->id, 303)->withErrors([
                "autenticacion" => "No tiene permisos necesarios para editar esta publicación."
            ])->withInput(); // 303: See Other
        }

        $categorias = Models\Categoria::All();

        return view("paginas.editar-publicacion", [
            "publicacion" => $publicacion,
            "categorias" => $categorias
        ]);
    }

    public function editarPublicacion(Request $request, $id)
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
                "imagen.dimensions" => "La imagen subida debe medir entre 100x100 y 5000x5000 pixeles."
            ]
        );

        if ($validador->fails()) {
            // ? campos invalidos
            return back(303)->withErrors($validador)->withInput(); // 303: See Other
        }

        $publicacion = Models\Publicacion::Find($id);
        if ($publicacion === null) {
            // ? publicacion no encontrada en la BBDD
            return back(303)->withErrors([
                "publicacion" => "Ocurrió un error al intentar editar la publicación."
            ])->withInput(); // 303: See Other
        }


        if (Auth::guest() || Auth::id() != $publicacion->usuarie_id) {
            //? usuarie no autenticade como autore, no puede editar
            return back(303)->withErrors([
                "autenticacion" => "No tiene permisos necesarios para editar esta publicación."
            ])->withInput(); // 303: See Other
        }

        //* campos validos y autenticade
        $camposValidados = $validador->validated();

        $publicacion->titulo = $camposValidados["titulo"];
        $publicacion->cuerpo = $camposValidados["cuerpo"];

        if ($request->hasFile("imagen")) {
            // # imagen cargada
            $request->file("imagen")->store("/public/publicaciones"); // /storage/app/public/publicaciones/img.xyz
            $publicacion->portada = $request->file("imagen")->hashName();
        }

        $publicacion->save(); // cargamos usuarie a la BBDD
        $publicacion->categorias()->detach();
        $publicacion->categorias()->attach($camposValidados["categorias"]);

        return redirect("/publicaciones/" . $publicacion->id, 302); // 302: Found
    }

    public function eliminarPublicacion($id)
    {
        $publicacion = Models\Publicacion::Find($id);
        if ($publicacion === null) {
            // ? publicacion no encontrada en la BBDD
            return redirect("/publicaciones", 303)->withErrors([
                "publicacion" => "Ocurrió un error al intentar eliminar la publicación."
            ])->withInput(); // 303: See Other
        }


        if (Auth::guest() || Auth::id() != $publicacion->usuarie_id) {
            //? usuarie no autenticade como autore, no puede editar
            return redirect("/publicaciones", 303)->withErrors([
                "autenticacion" => "No tiene permisos necesarios para eliminar esta publicación."
            ])->withInput(); // 303: See Other
        }

        //* publicacion valida y autenticade
        Models\CategoriaPublicacion::query()->where("publicacion_id", $id)->delete(); // eliminamos relaciones con categorias
        Models\Comentario::query()->where("publicacion_id", $id)->delete(); // eliminamos comentarios
        Models\Reaccion::query()->where("publicacion_id", $id)->delete(); // eliminamos reacciones
        if ($publicacion->portada != null) {
            // publicacion tiene imagen, borrar de disco
            Storage::disk("public")->delete("publicaciones/" . $publicacion->portada);
        }
        $publicacion->delete(); // eliminamos publicacion de la BBDD

        return redirect("/perfil", 302)->with([
            "exito" => "La publicación fue eliminada correctamente."
        ]); // 302: Found
    }
}
