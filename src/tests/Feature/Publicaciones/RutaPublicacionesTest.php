<?php

namespace Tests\Feature\Usuaries;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Arr;

use App\Models;

class RutaPublicacionesTest extends TestCase
{
    use RefreshDatabase; // uso RefreshDatabase para que el default sea sin publicaciones

    public function test_DeberiaMostrarMensajeVacio_CuandoNoHayPublicaciones()
    {
        // no hay ninguna publicacion en la base de datos

        $response = $this->get("/publicaciones");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertSeeText("No se han encontrado publicaciones.");
    }

    public function test_DeberiaMostrarTodasLasPublicaciones_CuandoQueryNormal()
    {
        Models\Categoria::factory()->count(6)->create();

        $publicaciones = Models\Publicacion::factory()
            ->count(20)
            ->create()
            ->each(function ($publicacion) {
                $publicacion->categorias()->attach(Models\Categoria::all()->random(rand(1, 4))->pluck('id')->toArray());

                $publicacion->reacciones()->attach(Models\Usuarie::all()->random(1)->pluck('id')->toArray(), ["relacion" => Arr::random(["me_gusta", "guardar"])]);
            });

        $response = $this->get("/publicaciones");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertDontSeeText("No se han encontrado publicaciones.");

        foreach ($publicaciones as $publicacion) {
            $response->assertSeeText($publicacion->titulo);
            $response->assertSeeText($publicacion->autore()->first()->nombre);
            $response->assertSeeText($publicacion->categorias()->get()->pluck("nombre")->toArray()); // cada nombre de categoria relacionada

            // cantidad de "me gusta"
            $cantidadMeGusta = array_reduce(
                $publicacion
                    ->reacciones()->get() //lista con todas las reacciones
                    ->pluck("pivot.relacion")->toArray() // filtramos solo a tipo de reaccion ("me gusta" | "guardar")
                ,
                function ($valorPrevio, $reaccion) {
                    // contamos cuantos "me_gusta" hay
                    return $reaccion == "me_gusta" ? $valorPrevio + 1 : $valorPrevio;
                },
                0
            );
            $response->assertSeeText($cantidadMeGusta . " me gusta");
        }
    }
}
