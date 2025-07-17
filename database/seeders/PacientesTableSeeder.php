<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PacientesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposDoc = DB::table('tipos_documento')->pluck('id');
        $generos = DB::table('generos')->pluck('id');
        $municipios = DB::table('municipios')->pluck('id');

        for ($i = 1; $i <= 5; $i++) {
            DB::table('pacientes')->insert([
                'tipo_documento_id' => $tiposDoc->random(),
                'numero_documento' => '1000000'.$i,
                'nombre1' => 'Paciente'.$i,
                'nombre2' => 'Segundo'.$i,
                'apellido1' => 'Apellido'.$i,
                'apellido2' => 'Segundo'.$i,
                'genero_id' => $generos->random(),
                'departamento_id' => DB::table('municipios')->where('id', $municipios->random())->value('departamento_id'),
                'municipio_id' => $municipios->random(),
                'correo' => 'paciente'.$i.'@example.com',
                'foto' => $i % 2 == 0 ? 'fotos/paciente'.$i.'.jpg' : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
