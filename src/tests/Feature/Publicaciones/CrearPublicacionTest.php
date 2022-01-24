<?php

namespace Tests\Feature\Publicaciones;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

use Tests\TestCase;

use App\Models;

class CrearPublicacionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_DeberiaCrearPublicacionYRedirigir_CuandoDatosValidosConImagen()
    {
        $usuarie = Models\Usuarie::factory()->create();

        $categorias = Models\Categoria::factory()->count(3)->create();

        $archivo = UploadedFile::fake()->image("juan.jpg", 150, 150);

        $response = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => $this->faker->paragraphs(3, true),
                "imagen" => $archivo
            ]);

        $publicacionCreada = Models\Publicacion::First();

        $response->assertStatus(302); // 302: Found
        $response->assertRedirect("/publicaciones/" . $publicacionCreada->id);
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($usuarie);

        $this->followRedirects($response)->assertSeeText($publicacionCreada->titulo);
        $this->followRedirects($response)->assertSeeText($categorias->pluck("nombre")->toArray());
        $this->followRedirects($response)->assertSeeText("por " . $usuarie->nombre);
        $this->followRedirects($response)->assertSeeText($publicacionCreada->fecha_creacion->format("d/m/Y"));
        $this->followRedirects($response)->assertSeeText("0 me gusta");
        $this->followRedirects($response)->assertSeeText("0 comentarios");
        $this->followRedirects($response)->assertSeeText(explode("\n", $publicacionCreada->cuerpo));

        assertTrue(Storage::disk("public")->exists("publicaciones/" . $archivo->hashName()));
        assertEquals($publicacionCreada->portada, $archivo->hashName());

        // eliminamos la foto luego de todas las validaciones
        Storage::disk("public")->delete("publicaciones/" . $archivo->hashName());
    }

    public function test_DeberiaCrearPublicacionYRedirigir_CuandoDatosValidosSinImagen()
    {
        $usuarie = Models\Usuarie::factory()->create();

        $categorias = Models\Categoria::factory()->count(3)->create();

        $response = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $publicacionCreada = Models\Publicacion::First();

        $response->assertStatus(302); // 302: Found
        $response->assertRedirect("/publicaciones/" . $publicacionCreada->id);
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($usuarie);

        $this->followRedirects($response)->assertSeeText($publicacionCreada->titulo);
        $this->followRedirects($response)->assertSeeText($categorias->pluck("nombre")->toArray());
        $this->followRedirects($response)->assertSeeText("por " . $usuarie->nombre);
        $this->followRedirects($response)->assertSeeText($publicacionCreada->fecha_creacion->format("d/m/Y"));
        $this->followRedirects($response)->assertSeeText("0 me gusta");
        $this->followRedirects($response)->assertSeeText("0 comentarios");
        $this->followRedirects($response)->assertSeeText(explode("\n", $publicacionCreada->cuerpo));
    }

    public function test_DeberiaNegarPublicacion_CuandoTituloInvalido()
    {
        $usuarie = Models\Usuarie::factory()->create();

        $categorias = Models\Categoria::factory()->count(3)->create();

        //# titulo vacio
        $responseTituloVacio = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => "",
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseTituloVacio->assertStatus(303); // 303: See Other
        $responseTituloVacio->assertRedirect("/escribir");
        $responseTituloVacio->assertSessionHasErrors([
            "titulo" => "El campo titulo es obligatorio."
        ]);

        //# titulo corto
        $responseTituloCorto = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => "12",
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseTituloCorto->assertStatus(303); // 303: See Other
        $responseTituloCorto->assertRedirect("/escribir");
        $responseTituloCorto->assertSessionHasErrors([
            "titulo" => "El campo titulo debe tener entre 3 y 110 caracteres."
        ]);

        //# titulo largo
        $responseTituloLargo = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->words(200, true),
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseTituloLargo->assertStatus(303); // 303: See Other
        $responseTituloLargo->assertRedirect("/escribir");
        $responseTituloLargo->assertSessionHasErrors([
            "titulo" => "El campo titulo debe tener entre 3 y 110 caracteres."
        ]);
    }

    public function test_DeberiaNegarPublicacion_CuandoCategoriasInvalidas()
    {
        $usuarie = Models\Usuarie::factory()->create();

        //# categorias vacias
        $responseTituloVacio = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->sentence(8),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseTituloVacio->assertStatus(303); // 303: See Other
        $responseTituloVacio->assertRedirect("/escribir");
        $responseTituloVacio->assertSessionHasErrors([
            "categorias" => "Es necesario seleccionar al menos una categoría."
        ]);
    }

    public function test_DeberiaNegarPublicacion_CuandoNoAutenticade()
    {
        $categorias = Models\Categoria::factory()->count(3)->create();

        $responseNoAutenticado = $this
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseNoAutenticado->assertStatus(303); // 303: See Other
        $responseNoAutenticado->assertRedirect("/escribir");
        $responseNoAutenticado->assertSessionHasErrors([
            "autenticacion" => "Para escribir una publicación es necesario iniciar sesión."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaNegarPublicacion_CuandoImagenInvalida()
    {
        $usuarie = Models\Usuarie::factory()->create();

        $categorias = Models\Categoria::factory()->count(3)->create();

        //# archivo no es imagen
        $archivoNoImagen = UploadedFile::fake()->create("juan.txt");

        $responseArchivoNoEsImagen = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => $this->faker->paragraphs(3, true),
                "imagen" => $archivoNoImagen
            ]);

        $responseArchivoNoEsImagen->assertStatus(303); // 303: See Other
        $responseArchivoNoEsImagen->assertRedirect("/escribir");
        $responseArchivoNoEsImagen->assertSessionHasErrors([
            "imagen" => "El archivo subido debe ser una imagen."
        ]);

        //# imagen es demasiado chica
        $imagenChica = UploadedFile::fake()->image("juan.jpg", 50, 50);

        $responseArchivoNoEsImagen = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => $this->faker->paragraphs(3, true),
                "imagen" => $imagenChica
            ]);

        $responseArchivoNoEsImagen->assertStatus(303); // 303: See Other
        $responseArchivoNoEsImagen->assertRedirect("/escribir");
        $responseArchivoNoEsImagen->assertSessionHasErrors([
            "imagen" => "La imagen subida debe medir entre 100x100 y 5000x5000 pixeles."
        ]);

        // eliminamos la foto luego de todas las validaciones
        Storage::disk("public")->delete("publicaciones/" . $archivoNoImagen->hashName());
        Storage::disk("public")->delete("publicaciones/" . $imagenChica->hashName());
    }

    public function test_DeberiaNegarPublicacion_CuandoCuerpoInvalido()
    {
        $usuarie = Models\Usuarie::factory()->create();

        $categorias = Models\Categoria::factory()->count(3)->create();

        //# cuerpo vacio
        $responseCuerpoVacio = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => ""
            ]);

        $responseCuerpoVacio->assertStatus(303); // 303: See Other
        $responseCuerpoVacio->assertRedirect("/escribir");
        $responseCuerpoVacio->assertSessionHasErrors([
            "cuerpo" => "El campo cuerpo es obligatorio."
        ]);

        //# cuerpo corto
        $responseCuerpoCorto = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => "12"
            ]);

        $responseCuerpoCorto->assertStatus(303); // 303: See Other
        $responseCuerpoCorto->assertRedirect("/escribir");
        $responseCuerpoCorto->assertSessionHasErrors([
            "cuerpo" => "El campo cuerpo debe tener entre 3 y 2000 caracteres."
        ]);

        //# cuerpo largo
        $responseCuerpoLargo = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => $this->faker->words(2000, true)
            ]);

        $responseCuerpoLargo->assertStatus(303); // 303: See Other
        $responseCuerpoLargo->assertRedirect("/escribir");
        $responseCuerpoLargo->assertSessionHasErrors([
            "cuerpo" => "El campo cuerpo debe tener entre 3 y 2000 caracteres."
        ]);
    }
}
