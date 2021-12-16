<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarieFactory extends Factory
{
    public function definition()
    {
        return [
            "nombre" => $this->faker->name(),
            "contrasena" => $this->faker->password(6, 10)
        ];
    }
}
