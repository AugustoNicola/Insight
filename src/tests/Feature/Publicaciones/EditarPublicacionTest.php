<?php

namespace Tests\Feature\Publicaciones;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

use Tests\TestCase;

use App\Models;

class EditarPublicacionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_DeberiaEditarPublicacionYRedirigir_CuandoDatosValidosConImagen()
    {
        $usuarie = Models\Usuarie::factory()->create();

        $categorias = Models\Categoria::factory()->count(3)->create();

        $publicacionPrevia = Models\Publicacion::factory()->usuarieExistente()->create();
        $publicacionPrevia->categorias()->attach([$categorias[0]->id, $categorias[1]->id]); // la publicacion tiene las dos primeras categorias

        $nuevoTitulo = $this->faker->sentence(8);
        $nuevasCategorias = [$categorias[1]->id, $categorias[2]->id];
        $nuevoCuerpo = $this->faker->paragraphs(3, true);
        $nuevoArchivo = UploadedFile::fake()->image("juan.jpg", 150, 150);

        $response = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => $nuevoTitulo,
                "categorias" => $nuevasCategorias,
                "cuerpo" => $nuevoCuerpo,
                "imagen" => $nuevoArchivo
            ]);

        $nuevaPublicacion = Models\Publicacion::first();

        $response->assertStatus(302); // 302: Found
        $response->assertRedirect("/publicaciones/" . $publicacionPrevia->id);
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($usuarie);

        $this->followRedirects($response)->assertSeeText($nuevoTitulo);
        $this->followRedirects($response)->assertSeeText([$categorias[1]->nombre, $categorias[2]->nombre]);
        $this->followRedirects($response)->assertSeeText("por " . $usuarie->nombre);
        $this->followRedirects($response)->assertSeeText(array_filter(explode("\n", $nuevoCuerpo)), function ($parrafo) {
            return $parrafo != "";
        });

        $this->followRedirects($response)->assertDontSeeText($publicacionPrevia->titulo);
        $this->followRedirects($response)->assertDontSeeText(array_filter(explode("\n", $publicacionPrevia->cuerpo)), function ($parrafo) {
            return $parrafo != "";
        });

        assertTrue(Storage::disk("public")->exists("publicaciones/" . $nuevoArchivo->hashName()));
        assertEquals($nuevaPublicacion->portada, $nuevoArchivo->hashName());

        // eliminamos la foto luego de todas las validaciones
        Storage::disk("public")->delete("publicaciones/" . $nuevoArchivo->hashName());
    }

    public function test_DeberiaEditarPublicacionYRedirigir_CuandoDatosValidosSinImagen()
    {
        $usuarie = Models\Usuarie::factory()->create();

        $categorias = Models\Categoria::factory()->count(3)->create();

        $publicacionPrevia = Models\Publicacion::factory()->usuarieExistente()->create();
        $publicacionPrevia->categorias()->attach([$categorias[0]->id, $categorias[1]->id]); // la publicacion tiene las dos primeras categorias

        $nuevoTitulo = $this->faker->sentence(8);
        $nuevasCategorias = [$categorias[1]->id, $categorias[2]->id];
        $nuevoCuerpo = $this->faker->paragraphs(3, true);

        $response = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => $nuevoTitulo,
                "categorias" => $nuevasCategorias,
                "cuerpo" => $nuevoCuerpo
            ]);

        $nuevaPublicacion = Models\Publicacion::first();

        $response->assertStatus(302); // 302: Found
        $response->assertRedirect("/publicaciones/" . $publicacionPrevia->id);
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($usuarie);

        $this->followRedirects($response)->assertSeeText($nuevoTitulo);
        $this->followRedirects($response)->assertSeeText([$categorias[1]->nombre, $categorias[2]->nombre]);
        $this->followRedirects($response)->assertSeeText("por " . $usuarie->nombre);
        $this->followRedirects($response)->assertSeeText(array_filter(explode("\n", $nuevoCuerpo)), function ($parrafo) {
            return $parrafo != "";
        });

        $this->followRedirects($response)->assertDontSeeText($publicacionPrevia->titulo);
        $this->followRedirects($response)->assertDontSeeText(array_filter(explode("\n", $publicacionPrevia->cuerpo)), function ($parrafo) {
            return $parrafo != "";
        });
    }

    public function test_DeberiaNegarEdicion_CuandoTituloInvalido()
    {
        $usuarie = Models\Usuarie::factory()->create();
        $categorias = Models\Categoria::factory()->count(3)->create();
        $publicacionPrevia = Models\Publicacion::factory()->usuarieExistente()->create();

        //# titulo vacio
        $responseCuerpoVacio = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => "",
                "categorias" => $categorias->pluck("id")->toArray(),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseCuerpoVacio->assertStatus(303); // 303: See Other
        $responseCuerpoVacio->assertRedirect("/publicaciones/" . $publicacionPrevia->id .  "/editar");
        $responseCuerpoVacio->assertSessionHasErrors([
            "titulo" => "El campo titulo es obligatorio."
        ]);

        //# titulo corto
        $responseTituloCorto = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => "12",
                "categorias" => $categorias->pluck("id")->toArray(),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseTituloCorto->assertStatus(303); // 303: See Other
        $responseTituloCorto->assertRedirect("/publicaciones/" . $publicacionPrevia->id .  "/editar");
        $responseTituloCorto->assertSessionHasErrors([
            "titulo" => "El campo titulo debe tener entre 3 y 110 caracteres."
        ]);

        //# titulo largo
        $responseTituloLargo = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => $this->faker->words(200, true),
                "categorias" => $categorias->pluck("id")->toArray(),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseTituloLargo->assertStatus(303); // 303: See Other
        $responseTituloLargo->assertRedirect("/publicaciones/" . $publicacionPrevia->id .  "/editar");
        $responseTituloLargo->assertSessionHasErrors([
            "titulo" => "El campo titulo debe tener entre 3 y 110 caracteres."
        ]);
    }

    public function test_DeberiaNegarEdicion_CuandoCategoriasInvalidas()
    {
        $usuarie = Models\Usuarie::factory()->create();
        $publicacionPrevia = Models\Publicacion::factory()->usuarieExistente()->create();

        //# categorias vacias
        $responseCategoriasVacias = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "categorias" => []
            ]);

        $responseCategoriasVacias->assertStatus(303); // 303: See Other
        $responseCategoriasVacias->assertRedirect("/publicaciones/" . $publicacionPrevia->id .  "/editar");
        $responseCategoriasVacias->assertSessionHasErrors([
            "categorias" => "Es necesario seleccionar al menos una categoría."
        ]);
    }

    public function test_DeberiaNegarEdicion_CuandoNoAutenticade()
    {
        $usuarie = Models\Usuarie::factory()->create();
        $categorias = Models\Categoria::factory()->count(3)->create();
        $publicacionPrevia = Models\Publicacion::factory()->usuarieExistente()->create();
        $otreUsuarie = Models\Usuarie::factory()->create();

        //# no autenticade
        $responseNoAutenticade = $this
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id")->toArray(),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseNoAutenticade->assertStatus(303); // 303: See Other
        $responseNoAutenticade->assertRedirect("/publicaciones/" . $publicacionPrevia->id .  "/editar");
        $responseNoAutenticade->assertSessionHasErrors([
            "autenticacion" => "No tiene permisos necesarios para editar esta publicación."
        ]);

        //# autenticade como usuarie que no es autore
        $responseOtreUsuarie = $this->actingAs($otreUsuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id")->toArray(),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseOtreUsuarie->assertStatus(303); // 303: See Other
        $responseOtreUsuarie->assertRedirect("/publicaciones/" . $publicacionPrevia->id .  "/editar");
        $responseOtreUsuarie->assertSessionHasErrors([
            "autenticacion" => "No tiene permisos necesarios para editar esta publicación."
        ]);
    }

    public function test_DeberiaNegarEdicion_CuandoImagenInvalida()
    {
        $usuarie = Models\Usuarie::factory()->create();
        $categorias = Models\Categoria::factory()->count(3)->create();
        $publicacionPrevia = Models\Publicacion::factory()->usuarieExistente()->create();

        //# archivo no es imagen
        $archivoNoImagen = UploadedFile::fake()->create("juan.txt");

        $responseArchivoNoEsImagen = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id")->toArray(),
                "cuerpo" => $this->faker->paragraphs(3, true),
                "imagen" => $archivoNoImagen
            ]);

        $responseArchivoNoEsImagen->assertStatus(303); // 303: See Other
        $responseArchivoNoEsImagen->assertRedirect("/publicaciones/" . $publicacionPrevia->id .  "/editar");
        $responseArchivoNoEsImagen->assertSessionHasErrors([
            "imagen" => "El archivo subido debe ser una imagen."
        ]);

        //# imagen es demasiado chica
        $imagenChica = UploadedFile::fake()->image("juan.jpg", 50, 50);

        $responseArchivoNoEsImagen = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id")->toArray(),
                "cuerpo" => $this->faker->paragraphs(3, true),
                "imagen" => $imagenChica
            ]);

        $responseArchivoNoEsImagen->assertStatus(303); // 303: See Other
        $responseArchivoNoEsImagen->assertRedirect("/publicaciones/" . $publicacionPrevia->id .  "/editar");
        $responseArchivoNoEsImagen->assertSessionHasErrors([
            "imagen" => "La imagen subida debe medir entre 100x100 y 5000x5000 pixeles."
        ]);

        // eliminamos la foto luego de todas las validaciones
        Storage::disk("public")->delete("publicaciones/" . $archivoNoImagen->hashName());
        Storage::disk("public")->delete("publicaciones/" . $imagenChica->hashName());
    }

    public function test_DeberiaNegarEdicion_CuandoCuerpoInvalido()
    {
        $usuarie = Models\Usuarie::factory()->create();
        $categorias = Models\Categoria::factory()->count(3)->create();
        $publicacionPrevia = Models\Publicacion::factory()->usuarieExistente()->create();

        //# cuerpo vacio
        $responseCuerpoVacio = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id")->toArray(),
                "cuerpo" => "",
            ]);

        $responseCuerpoVacio->assertStatus(303); // 303: See Other
        $responseCuerpoVacio->assertRedirect("/publicaciones/" . $publicacionPrevia->id .  "/editar");
        $responseCuerpoVacio->assertSessionHasErrors([
            "cuerpo" => "El campo cuerpo es obligatorio."
        ]);

        //# cuerpo corto
        $responseCuerpoCorto = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id")->toArray(),
                "cuerpo" => "12",
            ]);

        $responseCuerpoCorto->assertStatus(303); // 303: See Other
        $responseCuerpoCorto->assertRedirect("/publicaciones/" . $publicacionPrevia->id .  "/editar");
        $responseCuerpoCorto->assertSessionHasErrors([
            "cuerpo" => "El campo cuerpo debe tener entre 3 y 2000 caracteres."
        ]);

        //# cuerpo largo
        $responseCuerpoLargo = $this->actingAs($usuarie)
            ->from("/publicaciones/" . $publicacionPrevia->id .  "/editar")
            ->put("/publicaciones/" . $publicacionPrevia->id, [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id")->toArray(),
                "cuerpo" => $this->faker->words(2000, true)
            ]);

        $responseCuerpoLargo->assertStatus(303); // 303: See Other
        $responseCuerpoLargo->assertRedirect("/publicaciones/" . $publicacionPrevia->id .  "/editar");
        $responseCuerpoLargo->assertSessionHasErrors([
            "cuerpo" => "El campo cuerpo debe tener entre 3 y 2000 caracteres."
        ]);
    }
}
