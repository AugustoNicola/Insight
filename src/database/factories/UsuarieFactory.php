<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UsuarieFactory extends Factory
{
    public function definition()
    {
        return [
            "nombre" => $this->faker->name(),
            "contrasena" => Hash::make($this->faker->word())
        ];
    }
}
