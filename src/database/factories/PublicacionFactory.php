<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Usuarie;

class PublicacionFactory extends Factory
{
    public function definition()
    {
        return [
            "titulo" => $this->faker->sentence(8),
            "cuerpo" => $this->faker->paragraphs(3, true),
            "usuarie_id" => function () {
                return Usuarie::factory()->create()->id;
            }
        ];
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
