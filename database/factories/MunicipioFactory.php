<?php

namespace Database\Factories;

use App\Models\Municipio;
use Illuminate\Database\Eloquent\Factories\Factory;

class MunicipioFactory extends Factory
{
    protected $model = Municipio::class;

    public function definition()
    {
        return [
            'departamento_id' => \App\Models\Departamento::factory(),
            'nombre' => $this->faker->unique()->city()
        ];
    }
}