<?php

namespace Database\Factories;

use App\Models\Genero;
use Illuminate\Database\Eloquent\Factories\Factory;

class GeneroFactory extends Factory
{
    protected $model = Genero::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->randomElement(['Masculino', 'Femenino', 'Otro'])
        ];
    }
}