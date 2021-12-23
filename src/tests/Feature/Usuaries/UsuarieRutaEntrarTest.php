<?php

namespace Tests\Feature\Usuaries;

use Illuminate\Foundation\Testing\DatabaseTransactions;
//use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\Hash;
use App\Models\Usuarie;

class UsuarieRutaEntrarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_DeberiaIniciarSesion_CuandoCredencialesCorrectas()
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

    public function test_DeberiaNegarInicioYVolver_CuandoNombreYContrasenaIncorrectas()
    {
        Usuarie::create([
            "nombre" => "Juan",
            "contrasena" => Hash::make("contra")
        ]);

        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "nombre_falso",
            "contrasena" => "contrasena_falsa"
        ]);

        $response->assertStatus(403); // 403: Forbidden
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "autenticacion" => "Las credenciales ingresadas no son correctas. Por favor verificá que escribiste correctamente los datos."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaNegarInicioYVolver_CuandoNombreIncorrecto()
    {
        Usuarie::create([
            "nombre" => "Juan",
            "contrasena" => Hash::make("contra")
        ]);

        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "nombre_falso",
            "contrasena" => "contra"
        ]);

        $response->assertStatus(403); // 403: Forbidden
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "autenticacion" => "Las credenciales ingresadas no son correctas. Por favor verificá que escribiste correctamente los datos."
        ]);
        $this->assertGuest();
    }

    public function test_DeberiaNegarInicioYVolver_CuandoContrasenaIncorrecta()
    {
        Usuarie::create([
            "nombre" => "Juan",
            "contrasena" => Hash::make("contra")
        ]);

        $response = $this->from("/entrar")->post('/entrar', [
            "nombre" => "Juan",
            "contrasena" => "contrasena_falsa"
        ]);

        $response->assertStatus(403); // 403: Forbidden
        $response->assertRedirect("/entrar");
        $response->assertSessionHasErrors([
            "autenticacion" => "Las credenciales ingresadas no son correctas. Por favor verificá que escribiste correctamente los datos."
        ]);
        $this->assertGuest();
    }
}
