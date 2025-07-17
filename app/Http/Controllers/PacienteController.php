<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PacienteController extends Controller
{	
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
    
	    $pacientes = Paciente::with([
            'tipoDocumento:id,nombre',
            'genero:id,nombre',
            'departamento:id,nombre',
            'municipio:id,nombre'
        ])
        ->paginate($perPage);

	    return response()->json([
	        'success' => true,
	        'data' => $pacientes->items(), // Los items paginados
	        'meta' => [
	            'current_page' => $pacientes->currentPage(),
	            'last_page' => $pacientes->lastPage(),
	            'per_page' => $pacientes->perPage(),
	            'total' => $pacientes->total(),
	            'from' => $pacientes->firstItem(),
	            'to' => $pacientes->lastItem()
	        ],
	        'links' => [
	            'first' => $pacientes->url(1),
	            'last' => $pacientes->url($pacientes->lastPage()),
	            'prev' => $pacientes->previousPageUrl(),
	            'next' => $pacientes->nextPageUrl()
	        ]
	    ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_documento_id' => 'required|exists:tipos_documento,id',
            'numero_documento' => 'required|unique:pacientes|max:20',
            'nombre1' => 'required|max:50',
            'nombre2' => 'nullable|max:50',
            'apellido1' => 'required|max:50',
            'apellido2' => 'nullable|max:50',
            'genero_id' => 'required|exists:generos,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'municipio_id' => 'required|exists:municipios,id',
            'correo' => 'nullable|email|max:100',
            'foto' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $paciente = Paciente::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $paciente->load(['tipoDocumento', 'genero', 'departamento', 'municipio'])
        ], 201);
    }

    public function show(string $id)
    {
        $paciente = Paciente::with(['tipoDocumento', 'genero', 'departamento', 'municipio'])
                          ->find($id);

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $paciente
        ]);
    }

    public function update(Request $request, string $id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo_documento_id' => 'sometimes|required|exists:tipos_documento,id',
            'numero_documento' => 'sometimes|required|max:20|unique:pacientes,numero_documento,'.$id,
            'nombre1' => 'sometimes|required|max:50',
            'nombre2' => 'nullable|max:50',
            'apellido1' => 'sometimes|required|max:50',
            'apellido2' => 'nullable|max:50',
            'genero_id' => 'sometimes|required|exists:generos,id',
            'departamento_id' => 'sometimes|required|exists:departamentos,id',
            'municipio_id' => 'sometimes|required|exists:municipios,id',
            'correo' => 'nullable|email|max:100',
            'foto' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $paciente->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $paciente->load(['tipoDocumento', 'genero', 'departamento', 'municipio'])
        ]);
    }

    public function destroy(string $id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        // Eliminar foto si existe
        if ($paciente->foto) {
            Storage::delete($paciente->foto);
        }

        $paciente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paciente eliminado correctamente'
        ]);
    }

    public function uploadPhoto(Request $request, string $id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Eliminar foto anterior si existe
        if ($paciente->foto) {
            Storage::delete($paciente->foto);
        }

        // Guardar nueva foto
        $path = $request->file('foto')->store('public/pacientes');
        $paciente->foto = str_replace('public/', '', $path);
        $paciente->save();

        return response()->json([
            'success' => true,
            'data' => [
                'foto_url' => Storage::url($path)
            ]
        ]);
    }
}
