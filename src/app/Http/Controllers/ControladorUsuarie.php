<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControladorUsuarie extends Controller
{
    public function formInicioSesion()
    {
        return view("entrar");
    }

    public function registro(Request $request)
    {
        //
    }

    public function inicioSesion(Request $request)
    {
        $credenciales = [
            "nombre" => $request->input("nombre"),
            "password" => $request->input("contrasena")
        ];

        return Auth::attempt($credenciales) ? "autenticado" : "NO";
    }
}
