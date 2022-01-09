<?php

namespace Tests\Feature\Publicaciones;

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

    public function test_DeberiaMostrarPorTitulo_CuandoQueryConTitulo()
    {
        // vamos a estar queryando para "Et"
        Models\Publicacion::factory()->create(["titulo" => "Et"]); // SI
        Models\Publicacion::factory()->create(["titulo" => "Et nomina"]); // SI
        Models\Publicacion::factory()->create(["titulo" => "Et clausula"]); // SI
        Models\Publicacion::factory()->create(["titulo" => "Dominus Et"]); // NO
        Models\Publicacion::factory()->create(["titulo" => "CircEten"]); // NO
        Models\Publicacion::factory()->create(["titulo" => "Dumus"]); // NO

        $response = $this->get("/publicaciones?titulo=Et");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertDontSeeText("No se han encontrado publicaciones.");

        $response->assertSeeText("Et");
        $response->assertSeeText("Et nomina");
        $response->assertSeeText("Et clausula");

        $response->assertDontSeeText("Dominus Et");
        $response->assertDontSeeText("CircEten");
        $response->assertDontSeeText("Dumus");
    }

    /*public function test_DeberiaMostrarEnOrden_CuandoQueryConOrden()
    {
        // vamos a estar queryando para ordenar por más me gusta
        $publicaciones = Models\Publicacion::factory()->count(3)->create();

        // la primera publicacion tiene 0  me gusta

        $publicaciones[1]->reacciones()->attach(Models\Usuarie::all()->pluck('id')->toArray(), ["relacion" => "me_gusta"]); // la segunda publicacion tiene 3 me gusta

        $publicaciones[2]->reacciones()->attach(Models\Usuarie::all()->random(1)->pluck('id')->toArray(), ["relacion" => "me_gusta"]); // la tercera publicacion tiene 1 me gusta


        $response = $this->get("/publicaciones?orden=megusta_desc");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertDontSeeText("No se han encontrado publicaciones.");

        $response->assertSeeTextInOrder([$publicaciones[1]->nombre, $publicaciones[2]->nombre, $publicaciones[0]->nombre]);
    }

    public function test_DeberiaMostrarPorTituloYEnOrden_CuandoQueryConTituloYOrden()
    {
        // vamos a estar queryando para "Et" y ordenado por más me gusta
        Models\Publicacion::factory()->create(["titulo" => "Et"]); // SI, 3°
        Models\Publicacion::factory()->create(["titulo" => "Et nomina"]); // SI, 1°
        Models\Publicacion::factory()->create(["titulo" => "Et clausula"]); // SI, 2°
        Models\Publicacion::factory()->create(["titulo" => "Dominus Et"]); // NO
        Models\Publicacion::factory()->create(["titulo" => "CircEten"]); // NO
        Models\Publicacion::factory()->create(["titulo" => "Dumus"]); // NO
        $publicaciones = Models\Publicacion::all();

        $publicaciones[1]->reacciones()->attach(Models\Usuarie::all()->random(5)->pluck('id')->toArray(), ["relacion" => "me_gusta"]); // la segunda publicacion tiene 5 me gusta
        $publicaciones[2]->reacciones()->attach(Models\Usuarie::all()->random(3)->pluck('id')->toArray(), ["relacion" => "me_gusta"]); // la tercera publicacion tiene 3 me gusta
        // la primera publicacion tiene 0 me gusta

        $response = $this->get("/publicaciones?titulo=Et&orden=megusta_desc");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertDontSeeText("No se han encontrado publicaciones.");

        $response->assertDontSeeText("Dominus Et");
        $response->assertDontSeeText("CircEten");
        $response->assertDontSeeText("Dumus");

        $response->assertSeeInOrder(["Et nomina", "Et clausula", "Et"]);
    }*/

    public function test_DeberiaMostrarMensajeCategoriasVacias_CuandoNoHayCategorias()
    {
        // no hay ninguna categoria en la base de datos

        $response = $this->get("/publicaciones");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertSeeText("No se han encontrado categorías.");
    }

    public function test_DeberiaMostrarTodasLasCategorias_CuandoCategoriasMenosQueCinco()
    {
        $categorias = Models\Categoria::factory()->count(3)->create();

        $response = $this->get("/publicaciones");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        foreach ($categorias as $categoria) {
            $response->assertSeeText($categoria->nombre);
        }
    }

    public function test_DeberiaMostrarCincoCategorias_CuandoCategoriasMasQueCinco()
    {
        $categorias = Models\Categoria::factory()->count(10)->create();

        $response = $this->get("/publicaciones");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        foreach ($categorias->slice(0, 5) as $categoria) {
            $response->assertSeeText($categoria->nombre);
        }
    }
}
