<?php

namespace Tests\Feature\Usuaries;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

use App\Models;

class CrearComentarioTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_DeberiaPublicarComentarioYRedirigir_CuandoAutenticadoYPublicacionValida()
    {
        $publicacion = Models\Publicacion::factory()->create();
        // por alguna razon el factory devuelve una clase que el actingAs no puede usar, asi que creamos a le usuarie desde la clase y guardamos a la BBDD
        $usuarie = new Models\Usuarie([
            "nombre" => $this->faker->name(),
            "contrasena" => Hash::make($this->faker->word())
        ]);
        $usuarie->save();

        $cuerpo = $this->faker->paragraph(3);

        $response = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacion->id)
            ->post("/comentario", [
                "id" => $publicacion->id,
                "cuerpo" => $cuerpo
            ]);

        $response->assertStatus(302); // 302: Found
        $response->assertRedirect("/publicaciones/" . $publicacion->id);
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($usuarie);
    }

    public function test_NegarComentario_CuandoNoAutenticade()
    {
        $publicacion = Models\Publicacion::factory()->create();
        $cuerpo = $this->faker->paragraph(3);

        $response = $this->from("/publicaciones/" . $publicacion->id)->post("/comentario", [
            "id" => $publicacion->id,
            "cuerpo" => $cuerpo
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/publicaciones/" . $publicacion->id);
        $response->assertSessionHasErrors([
            "comentario" => "Para publicar un comentario es necesario iniciar sesión."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaNegarComentario_CuandoPublicacionInvalida()
    {
        // no hay publicaciones

        // por alguna razon el factory devuelve una clase que el actingAs no puede usar, asi que creamos a le usuarie desde la clase y guardamos a la BBDD
        $usuarie = new Models\Usuarie([
            "nombre" => $this->faker->name(),
            "contrasena" => Hash::make($this->faker->word())
        ]);
        $usuarie->save();

        $cuerpo = $this->faker->paragraph(3);

        $response = $this->actingAs($usuarie)
            ->from("/publicaciones")
            ->post("/comentario", [
                "id" => "1234",
                "cuerpo" => $cuerpo
            ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/publicaciones");
        $response->assertSessionHasErrors([
            "comentario" => "Ocurrió un error al intentar publicar el comentario."
        ]);
        $this->assertAuthenticatedAs($usuarie);
    }
}
