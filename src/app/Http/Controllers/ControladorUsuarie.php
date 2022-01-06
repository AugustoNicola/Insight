<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\Usuarie;

class ControladorUsuarie extends Controller
{
    public function vistaRegistrarse()
    {
        return view("paginas.registrarse");
    }

    public function registrarse(Request $request)
    {
        $validador = Validator::make(
            $request->only(["nombre", "contrasena", "contrasena_confirmation", "imagen"]),
            [
                "nombre" => "required|max:100",
                "contrasena" => "required|between:3,40|confirmed",
                "imagen" => "bail|image|mimes:jpg,png,jpeg|max:2048|dimensions:min_width=100,min_height=100,max_width=5000,max_height=5000"
            ],
            [
                "nombre.required" => "El campo nombre de usuarie es obligatorio.",
                "nombre.max" => "El nombre de usuarie no puede ser mayor de :max caracteres.",

                "contrasena.required" => "El campo contraseña es obligatorio.",
                "contrasena.between" => "El campo contraseña debe tener entre :min y :max caracteres.",
                "contrasena.confirmed" => "Las contraseñas no coinciden.",

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

        // * campos validos
        $camposValidados = $validador->validated();

        $usuarieCreade = new Usuarie;
        $usuarieCreade->nombre = $camposValidados["nombre"];
        $usuarieCreade->contrasena = Hash::make($camposValidados["contrasena"]);

        if ($request->hasFile("imagen")) {
            // # imagen cargada
            $request->file("imagen")->store("/public/usuaries"); // /storage/app/public/usuaries/img.xyz
            $usuarieCreade->imagen = $request->file("imagen")->hashName();
        }

        $usuarieCreade->save(); // cargamos usuarie a la BBDD

        $request->session()->regenerate(); // evitamos session fixation
        Auth::login($usuarieCreade); // iniciamos sesion como nueve usuarie
        return redirect("/publicaciones", 302); // 302: Found
    }

    public function vistaEntrar()
    {
        return view("paginas.entrar");
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
