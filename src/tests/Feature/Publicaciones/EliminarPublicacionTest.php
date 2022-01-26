<?php

namespace Tests\Feature\Publicaciones;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

use Tests\TestCase;

use App\Models;

class EliminarPublicacionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_DeberiaEliminarPublicacionEImagen_CuandoPublicacionConImagenValidaYAutenticade()
    {
        $usuarie = Models\Usuarie::factory()->create();
        $categorias = Models\Categoria::factory()->count(3)->create();
        $archivo = UploadedFile::fake()->image("juan.jpg", 150, 150);

        $responseCrearPublicacion = $this->actingAs($usuarie)->post("/publicaciones", [
            "titulo" => $this->faker->sentence(8),
            "categorias" => $categorias->pluck("id"),
            "cuerpo" => $this->faker->paragraphs(3, true),
            "imagen" => $archivo
        ]);

        $publicacion = Models\Publicacion::first();

        $responseCrearPublicacion->assertStatus(302); // 302: Found
        $responseCrearPublicacion->assertRedirect("/publicaciones/" . $publicacion->id);
        $responseCrearPublicacion->assertSessionHasNoErrors();
        assertTrue(Storage::disk("public")->exists("publicaciones/" . $archivo->hashName()));
        assertEquals(1, Models\Publicacion::count());


        $responseEliminarPublicacion = $this->actingAs($usuarie)->delete("/publicaciones/" . $publicacion->id);

        $responseEliminarPublicacion->assertStatus(302); // 302: Found
        $responseEliminarPublicacion->assertRedirect("/perfil");
        $responseEliminarPublicacion->assertSessionHasNoErrors();
        $responseEliminarPublicacion->assertSessionHas("exito", "La publicación fue eliminada correctamente.");
        assertFalse(Storage::disk("public")->exists("publicaciones/" . $archivo->hashName()));
        assertEquals(0, Models\Publicacion::count());
    }

    public function test_DeberiaEliminarPublicacion_CuandoPublicacionValidaYAutenticade()
    {
        $usuarie = Models\Usuarie::factory()->create();
        $categorias = Models\Categoria::factory()->count(3)->create();

        $responseCrearPublicacion = $this->actingAs($usuarie)->post("/publicaciones", [
            "titulo" => $this->faker->sentence(8),
            "categorias" => $categorias->pluck("id"),
            "cuerpo" => $this->faker->paragraphs(3, true)
        ]);

        $publicacion = Models\Publicacion::first();

        $responseCrearPublicacion->assertStatus(302); // 302: Found
        $responseCrearPublicacion->assertRedirect("/publicaciones/" . $publicacion->id);
        $responseCrearPublicacion->assertSessionHasNoErrors();
        assertEquals(1, Models\Publicacion::count());


        $responseEliminarPublicacion = $this->actingAs($usuarie)->delete("/publicaciones/" . $publicacion->id);

        $responseEliminarPublicacion->assertStatus(302); // 302: Found
        $responseEliminarPublicacion->assertRedirect("/perfil");
        $responseEliminarPublicacion->assertSessionHasNoErrors();
        $responseEliminarPublicacion->assertSessionHas("exito", "La publicación fue eliminada correctamente.");
        assertEquals(0, Models\Publicacion::count());
    }

    public function test_DeberiaNegarEliminacion_CuandoNoAutenticade()
    {
        $usuarie = Models\Usuarie::factory()->create();
        $categorias = Models\Categoria::factory()->count(3)->create();

        $publicacion = Models\Publicacion::factory()->usuarieExistente()->create();
        $publicacion->categorias()->attach($categorias->pluck('id')->toArray());

        //# no autenticade
        $responseNoAutenticade = $this->delete("/publicaciones/" . $publicacion->id);
        $responseNoAutenticade->assertStatus(303); // 303: See Other
        $responseNoAutenticade->assertRedirect("/publicaciones");
        $responseNoAutenticade->assertSessionHasErrors([
            "autenticacion" => "No tiene permisos necesarios para eliminar esta publicación."
        ]);

        //# autenticade como usuarie que no es autore
        $otreUsuarie = Models\Usuarie::factory()->create();

        $responseNoAutenticade = $this->actingAs($otreUsuarie)->delete("/publicaciones/" . $publicacion->id);
        $responseNoAutenticade->assertStatus(303); // 303: See Other
        $responseNoAutenticade->assertRedirect("/publicaciones");
        $responseNoAutenticade->assertSessionHasErrors([
            "autenticacion" => "No tiene permisos necesarios para eliminar esta publicación."
        ]);
    }

    public function test_DeberiaNegarEliminacion_CuandoPublicacionNoExiste()
    {
        $usuarie = Models\Usuarie::factory()->create();
        $categorias = Models\Categoria::factory()->count(3)->create();

        $publicacion = Models\Publicacion::factory()->usuarieExistente()->create();
        $publicacion->categorias()->attach($categorias->pluck('id')->toArray());


        $responsePublicacionNoExiste = $this->actingAs($usuarie)->delete("/publicaciones/" . ($publicacion->id + 1));
        $responsePublicacionNoExiste->assertStatus(303); // 303: See Other
        $responsePublicacionNoExiste->assertRedirect("/publicaciones");
        $responsePublicacionNoExiste->assertSessionHasErrors([
            "publicacion" => "Ocurrió un error al intentar eliminar la publicación."
        ]);
    }
}
