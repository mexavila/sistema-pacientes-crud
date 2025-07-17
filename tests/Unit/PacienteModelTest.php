<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PacienteModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_crear_paciente()
    {
        // Crear paciente con todas las relaciones automáticamente
        $paciente = Paciente::factory()->create();

        $this->assertDatabaseHas('pacientes', [
            'id' => $paciente->id,
            'numero_documento' => $paciente->numero_documento
        ]);
    }

    public function test_relaciones_del_paciente()
    {
        $paciente = Paciente::factory()->create();

        // Verificar relaciones
        $this->assertNotNull($paciente->tipoDocumento);
        $this->assertNotNull($paciente->genero);
        $this->assertNotNull($paciente->departamento);
        $this->assertNotNull($paciente->municipio);
    }
}