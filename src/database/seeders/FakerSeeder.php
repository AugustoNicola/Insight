<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

use App\Models\Usuarie;
use App\Models\Categoria;
use App\Models\Publicacion;
use App\Models\Comentario;

class FakerSeeder extends Seeder
{
    
    public function run()
    {
        Usuarie::factory()
            ->count(10)
            ->create();
        
        Categoria::factory()
            ->count(4)
            ->create();
        
        Publicacion::factory()
            ->count(20)
            ->usuarieExistente()
            ->create()
            ->each(function ($publicacion) {
                $publicacion->categorias()->attach(Categoria::all()->random(rand(1, 4))->pluck('id')->toArray());
                
                $publicacion->reacciones()->attach(Usuarie::all()->random(rand(1, 7))->pluck('id')->toArray(), ["relacion" => Arr::random(["me_gusta", "guardar"])]);
            });
        
        Comentario::factory()
            ->count(20)
            ->publicacionExistente()
            ->usuarieExistente()
            ->create();
    }
}
