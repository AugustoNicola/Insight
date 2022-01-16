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
        // por alguna razon el factory devuelve una clase que el actingAs no puede usar, asi que creamos a le usuarie desde la clase y guardamos a la BBDD
        $usuarie = new Models\Usuarie([
            "nombre" => $this->faker->name(),
            "contrasena" => Hash::make($this->faker->word())
        ]);

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

        $response->assertSeeText($publicacionCreada->titulo);
        $response->assertSeeText($categorias->pluck("id"));
        $response->assertSeeText("Por " . $usuarie->nombre);
        $response->assertSeeText($publicacionCreada->fecha_creacion->format("d/m/Y"));
        $response->assertSeeText("0 me gusta");
        $response->assertSeeText("0 comentarios");
        $response->assertSeeText(explode("\n", $publicacionCreada->cuerpo));

        assertTrue(Storage::disk("public")->exists("publicaciones/" . $archivo->hashName()));
        assertEquals($publicacionCreada->portada, $archivo->hashName());

        // eliminamos la foto luego de todas las validaciones
        Storage::disk("public")->delete("publicaciones/" . $archivo->hashName());
    }

    public function test_DeberiaCrearPublicacionYRedirigir_CuandoDatosValidosSinImagen()
    {
        // por alguna razon el factory devuelve una clase que el actingAs no puede usar, asi que creamos a le usuarie desde la clase y guardamos a la BBDD
        $usuarie = new Models\Usuarie([
            "nombre" => $this->faker->name(),
            "contrasena" => Hash::make($this->faker->word())
        ]);

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

        $response->assertSeeText($publicacionCreada->titulo);
        $response->assertSeeText($categorias->pluck("id"));
        $response->assertSeeText("Por " . $usuarie->nombre);
        $response->assertSeeText($publicacionCreada->fecha_creacion->format("d/m/Y"));
        $response->assertSeeText("0 me gusta");
        $response->assertSeeText("0 comentarios");
        $response->assertSeeText(explode("\n", $publicacionCreada->cuerpo));
    }

    public function test_DeberiaNegarPublicacion_CuandoTituloInvalido()
    {
        // por alguna razon el factory devuelve una clase que el actingAs no puede usar, asi que creamos a le usuarie desde la clase y guardamos a la BBDD
        $usuarie = new Models\Usuarie([
            "nombre" => $this->faker->name(),
            "contrasena" => Hash::make($this->faker->word())
        ]);

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
        $responseTituloVacio->assertRedirect("/entrar");
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
        $responseTituloCorto->assertRedirect("/entrar");
        $responseTituloCorto->assertSessionHasErrors([
            "titulo" => "El campo titulo debe tener entre 3 y 110 caracteres."
        ]);

        //# titulo largo
        $responseTituloLargo = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->word(200),
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseTituloLargo->assertStatus(303); // 303: See Other
        $responseTituloLargo->assertRedirect("/entrar");
        $responseTituloLargo->assertSessionHasErrors([
            "titulo" => "El campo titulo debe tener entre 3 y 110 caracteres."
        ]);
    }

    public function test_DeberiaNegarPublicacion_CuandoCategoriasInvalidas()
    {
        // por alguna razon el factory devuelve una clase que el actingAs no puede usar, asi que creamos a le usuarie desde la clase y guardamos a la BBDD
        $usuarie = new Models\Usuarie([
            "nombre" => $this->faker->name(),
            "contrasena" => Hash::make($this->faker->word())
        ]);

        //# categorias vacias
        $responseTituloVacio = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->sentence(8),
                "cuerpo" => $this->faker->paragraphs(3, true)
            ]);

        $responseTituloVacio->assertStatus(303); // 303: See Other
        $responseTituloVacio->assertRedirect("/entrar");
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
        $responseNoAutenticado->assertRedirect("/entrar");
        $responseNoAutenticado->assertSessionHasErrors([
            "autenticacion" => "Para escribir una publicación es necesario iniciar sesión."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaNegarPublicacion_CuandoImagenInvalida()
    {
        // por alguna razon el factory devuelve una clase que el actingAs no puede usar, asi que creamos a le usuarie desde la clase y guardamos a la BBDD
        $usuarie = new Models\Usuarie([
            "nombre" => $this->faker->name(),
            "contrasena" => Hash::make($this->faker->word())
        ]);

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
        $responseArchivoNoEsImagen->assertRedirect("/entrar");
        $responseArchivoNoEsImagen->assertSessionHasErrors([
            "imagen" => "El archivo subido debe ser una imagen."
        ]);

        //# imagen es demasiado chica
        $imagenChica = UploadedFile::fake()->create("juan.txt");

        $responseArchivoNoEsImagen = $this->actingAs($usuarie)
            ->from("/escribir")
            ->post("/publicaciones", [
                "titulo" => $this->faker->sentence(8),
                "categorias" => $categorias->pluck("id"),
                "cuerpo" => $this->faker->paragraphs(3, true),
                "imagen" => $imagenChica
            ]);

        $responseArchivoNoEsImagen->assertStatus(303); // 303: See Other
        $responseArchivoNoEsImagen->assertRedirect("/entrar");
        $responseArchivoNoEsImagen->assertSessionHasErrors([
            "imagen" => "La imagen subida es debe medir entre 100x100 y 5000x5000 pixeles."
        ]);
    }

    public function test_DeberiaNegarPublicacion_CuandoCuerpoInvalido()
    {
        // por alguna razon el factory devuelve una clase que el actingAs no puede usar, asi que creamos a le usuarie desde la clase y guardamos a la BBDD
        $usuarie = new Models\Usuarie([
            "nombre" => $this->faker->name(),
            "contrasena" => Hash::make($this->faker->word())
        ]);

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
        $responseCuerpoVacio->assertRedirect("/entrar");
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
        $responseCuerpoCorto->assertRedirect("/entrar");
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
        $responseCuerpoLargo->assertRedirect("/entrar");
        $responseCuerpoLargo->assertSessionHasErrors([
            "cuerpo" => "El campo cuerpo debe tener entre 3 y 2000 caracteres."
        ]);
    }
}
