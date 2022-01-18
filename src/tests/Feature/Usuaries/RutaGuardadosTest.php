<?php

namespace Tests\Feature\Usuaries;

use Tests\TestCase;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models;

class RutaGuardadosTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_DeberiaMostrarPublicaciones_CuandoAutenticadeYHayGuardados()
    {
        $usuarie = Models\Usuarie::factory()->create();
        Models\Categoria::factory()->count(4)->create();

        $publicaciones = Models\Publicacion::factory()->count(5)->create()
            ->each(function ($publicacion) {
                $publicacion->categorias()->attach(Models\Categoria::all()->random(3)->pluck('id')->toArray());
            });

        $usuarie->reacciones()->attach($publicaciones->random(3)->pluck('id')->toArray(), ["relacion" => "guardar"]);

        $response = $this->actingAs($usuarie)->get("/guardados");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertDontSeeText("Todavía no guardaste ninguna publicación.");

        foreach ($usuarie->reacciones as $publicacion) {
            $response->assertSeeText($publicacion->titulo);
            $response->assertSeeText($publicacion->categorias->pluck("nombre")->toArray());
            $response->assertSeeText("por " . $publicacion->autore->nombre);
        }
    }

    public function test_DeberiaMostrarMensajeSinPublicaciones_CuandoAutenticadeYNoHayGuardados()
    {
        $usuarie = Models\Usuarie::factory()->create();

        $response = $this->actingAs($usuarie)->get("/guardados");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertSeeText("Todavía no guardaste ninguna publicación.");
    }

    public function test_DeberiaMostrarMensajeIniciarSesion_CuandoNoAutenticade()
    {
        $response = $this->get("/guardados");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertSeeText("Para ver tus publicaciones guardadas primero es necesario iniciar sesión.");
        $response->assertDontSeeText("Todavía no guardaste ninguna publicación.");
    }
}
