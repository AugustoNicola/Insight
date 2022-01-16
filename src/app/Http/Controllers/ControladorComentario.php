<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models;

class ControladorComentario extends Controller
{
    public function publicarComentario(Request $request)
    {
        $validador = Validator::make(
            $request->only(["cuerpo", "id"]),
            [
                "id" => "required|numeric",
                "cuerpo" => "required|max:500"
            ],
            [
                "id.required" => "Ocurrió un error al intentar publicar el comentario.",
                "id.numeric" => "Ocurrió un error al intentar publicar el comentario.",
                "cuerpo.required" => "El cuerpo del comentario es obligatorio.",
                "cuerpo.max" => "El comentario no puede ser mayor de :max caracteres."
            ]
        );

        if ($validador->fails()) {
            // ? campos invalidos
            return back(303)->withErrors($validador)->withInput(); // 303: See Other
        }

        // * campos validos
        $camposValidados = $validador->validated();

        if (Auth::guest()) {
            // ? usuarie no autenticade, no puede publicar comentario
            $validador->errors()->add(
                "comentario",
                "Para publicar un comentario es necesario iniciar sesión."
            );
            return back(303)->withErrors($validador)->withInput(); // 303: See Other
        }

        if (Models\Publicacion::find($camposValidados["id"]) === null) {
            // ? publicacion inexistente
            $validador->errors()->add(
                "comentario",
                "Ocurrió un error al intentar publicar el comentario."
            );
            return back(303)->withErrors($validador)->withInput(); // 303: See Other
        }

        // * campos validados y usuarie autenticade

        $comentarioCreado = new Models\Comentario;
        $comentarioCreado->cuerpo = $camposValidados["cuerpo"];
        $comentarioCreado->publicacion_id = $camposValidados["id"];
        $comentarioCreado->usuarie_id = Auth::id();
        $comentarioCreado->save(); // cargamos usuarie a la BBDD

        return redirect("/publicaciones/" . $camposValidados["id"], 302); // 302: Found
    }
}
