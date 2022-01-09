<?php

namespace Tests\Feature\Categorias;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models;

class RutaCategoriaTest extends TestCase
{
    use RefreshDatabase; // uso RefreshDatabase para que el default sea sin categorias

    public function test_DeberiaMostrarInfoYMensajeVacio_CuandoNoHayPublicacionesAsociadas()
    {
        // no hay ninguna categoria en la base de datos

        $categoria = Models\Categoria::factory()->create();

        $response = $this->get("/categorias/" . $categoria->id);

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertSeeText($categoria->nombre);
        $response->assertSeeText("Sin publicaciones");
        $response->assertSeeText($categoria->descripcion);
        $response->assertSeeText("No se han encontrado publicaciones de esta categoría.");
    }

    public function test_DeberiaMostrarInfoYPublicacionesAsociadas_CuandoTienePublicaciones()
    {
        $categoria = Models\Categoria::factory()->create();
        $publicaciones = Models\Publicacion::factory()->count(3)->create();
        $categoria->publicaciones()->attach($publicaciones->pluck('id')->toArray()); # la categoria tiene a las tres publicaciones

        // la primera publicacion tiene 1 me gusta y 1 guardado, pero solo deberia reconocer a los "2 me gusta"
        $publicaciones[0]->reacciones()->attach(Models\Usuarie::all()->random(1)->pluck('id')->toArray(), ["relacion" => "me_gusta"]);
        $publicaciones[0]->reacciones()->attach(Models\Usuarie::all()->random(1)->pluck('id')->toArray(), ["relacion" => "guardar"]);

        // la segunda publicacion tiene 3 me gusta
        $publicaciones[1]->reacciones()->attach(Models\Usuarie::all()->pluck('id')->toArray(), ["relacion" => "me_gusta"]);

        // la tercera publicacion no tiene me gusta

        $response = $this->get("/categorias/" . $categoria->id);

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertSeeText($categoria->nombre);
        $response->assertSeeText("3 publicaciones");
        $response->assertSeeText($categoria->descripcion);

        foreach ($publicaciones as $publicacion) {
            $response->assertSeeText($publicacion->titulo);
            $response->assertSeeText($publicacion->autore()->get()[0]->nombre);
            $response->assertSeeText($publicacion->categorias()->get()->pluck("nombre")->toArray()); // cada nombre de categoria relacionada
        }
        $response->assertSeeText("1 me gusta"); // primera publicacion
        $response->assertDontSeeText("2 me gusta"); // esto no deberia pasar
        $response->assertSeeText("3 me gusta"); // segunda publicacion
        $response->assertSeeText("0 me gusta"); // tercera publicacion
    }

    public function test_DeberiaMostrarPaginaNoEncontrado_CuandoCategoriaNoExiste()
    {
        // no fue creada ninguna categoria

        $response = $this->get("/categorias/1234");
        $response->assertStatus(404); // 404: Not found
    }
}
