<?php

namespace Tests\Feature\Usuaries;

use Tests\TestCase;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

use App\Models;

class EditarCuentaTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_DeberiaEditarUsuarieYRedirigir_CuandoDatosValidosConImagen()
    {
        $usuarie = Models\Usuarie::factory()->create();

        $nuevoNombre = $this->faker->name();
        $nuevaContrasena = $this->faker->password(4, 10);
        $archivo = UploadedFile::fake()->image("juan.jpg", 150, 150);

        $response = $this->actingAs($usuarie)->from("/perfil/editar")->put("/perfil", [
            "nombre" => $nuevoNombre,
            "contrasena" => $nuevaContrasena,
            "contrasena_confirmation" => $nuevaContrasena,
            "imagen" => $archivo
        ]);

        $nueveUsuarie = Models\Usuarie::first();

        $response->assertStatus(302); // 302: Found
        $response->assertRedirect("/perfil");
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($nueveUsuarie);

        assertEquals($nuevoNombre, $nueveUsuarie->nombre);
        assertTrue(Hash::check($nuevaContrasena, $nueveUsuarie->contrasena));
        assertEquals($archivo->hashName(), $nueveUsuarie->imagen);
        assertTrue(Storage::disk("public")->exists("usuaries/" . $archivo->hashName()));
    }

    public function test_DeberiaEditarUsuarieYRedirigir_CuandoDatosValidosSinImagen()
    {
        $usuarie = Models\Usuarie::factory()->create();

        $nuevoNombre = $this->faker->name();
        $nuevaContrasena = $this->faker->password(4, 10);

        $response = $this->actingAs($usuarie)->from("/perfil/editar")->put("/perfil", [
            "nombre" => $nuevoNombre,
            "contrasena" => $nuevaContrasena,
            "contrasena_confirmation" => $nuevaContrasena
        ]);

        $nueveUsuarie = Models\Usuarie::first();

        $response->assertStatus(302); // 302: Found
        $response->assertRedirect("/perfil");
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($nueveUsuarie);

        assertEquals($nuevoNombre, $nueveUsuarie->nombre);
        assertTrue(Hash::check($nuevaContrasena, $nueveUsuarie->contrasena));
    }

    public function test_DeberiaNegarEdicion_CuandoNombreInvalido()
    {
        $usuarie = Models\Usuarie::factory()->create();

        //# nombre vacio
        $responseNombreVacio = $this->actingAs($usuarie)->from("/perfil/editar")->put("/perfil", [
            "nombre" => ""
        ]);

        $responseNombreVacio->assertStatus(303); // 303: See Other
        $responseNombreVacio->assertRedirect("/perfil/editar");
        $responseNombreVacio->assertSessionHasErrors([
            "nombre" => "El campo nombre de usuarie es obligatorio."
        ]);

        //# nombre corto
        $responseNombreCorto = $this->actingAs($usuarie)->from("/perfil/editar")->put("/perfil", [
            "nombre" => "12"
        ]);

        $responseNombreCorto->assertStatus(303); // 303: See Other
        $responseNombreCorto->assertRedirect("/perfil/editar");
        $responseNombreCorto->assertSessionHasErrors([
            "nombre" => "El campo nombre de usuarie debe tener entre 3 y 100 caracteres."
        ]);

        //# nombre largo
        $responseNombreLargo = $this->actingAs($usuarie)->from("/perfil/editar")->put("/perfil", [
            "nombre" => $this->faker->words(100, true)
        ]);

        $responseNombreLargo->assertStatus(303); // 303: See Other
        $responseNombreLargo->assertRedirect("/perfil/editar");
        $responseNombreLargo->assertSessionHasErrors([
            "nombre" => "El campo nombre de usuarie debe tener entre 3 y 100 caracteres."
        ]);
    }

    public function test_DeberiaNegarEdicion_CuandoContrasenaInvalida()
    {
        $usuarie = Models\Usuarie::factory()->create();
        $nuevoNombre = $this->faker->name();

        //# contrasena corta
        $responseContrasenaCorta = $this->actingAs($usuarie)->from("/perfil/editar")->put("/perfil", [
            "nombre" => $nuevoNombre,
            "contrasena" => "12",
            "contrasena_confirmation" => "12"
        ]);

        $responseContrasenaCorta->assertStatus(303); // 303: See Other
        $responseContrasenaCorta->assertRedirect("/perfil/editar");
        $responseContrasenaCorta->assertSessionHasErrors([
            "contrasena" => "El campo contraseña debe tener entre 3 y 40 caracteres."
        ]);

        //# contrasena larga
        $nuevaContrasenaLarga = $this->faker->words(40, true);
        $responseContrasenaLarga = $this->actingAs($usuarie)->from("/perfil/editar")->put("/perfil", [
            "nombre" => $nuevoNombre,
            "contrasena" => $nuevaContrasenaLarga,
            "contrasena_confirmation" => $nuevaContrasenaLarga
        ]);

        $responseContrasenaLarga->assertStatus(303); // 303: See Other
        $responseContrasenaLarga->assertRedirect("/perfil/editar");
        $responseContrasenaLarga->assertSessionHasErrors([
            "contrasena" => "El campo contraseña debe tener entre 3 y 40 caracteres."
        ]);

        //# contrasenas no coinciden
        $responseContrasenasNoCoinciden = $this->actingAs($usuarie)->from("/perfil/editar")->put("/perfil", [
            "nombre" => $nuevoNombre,
            "contrasena" => $this->faker->password(4, 10),
            "contrasena_confirmation" => "123"
        ]);

        $responseContrasenasNoCoinciden->assertStatus(303); // 303: See Other
        $responseContrasenasNoCoinciden->assertRedirect("/perfil/editar");
        $responseContrasenasNoCoinciden->assertSessionHasErrors([
            "contrasena" => "Las contraseñas no coinciden."
        ]);
    }

    public function test_DeberiaNegarEdicion_CuandoImagenInvalida()
    {
        $usuarie = Models\Usuarie::factory()->create();
        $nuevoNombre = $this->faker->name();

        //# archivo no es imagen
        $archivoNoImagen = UploadedFile::fake()->create("juan.txt");
        $responseArchivoNoEsImagen = $this->actingAs($usuarie)->from("/perfil/editar")->put("/perfil", [
            "nombre" => $nuevoNombre,
            "imagen" => $archivoNoImagen
        ]);

        $responseArchivoNoEsImagen->assertStatus(303); // 303: See Other
        $responseArchivoNoEsImagen->assertRedirect("/perfil/editar");
        $responseArchivoNoEsImagen->assertSessionHasErrors([
            "imagen" => "El archivo subido debe ser una imagen."
        ]);

        //# imagen es demasiado chica
        $imagenChica = UploadedFile::fake()->image("juan.jpg", 50, 50);

        $responseImagenChica = $this->actingAs($usuarie)->from("/perfil/editar")->put("/perfil", [
            "nombre" => $nuevoNombre,
            "imagen" => $imagenChica
        ]);

        $responseImagenChica->assertStatus(303); // 303: See Other
        $responseImagenChica->assertRedirect("/perfil/editar");
        $responseImagenChica->assertSessionHasErrors([
            "imagen" => "La imagen subida debe medir entre 100x100 y 5000x5000 pixeles."
        ]);

        // eliminamos los archivos luego de todas las validaciones
        Storage::disk("public")->delete("publicaciones/" . $archivoNoImagen->hashName());
        Storage::disk("public")->delete("publicaciones/" . $imagenChica->hashName());
    }

    public function test_DeberiaNegarEdicion_CuandoNoAutenticade()
    {
        Models\Usuarie::factory()->create();

        $responseNoAutenticade = $this->from("/perfil/editar")->put("/perfil", [
            "nombre" => $this->faker->name()
        ]);

        $responseNoAutenticade->assertStatus(303); // 303: See Other
        $responseNoAutenticade->assertRedirect("/entrar");
        $responseNoAutenticade->assertSessionHasErrors([
            "autenticacion" => "Para poder editar tu cuenta primero es necesario iniciar sesión."
        ]);
    }
}
