<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Paciente;
use App\Models\TipoDocumento;
use App\Models\Genero;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PacienteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $this->user->email,
            'password' => 'password'
        ]);

        $this->token = $loginResponse->json('access_token');

        // Crear datos relacionados necesarios
        TipoDocumento::factory()->create(['id' => 1]);
        Genero::factory()->create(['id' => 1]);
        Departamento::factory()->create(['id' => 1]);
        Municipio::factory()->create(['id' => 1, 'departamento_id' => 1]);
    }

    public function test_obtener_listado_de_pacientes()
    {
        Paciente::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->get('/api/pacientes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'numero_documento',
                            'nombre1',
                            'apellido1',
                            'tipo_documento',
                            'genero'
                        ]
                    ]
                ]
            ]);
    }

    public function test_crear_paciente()
    {
        $pacienteData = [
            'tipo_documento_id' => 1,
            'numero_documento' => '123456789',
            'nombre1' => 'Juan',
            'nombre2' => 'Carlos',
            'apellido1' => 'Perez',
            'apellido2' => 'Gomez',
            'genero_id' => 1,
            'departamento_id' => 1,
            'municipio_id' => 1,
            'correo' => 'juan@example.com'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->post('/api/pacientes', $pacienteData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'numero_documento' => '123456789',
                    'nombre1' => 'Juan'
                ]
            ]);
    }

    public function test_validacion_al_crear_paciente()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->post('/api/pacientes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'tipo_documento_id',
                'numero_documento',
                'nombre1',
                'apellido1',
                'genero_id',
                'departamento_id',
                'municipio_id'
            ]);
    }

    public function test_subir_foto_de_paciente()
    {
        Storage::fake('public');

        $paciente = Paciente::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->post('/api/pacientes/' . $paciente->id . '/foto', [
            'foto' => UploadedFile::fake()->image('foto.jpg')
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['foto_url']
            ]);

        Storage::disk('public')->assertExists('pacientes/' . $response->json('data.foto_url'));
    }
}