<?php

namespace Tests\Feature\Usuaries;

use Tests\TestCase;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models;

class RutaPerfilTest extends TestCase
{
    use RefreshDatabase;

    public function test_DeberiaMostrarPerfil_CuandoAutenticade()
    {
        $usuarie = Models\Usuarie::factory()->create();

        Models\Publicacion::factory()->usuarieExistente()->count(3)->create();
        $usuarie->reacciones()->attach(Models\Publicacion::first()->id, ["relacion" => "guardar"]);

        $response = $this->actingAs($usuarie)->get("/perfil");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();

        $response->assertSeeText($usuarie->nombre);
        $response->assertSeeText("Te uniste el " . $usuarie->fecha_creacion->format("d/m/Y"));
        $response->assertSeeText("3 publicaciones");
        $response->assertSeeText("1 publicación guardada");

        $response->assertDontSeeText("Para ver tu perfil primero es necesario iniciar sesión.");
    }

    public function test_DeberiaMostrarMensajeLoggear_CuandoNoAutenticade()
    {
        $response = $this->get("/perfil");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertSeeText("Para ver tu perfil primero es necesario iniciar sesión.");
    }

    public function test_DeberiaMostrarMensaje_CuandoSinPublicaciones()
    {
        $usuarieConPublicaciones = Models\Usuarie::factory()->create();

        Models\Publicacion::factory()->usuarieExistente()->count(3)->create();

        $usuarieSinPublicaciones = Models\Usuarie::factory()->create();

        $response = $this->actingAs($usuarieSinPublicaciones)->get("/perfil");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertSeeText("Todavía no escribiste ninguna publicación.");
        $response->assertDontSeeText("Para ver tu perfil primero es necesario iniciar sesión.");
    }

    public function test_DeberiaMostrarPublicaciones_CuandoTienePublicaciones()
    {
        $usuarie = Models\Usuarie::factory()->create();

        $publicaciones = Models\Publicacion::factory()->usuarieExistente()->count(3)->create();

        $response = $this->actingAs($usuarie)->get("/perfil");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertDontSeeText("Para ver tu perfil primero es necesario iniciar sesión.");
        $response->assertDontSeeText("Todavía no escribiste ninguna publicación.");

        foreach ($publicaciones as $publicacion) {
            $response->assertSeeText($publicacion->titulo);
            $response->assertSeeText($publicacion->categorias->pluck("nombre")->toArray());
            $response->assertSeeText("por " . $publicacion->autore->nombre);
        }
    }
}
