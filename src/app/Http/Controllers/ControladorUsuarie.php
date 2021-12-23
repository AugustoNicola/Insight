<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ControladorUsuarie extends Controller
{
    public function formularioEntrar()
    {
        return view("entrar");
    }

    public function entrar(Request $request)
    {
        // # comprobamos que los campos sean validos
        $validador = Validator::make(
            $request->only(["nombre", "contrasena"]),
            [
                "nombre" => "required|max:100",
                "contrasena" => "required|between:3,40"
            ],
            [
                "nombre.required" => "El campo nombre de usuarie es obligatorio.",
                "nombre.max" => "El nombre de usuarie no puede ser mayor de :max caracteres.",
                "contrasena.required" => "El campo contraseña es obligatorio.",
                "contrasena.between" => "El campo contraseña debe tener entre :min y :max caracteres."
            ]
        );

        if ($validador->fails()) {
            // ? campos invalidos
            return back(303)->withErrors($validador)->withInput(); // 303: See Other
        }

        // * campos validos
        // # comprobamos credenciales correctas
        $camposValidados = $validador->validated();
        $credenciales = [
            "nombre" => $camposValidados["nombre"],
            "password" => $camposValidados["contrasena"]
        ];

        if (Auth::attempt($credenciales)) {
            // * inicio de sesion exitoso
            $request->session()->regenerate(); // evitamos session fixation
            return redirect("/", 302); // 302: Found
        } else {
            // ? inicio de sesion fallido
            $validador->errors()->add(
                "autenticacion",
                "Las credenciales ingresadas no son correctas. Por favor verificá que escribiste correctamente los datos."
            );
            return back(303)->withErrors($validador)->withInput(); // 303: See Other
        }
    }
}
