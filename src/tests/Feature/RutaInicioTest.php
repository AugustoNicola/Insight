<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models;

class RutaInicioTest extends TestCase
{
    use RefreshDatabase; // uso RefreshDatabase para que el default sea sin modelos

    public function test_DeberiaMostrarSeleccionPublicacionesYCategorias_CuandoHayPublicacionesYCategorias()
    {
        $publicaciones = Models\Publicacion::factory()->count(10)->create();
        $categorias = Models\Categoria::factory()->count(6)->create();

        //le agregamos me gusta en forma decreciente a las primeras seis publicaciones
        $publicaciones[0]->reacciones()->attach(Models\Usuarie::all()->random(8)->pluck('id')->toArray(), ["relacion" => "me_gusta"]);
        $publicaciones[1]->reacciones()->attach(Models\Usuarie::all()->random(7)->pluck('id')->toArray(), ["relacion" => "me_gusta"]);
        $publicaciones[2]->reacciones()->attach(Models\Usuarie::all()->random(5)->pluck('id')->toArray(), ["relacion" => "me_gusta"]);
        $publicaciones[3]->reacciones()->attach(Models\Usuarie::all()->random(5)->pluck('id')->toArray(), ["relacion" => "me_gusta"]);
        $publicaciones[4]->reacciones()->attach(Models\Usuarie::all()->random(3)->pluck('id')->toArray(), ["relacion" => "me_gusta"]);
        $publicaciones[5]->reacciones()->attach(Models\Usuarie::all()->random(1)->pluck('id')->toArray(), ["relacion" => "me_gusta"]);

        // le agregamos publicaciones en forma decreciente a las primeras cinco categorias
        $categorias[0]->publicaciones()->attach($publicaciones->random(6)->pluck('id')->toArray());
        $categorias[1]->publicaciones()->attach($publicaciones->random(5)->pluck('id')->toArray());
        $categorias[2]->publicaciones()->attach($publicaciones->random(4)->pluck('id')->toArray());
        $categorias[3]->publicaciones()->attach($publicaciones->random(3)->pluck('id')->toArray());
        $categorias[4]->publicaciones()->attach($publicaciones->random(1)->pluck('id')->toArray());

        $response = $this->get("/");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertDontSeeText("No se han encontrado publicaciones.");
        $response->assertDontSeeText("No se han encontrado categorías.");

        foreach (array_slice($publicaciones->toArray(), 0, 6) as $publicacion) {
            $response->assertSeeText($publicacion["titulo"]);
        }

        foreach (array_slice($categorias->toArray(), 0, 5) as $categoria) {
            $response->assertSeeText($categoria["nombre"]);
        }
    }

    public function test_DeberiaMostrarTodasLasPublicaciones_CuandoHayMenorLimitePublicaciones()
    {
        $publicaciones = Models\Publicacion::factory()->count(4)->create();

        //las dos primeras tienen me gusta, pero las otras dos no
        $publicaciones[0]->reacciones()->attach(Models\Usuarie::all()->random(4)->pluck('id')->toArray(), ["relacion" => "me_gusta"]);
        $publicaciones[1]->reacciones()->attach(Models\Usuarie::all()->random(2)->pluck('id')->toArray(), ["relacion" => "me_gusta"]);

        $response = $this->get("/");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertDontSeeText("No se han encontrado publicaciones.");

        foreach ($publicaciones as $publicacion) {
            $response->assertSeeText($publicacion->titulo);
        }
    }

    public function test_DeberiaMostrarTodasLasCategorias_CuandoHayMenorLimiteCategorias()
    {
        $publicaciones = Models\Publicacion::factory()->count(10)->create();
        $categorias = Models\Categoria::factory()->count(3)->create();

        // las dos primeras tienen publicaciones, pero la otra no
        $categorias[0]->publicaciones()->attach($publicaciones->random(6)->pluck('id')->toArray());
        $categorias[1]->publicaciones()->attach($publicaciones->random(3)->pluck('id')->toArray());

        $response = $this->get("/");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertDontSeeText("No se han encontrado categorías.");

        foreach ($categorias as $categoria) {
            $response->assertSeeText($categoria->nombre);
        }
    }

    public function test_DeberiaMostrarMensajePublicaciones_CuandoNoHayPublicaciones()
    {
        $response = $this->get("/");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertSeeText("No se han encontrado publicaciones.");
    }

    public function test_DeberiaMostrarMensajeCategorias_CuandoNoHayCategorias()
    {
        $response = $this->get("/");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertSeeText("No se han encontrado categorías.");
    }
}
