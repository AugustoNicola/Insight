<?php

namespace Tests\Feature\Publicaciones;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models;

class RutaPublicacionTest extends TestCase
{
    use RefreshDatabase; // uso RefreshDatabase para que el default sea sin publicaciones

    public function test_DeberiaMostrarPaginaNoEncontrado_CuandoPublicacionNoExiste()
    {
        // no fue creada ninguna publicacion

        $response = $this->get("/publicacion/1234");
        $response->assertStatus(404); // 404: Not found
    }

    public function test_DeberiaMostrarInfo_CuandoExistePublicacion()
    {
        $publicacion = Models\Publicacion::factory()->create();
        $categorias = Models\Categoria::factory()->count(3)->create();
        Models\Usuarie::factory()->count(2)->create();

        $publicacion->categorias()->attach(Models\Categoria::all()->pluck('id')->toArray());
        $publicacion->reacciones()->attach(Models\Usuarie::all()->pluck('id')->toArray(), ["relacion" => "me_gusta"]);

        $response = $this->get("/publicaciones/" . $publicacion->id);

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();

        $response->assertSeeText($publicacion->titulo);
        foreach ($categorias as $categoria) {
            $response->assertSeeText($categoria->nombre);
        }
        $response->assertSeeText("por " . $publicacion->autore->nombre);
        $response->assertSeeText($publicacion->fecha_creacion->format("d/m/Y"));
        $response->assertSeeText("3 me gusta");
        $response->assertSeeText("0 comentarios");

        $response->assertSeeText(explode("\n", $publicacion->cuerpo));
    }

    public function test_DeberiaMostrarMensajeComentariosVacio_CuandoNoHayComentariosRelacionados()
    {
        $publicacion = Models\Publicacion::factory()->create();
        Models\Categoria::factory()->count(3)->create();
        $publicacion->categorias()->attach(Models\Categoria::all()->pluck('id')->toArray());

        // no hay comentarios para la publicacion

        $response = $this->get("/publicaciones/" . $publicacion->id);

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();

        $response->assertSeeText("No se han encontrado comentarios de esta publicación.");
    }

    public function test_DeberiaMostrarComentarios_CuandoHayComentariosRelacionados()
    {
        $publicacion = Models\Publicacion::factory()->create();
        Models\Categoria::factory()->count(3)->create();
        $publicacion->categorias()->attach(Models\Categoria::all()->pluck('id')->toArray());

        $comentarios = Models\Comentario::factory()->usuarieExistente()->publicacionExistente()->count(3)->create();

        $response = $this->get("/publicaciones/" . $publicacion->id);

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();

        $response->assertDontSeeText("No se han encontrado comentarios de esta publicación.");

        foreach ($comentarios as $comentario) {
            $response->assertSeeText($comentario->usuarie->nombre);
            $response->assertSeeText($comentario->fecha_creacion->format("d/m/Y"));
            $response->assertSeeText(explode("\n", $comentario->cuerpo));
        }
    }
}
