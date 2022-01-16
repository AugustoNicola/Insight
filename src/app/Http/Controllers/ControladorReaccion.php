<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models;

class ControladorReaccion extends Controller
{
    public function publicarReaccion(Request $request)
    {
        $publicacion = Models\Publicacion::find($request->input("publicacion"));
        if ($publicacion === null) {
            //? publicacion inexistente, error
            return response("", 404); // 404: Not Found
        }

        if (Auth::guest()) {
            //? usuarie no autenticade, no puede reaccionar
            return response("", 401); // 401: Unauthorized
        }

        //* usuarie autenticade y publicacion valida, puede reaccionar
        $relacion = $request->input("relacion"); // "me_gusta" o "guardar"

        //# comprobacion de que relacion no este ya en la bbdd
        $existeReaccion = $publicacion->reacciones()
            ->where("usuarie_id", Auth::id())
            ->where("relacion", $relacion)
            ->exists();

        if (!$existeReaccion) {
            $publicacion->reacciones()
                ->attach(Auth::id(), ["relacion" => $relacion]);
        }
        return response("", 200);
    }

    public function eliminarReaccion(Request $request)
    {
        $publicacion = Models\Publicacion::find($request->input("publicacion"));
        if ($publicacion === null) {
            //? publicacion inexistente, error
            return response("", 404); // 404: Not Found
        }

        if (Auth::guest()) {
            //? usuarie no autenticade, no puede reaccionar
            return response("", 401); // 401: Unauthorized
        }

        //* usuarie autenticade y publicacion valida, puede eliminar reaccion
        $relacion = $request->input("relacion"); // "me_gusta" o "guardar"

        //# comprobacion de que relacion esta previamente en la BBDD
        $existeReaccion = $publicacion->reacciones()
            ->where("usuarie_id", Auth::id())
            ->where("relacion", $relacion)
            ->exists();

        if ($existeReaccion) {
            DB::table("reacciones")
                ->where("usuarie_id", Auth::id())
                ->where("publicacion_id", $publicacion->id)
                ->where("relacion", $relacion) // usamos el Facade\DB para poder filtrar por relacion
                ->delete();
        }

        return response("", 200);
    }
}
