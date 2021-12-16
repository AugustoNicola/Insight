<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Publicacion;
use App\Models\Usuarie;

class ComentarioFactory extends Factory
{
    public function definition()
    {
        return [
            "cuerpo" => $this->faker->paragraph(3),
            "publicacion_id" => function () {
                return Publicacion::factory()->create()->id;
            },
            "usuarie_id" => function () {
                return Usuarie::factory()->create()->id;
            }
        ];
    }
    
    public function publicacionExistente()
    {
        return $this->state(function (array $attributes) {
            return [
                "publicacion_id" => Publicacion::all()->random()->id,
            ];
        });
    }
    public function usuarieExistente()
    {
        return $this->state(function (array $attributes) {
            return [
                "usuarie_id" => Usuarie::all()->random()->id,
            ];
        });
    }
}
