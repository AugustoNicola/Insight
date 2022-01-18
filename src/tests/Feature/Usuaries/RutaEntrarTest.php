<?php

namespace Tests\Feature\Usuaries;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use Illuminate\Support\Facades\Hash;
use App\Models\Usuarie;

class RutaEntrarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_DeberiaIniciarSesionYRedirigir_CuandoCredencialesCorrectas()
    {
        $usuarie = Usuarie::create([
            "nombre" => "Juan",
            "contrasena" => Hash::make("contra")
        ]);

        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "Juan",
            "contrasena" => "contra"
        ]);

        $response->assertStatus(302); // 302: Found
        $response->assertRedirect("/");
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($usuarie);
    }

    public function test_DeberiaNegarInicioSesionYVolver_CuandoNombreYContrasenaIncorrectas()
    {
        Usuarie::create([
            "nombre" => "Juan",
            "contrasena" => Hash::make("contra")
        ]);

        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "nombre_falso",
            "contrasena" => "contrasena_falsa"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "autenticacion" => "Las credenciales ingresadas no son correctas. Por favor verificá que escribiste correctamente los datos."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaNegarInicioSesionYVolver_CuandoNombreIncorrecto()
    {
        Usuarie::create([
            "nombre" => "Juan",
            "contrasena" => Hash::make("contra")
        ]);

        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "nombre_falso",
            "contrasena" => "contra"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "autenticacion" => "Las credenciales ingresadas no son correctas. Por favor verificá que escribiste correctamente los datos."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaNegarInicioSesionYVolver_CuandoContrasenaIncorrecta()
    {
        Usuarie::create([
            "nombre" => "Juan",
            "contrasena" => Hash::make("contra")
        ]);

        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "Juan",
            "contrasena" => "contrasena_falsa"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "autenticacion" => "Las credenciales ingresadas no son correctas. Por favor verificá que escribiste correctamente los datos."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConErrorValidacion_CuandoNombreVacio()
    {
        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "",
            "contrasena" => "contrasena"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "nombre" => "El campo nombre de usuarie es obligatorio."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConErrorValidacion_CuandoNombreMayorLimiteCaracteres()
    {
        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890",
            "contrasena" => "contrasena"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "nombre" => "El nombre de usuarie no puede ser mayor de 100 caracteres."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConErrorValidacion_CuandoContrasenaVacia()
    {
        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "Juan",
            "contrasena" => ""
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "contrasena" => "El campo contraseña es obligatorio."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConErrorValidacion_CuandoContrasenaMenorLimiteCaracteres()
    {
        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "Juan",
            "contrasena" => "12"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "contrasena" => "El campo contraseña debe tener entre 3 y 40 caracteres."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConErrorValidacion_CuandoContrasenaMayorLimiteCaracteres()
    {
        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "Juan",
            "contrasena" => "12345678901234567890123456789012345678901234567890"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "contrasena" => "El campo contraseña debe tener entre 3 y 40 caracteres."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConDosErroresValidacion_CuandoNombreVacioYContrasenaVacia()
    {
        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "",
            "contrasena" => ""
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "nombre" => "El campo nombre de usuarie es obligatorio.",
            "contrasena" => "El campo contraseña es obligatorio."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConDosErroresValidacion_CuandoNombreVacioYContrasenaMenorLimiteCaracteres()
    {
        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "",
            "contrasena" => "12"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "nombre" => "El campo nombre de usuarie es obligatorio.",
            "contrasena" => "El campo contraseña debe tener entre 3 y 40 caracteres."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConDosErroresValidacion_CuandoNombreVacioYContrasenaMayorLimiteCaracteres()
    {
        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "",
            "contrasena" => "12345678901234567890123456789012345678901234567890"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "nombre" => "El campo nombre de usuarie es obligatorio.",
            "contrasena" => "El campo contraseña debe tener entre 3 y 40 caracteres."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConDosErroresValidacion_CuandoNombreMayorLimiteCaracteresYContrasenaVacia()
    {
        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890",
            "contrasena" => ""
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "nombre" => "El nombre de usuarie no puede ser mayor de 100 caracteres.",
            "contrasena" => "El campo contraseña es obligatorio."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConDosErroresValidacion_CuandoNombreMayorLimiteCaracteresYContrasenaMenorLimiteCaracteres()
    {
        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890",
            "contrasena" => "12"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "nombre" => "El nombre de usuarie no puede ser mayor de 100 caracteres.",
            "contrasena" => "El campo contraseña debe tener entre 3 y 40 caracteres."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaVolverConDosErroresValidacion_CuandoNombreMayorLimiteCaracteresYContrasenaMayorLimiteCaracteres()
    {
        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890",
            "contrasena" => "12345678901234567890123456789012345678901234567890"
        ]);

        $response->assertStatus(303); // 303: See Other
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "nombre" => "El nombre de usuarie no puede ser mayor de 100 caracteres.",
            "contrasena" => "El campo contraseña debe tener entre 3 y 40 caracteres."
        ]);
        $this->assertGuest();
    }
}
