<?php

namespace Tests\Feature\Usuaries;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\Usuarie;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class RutaRegistrarseTest extends TestCase
{
    use DatabaseTransactions;

    public function test_DeberiaCrearUsuarieYRedirigirAPublicacionesLogueado_CuandoCredencialesValidasConImagen()
    {
        $archivo = UploadedFile::fake()->image("juan.jpg", 150, 150);

        $response = $this->from("/registrarse")->post('/registrarse', [
            "nombre" => "Juan",
            "contrasena" => "contra",
            "contrasena_confirmation" => "contra",
            "imagen" => $archivo
        ]);

        $usuarieCreado = Usuarie::where("nombre", "Juan")->get()->first();

        $response->assertStatus(302); // 302: Found
        $response->assertRedirect("/publicaciones");
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($usuarieCreado);
        assertTrue(Storage::disk("public")->exists("usuaries/" . $archivo->hashName()));
        assertEquals($usuarieCreado->imagen, $archivo->hashName());

        // eliminamos la foto luego de todas las validaciones
        Storage::disk("public")->delete("usuaries/" . $archivo->hashName());
    }

    public function test_DeberiaCrearUsuarieYRedirigirAPublicacionesLogueado_CuandoCredencialesValidasSinImagen()
    {
        $response = $this->from("/registrarse")->post('/registrarse', [
            "nombre" => "Juan",
            "contrasena" => "contra",
            "contrasena_confirmation" => "contra"
        ]);

        $usuarieCreado = Usuarie::where("nombre", "Juan")->get()->first();

        $response->assertStatus(302); // 302: Found
        $response->assertRedirect("/publicaciones");
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($usuarieCreado);
    }

    public function test_DeberiaVolverConError_CuandoNombreVacio()
    {
        $response = $this->from("/registrarse")->post('/registrarse', [
            "nombre" => "",
            "contrasena" => "contra",
            "contrasena_confirmation" => "contra"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/registrarse");
        $response->assertSessionHasErrors([
            "nombre" => "El campo nombre de usuarie es obligatorio."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConError_CuandoNombreMayorLimiteCaracteres()
    {
        $response = $this->from("/registrarse")->post('/registrarse', [
            "nombre" => "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890",
            "contrasena" => "contra",
            "contrasena_confirmation" => "contra"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/registrarse");
        $response->assertSessionHasErrors([
            "nombre" => "El nombre de usuarie no puede ser mayor de 100 caracteres."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConError_CuandoContrasenaVacia()
    {
        $response = $this->from("/registrarse")->post('/registrarse', [
            "nombre" => "Juan",
            "contrasena" => "",
            "contrasena_confirmation" => ""
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/registrarse");
        $response->assertSessionHasErrors([
            "contrasena" => "El campo contraseña es obligatorio."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConError_CuandoContrasenaMenorLimiteCaracteres()
    {
        $response = $this->from("/registrarse")->post('/registrarse', [
            "nombre" => "Juan",
            "contrasena" => "12",
            "contrasena_confirmation" => "12"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/registrarse");
        $response->assertSessionHasErrors([
            "contrasena" => "El campo contraseña debe tener entre 3 y 40 caracteres."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConError_CuandoContrasenaMayorLimiteCaracteres()
    {
        $response = $this->from("/registrarse")->post('/registrarse', [
            "nombre" => "Juan",
            "contrasena" => "12345678901234567890123456789012345678901234567890",
            "contrasena_confirmation" => "12345678901234567890123456789012345678901234567890"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/registrarse");
        $response->assertSessionHasErrors([
            "contrasena" => "El campo contraseña debe tener entre 3 y 40 caracteres."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConError_CuandoContrasenaNoConfirmada()
    {
        $response = $this->from("/registrarse")->post('/registrarse', [
            "nombre" => "Juan",
            "contrasena" => "contra",
            "contrasena_confirmation" => "otra"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/registrarse");
        $response->assertSessionHasErrors([
            "contrasena" => "Las contraseñas no coinciden."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConDosErrores_CuandoNombreVacioYContrasenaVacia()
    {
        $response = $this->from("/registrarse")->post('/registrarse', [
            "nombre" => "",
            "contrasena" => "",
            "contrasena_confirmation" => ""
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/registrarse");
        $response->assertSessionHasErrors([
            "nombre" => "El campo nombre de usuarie es obligatorio.",
            "contrasena" => "El campo contraseña es obligatorio."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConError_CuandoArchivoNoEsImagen()
    {
        $archivo = UploadedFile::fake()->create("juan.txt");

        $response = $this->from("/registrarse")->post('/registrarse', [
            "nombre" => "Juan",
            "contrasena" => "contra",
            "contrasena_confirmation" => "contra",
            "imagen" => $archivo
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/registrarse");
        $response->assertSessionHasErrors([
            "imagen" => "El archivo subido debe ser una imagen.",
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConError_CuandoImagenMenorLimiteDimensiones()
    {
        $archivo = UploadedFile::fake()->image("juan.jpg", 50, 50);

        $response = $this->from("/registrarse")->post('/registrarse', [
            "nombre" => "Juan",
            "contrasena" => "contra",
            "contrasena_confirmation" => "contra",
            "imagen" => $archivo
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/registrarse");
        $response->assertSessionHasErrors([
            "imagen" => "La imagen subida es debe medir entre 100x100 y 5000x5000 pixeles.",
        ]);
        $this->assertGuest();
    }
}
