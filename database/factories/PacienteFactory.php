<?php

namespace Database\Factories;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

class PacienteFactory extends Factory
{
    protected $model = Paciente::class;

    public function definition()
    {
        return [
            'tipo_documento_id' => \App\Models\TipoDocumento::factory(),
            'numero_documento' => $this->faker->unique()->numerify('##########'),
            'nombre1' => $this->faker->firstName,
            'nombre2' => $this->faker->firstName,
            'apellido1' => $this->faker->lastName,
            'apellido2' => $this->faker->lastName,
            'genero_id' => \App\Models\Genero::factory(),
            'departamento_id' => \App\Models\Departamento::factory(),
            'municipio_id' => \App\Models\Municipio::factory(),
            'correo' => $this->faker->unique()->safeEmail,
            'foto' => null
        ];
    }
}