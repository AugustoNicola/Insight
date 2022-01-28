<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

use App\Models;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        DB::table("categoria_publicacion")->truncate();
        DB::table("reacciones")->truncate();
        DB::table("comentarios")->truncate();
        DB::table("publicaciones")->truncate();
        DB::table("categorias")->truncate();
        DB::table("usuaries")->truncate();
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");


        $usuaries = json_decode(file_get_contents(database_path("datos/Usuaries.json"), true));
        $categorias = json_decode(file_get_contents(database_path("datos/Categorias.json"), true));
        $publicaciones = json_decode(file_get_contents(database_path("datos/Publicaciones.json"), true));
        $comentarios = json_decode(file_get_contents(database_path("datos/Comentarios.json"), true));

        $categoriasCreadas = [];
        $publicacionesCreadas = [];

        foreach ($usuaries as $usuarie) {
            Storage::put("/public/usuaries/" . $usuarie->imagen, File::get(database_path("datos/usuaries/" . $usuarie->imagen)));

            Models\Usuarie::factory()->create([
                "nombre" => $usuarie->nombre,
                "imagen" => $usuarie->imagen
            ]);
        }

        foreach ($categorias as $categoria) {
            $categoriaCreada =  Models\Categoria::factory()->create([
                "nombre" => $categoria->nombre,
                "descripcion" => $categoria->descripcion
            ]);

            array_push($categoriasCreadas, $categoriaCreada);
        }

        foreach ($publicaciones as $publicacion) {
            Storage::put("/public/publicaciones/" . $publicacion->portada, File::get(database_path("datos/publicaciones/" . $publicacion->portada)));


            $publicacionCreada = Models\Publicacion::factory()->usuarieExistente()->create([
                "titulo" => $publicacion->titulo,
                "cuerpo" => $publicacion->cuerpo,
                "portada" => $publicacion->portada
            ]);

            foreach ($publicacion->categorias as $categoria) {
                $publicacionCreada->categorias()->attach($categoriasCreadas[$categoria - 1]->id);
            }

            $publicacionCreada->reacciones()->attach(Models\Usuarie::all()->random(rand(1, 7))->pluck('id')->toArray(), ["relacion" => Arr::random(["me_gusta", "guardar"])]);

            array_push($publicacionesCreadas, $publicacionCreada);
        }

        foreach ($comentarios as $comentario) {
            Models\Comentario::factory()->usuarieExistente()->create([
                "publicacion_id" => $publicacionesCreadas[$comentario->publicacion - 1]->id,
                "cuerpo" => $comentario->cuerpo
            ]);
        }

        Models\Usuarie::factory()->create([
            "nombre" => "admin",
            "contrasena" => '$2y$10$DuZyfNwnYtUR8AylsL5oZOxJ1N/7/I7llPPUSwZEwRr7SkAxXNscC' // yo se la contrasena,
        ]);
    }
}
