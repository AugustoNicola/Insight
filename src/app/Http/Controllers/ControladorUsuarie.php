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
        if (Auth::check()) {
            return redirect("/publicaciones", 303)->withErrors([
                "autenticacion" => "Ya has iniciado sesión."
            ])->withInput(); // 303: See Other
        }
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
        if (Auth::check()) {
            return redirect("/publicaciones", 303)->withErrors([
                "autenticacion" => "Ya has iniciado sesión."
            ])->withInput(); // 303: See Other
        }
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

    public function cerrarSesion(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        return redirect('/');
    }

    public function mostrarPerfil()
    {
        $usuarie = null;

        if (Auth::check()) {
            //# usuarie autenticade, podemos devolver informacion

            $usuarie = Usuarie::with("publicaciones")
                ->withCount("publicaciones")
                ->with("reacciones")
                ->where("id", Auth::id())
                ->first();

            // agregamos un campo al objeto devuelto con el calculo de cantidad de guardados
            $usuarie->cantidad_guardados = array_reduce(
                $usuarie
                    ->reacciones // buscamos todas las reacciones
                    ->pluck("pivot.relacion")->toArray() // filtramos solo a tipo de reaccion ("me gusta" | "guardar")
                ,
                function ($valorPrevio, $reaccion) {
                    // contamos cuantos "me_gusta" hay
                    return $reaccion == "guardar" ? $valorPrevio + 1 : $valorPrevio;
                },
                0
            );
        }

        return view("paginas.perfil", [
            "usuarie" => $usuarie
        ]);
    }

    public function mostrarGuardados()
    {
        $autenticade = false;
        $publicacionesGuardadas = [];

        if (Auth::check()) {
            //# usuarie autenticade, podemos devolver informacion
            $autenticade = true;

            $usuarie = Usuarie::with("reacciones")
                ->where("id", Auth::id())
                ->first();

            // filtramos a un nuevo array con solo las publicaciones guardadas
            foreach ($usuarie->reacciones as $publicacion) {
                if ($publicacion->pivot->relacion == "guardar") {
                    array_push($publicacionesGuardadas, $publicacion);
                }
            }
        }

        return view("paginas.guardados", [
            "publicaciones" => $publicacionesGuardadas,
            "autenticade" => $autenticade
        ]);
    }

    public function vistaEditarPerfil()
    {
        if (Auth::guest()) {
            //? usuarie no autenticade, no puede editar
            return redirect("/entrar", 303)->withErrors([
                "autenticacion" => "Para poder editar tu cuenta primero es necesario iniciar sesión."
            ]); // 303: See Other
        }

        //* usuarie autenticade, devolviendo informacion de usuarie
        $usuarie = Usuarie::find(Auth::id());

        return view("paginas.editar-perfil", [
            "usuarie" => $usuarie
        ]);
    }

    public function editarPerfil(Request $request)
    {
        $validador = Validator::make(
            $request->only(["nombre", "contrasena", "contrasena_confirmation", "imagen"]),
            [
                "nombre" => "required|between:3,100",
                "contrasena" => "nullable|between:3,40|confirmed",
                "imagen" => "bail|image|mimes:jpg,png,jpeg|max:2048|dimensions:min_width=100,min_height=100,max_width=5000,max_height=5000"
            ],
            [
                "nombre.required" => "El campo nombre de usuarie es obligatorio.",
                "nombre.between" => "El campo nombre de usuarie debe tener entre :min y :max caracteres.",

                "contrasena.between" => "El campo contraseña debe tener entre :min y :max caracteres.",
                "contrasena.confirmed" => "Las contraseñas no coinciden.",

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
            //? usuarie no autenticade, no puede editar
            return redirect("/entrar", 303)->withErrors([
                "autenticacion" => "Para poder editar tu cuenta primero es necesario iniciar sesión."
            ])->withInput(); // 303: See Other
        }

        //* campos validos y autenticade
        $camposValidados = $validador->validated();

        $usuarie = Usuarie::find(Auth::id());

        $usuarie->nombre = $camposValidados["nombre"];
        if ($camposValidados["contrasena"]) {
            $usuarie->contrasena = Hash::make($camposValidados["contrasena"]);
        }

        if ($request->hasFile("imagen")) {
            // # imagen cargada
            $request->file("imagen")->store("/public/usuaries"); // /storage/app/public/usuaries/img.xyz
            $usuarie->imagen = $request->file("imagen")->hashName();
        }

        $usuarie->save(); // cargamos usuarie a la BBDD

        return redirect("/perfil", 302); // 302: Found
    }
}
