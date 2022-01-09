<?php

namespace Tests\Feature\Categorias;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models;

class RutaCategoriasTest extends TestCase
{
    use RefreshDatabase; // uso RefreshDatabase para que el default sea sin categorias

    public function test_DeberiaMostrarMensajeVacio_CuandoNoHayCategorias()
    {
        // no hay ninguna categoria en la base de datos

        $response = $this->get("/categorias");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertSeeText("No se han encontrado categorías.");
        $response->assertDontSeeText("publicaci"); // "20 publicaci(ón|ones)"
    }

    public function test_DeberiaMostrarCategoriasConInfo_CuandoHayCategoriasSinPublicaciones()
    {
        $categorias = Models\Categoria::factory()->count(4)->create(); // no tienen relacion a publicaciones

        $response = $this->get("/categorias");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertDontSeeText("No se han encontrado categorías.");

        foreach ($categorias as $categoria) {
            $response->assertSeeText($categoria->nombre);
            $response->assertSeeText($categoria->descripcion);
        }
        $response->assertSeeText("Sin publicaciones");
    }

    public function test_DeberiaMostrarCategoriasConInfo_CuandoHayCategoriasConPublicaciones()
    {
        $categorias = Models\Categoria::factory()->count(3)->create();
        $publicaciones = Models\Publicacion::factory()->count(4)->create();

        // categorias[0] no tiene publicaciones
        $categorias[1]->publicaciones()->attach($publicaciones->random(1)->pluck('id')->toArray()); // categorias[1] tiene una publicacion
        $categorias[2]->publicaciones()->attach($publicaciones->random(4)->pluck('id')->toArray()); // categorias[2] tiene cuatro publicaciones

        $response = $this->get("/categorias");

        $response->assertStatus(200); // 200: Ok
        $response->assertSessionHasNoErrors();
        $response->assertDontSeeText("No se han encontrado categorías.");

        foreach ($categorias as $categoria) {
            $response->assertSeeText($categoria->nombre);
            $response->assertSeeText($categoria->descripcion);
        }
        $response->assertSeeText("Sin publicaciones");
        $response->assertSeeText("1 publicación");
        $response->assertSeeText("4 publicaciones");
    }
}
