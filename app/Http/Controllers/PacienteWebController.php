<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\PacienteController;

class PacienteWebController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Mantener la página actual en sesión
            if ($request->has('page')) {
                Session::put('current_page', $request->query('page'));
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the patients.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $currentPage = $request->query('page', 1);
            
            $response = Http::withToken($request->cookie('api_token'))
                ->get(config('app.api_url').'/api/pacientes', [
                    'page' => $currentPage
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Guardar la página actual en sesión
                $request->session()->put('current_page', $currentPage);
                
                return view('pacientes.index', [
                    'pacientes' => $data['data'],
                    'pagination' => $data['meta'] 
                ]);
            }

            // Manejo específico para paginación
            if ($response->status() === 401 && $currentPage > 1) {
                return redirect()
                    ->route('pacientes.index', ['page' => 1])
                    ->with('error', 'Sesión expirada');
            }

            throw new \Exception('Error al obtener pacientes');

        } catch (\Exception $e) {

            dd( $e->getMessage() );

            return redirect()
                ->route('login')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new patient.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pacientes.create');
    }


    /**
     * Show the form for editing the specified patient.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $pacienteController = new PacienteController();

            $pacienteJson = $pacienteController->show( $id );

            $data = $pacienteJson->getData();

            if( $data->success )
            {            
                $pacienteJson = json_encode( $data->data );

                return view('pacientes.create', compact( 'pacienteJson' ) );
            }

            return back()->with('error', 'Error al cargar datos para edición');

        } catch (\Exception $e) {
            return redirect()->route('pacientes.index')
                ->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified patient in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $request->cookie('api_token'),
                'Accept' => 'application/json'
            ])->put(config('app.api_url').'/api/pacientes/'.$id, $request->except(['_token', '_method']));

            if ($response->successful()) {
                return redirect()->route('pacientes.index')
                    ->with('success', 'Paciente actualizado exitosamente');
            }

            if ($response->status() === 422) {
                return back()
                    ->withErrors($response->json()['errors'])
                    ->withInput();
            }

            return back()
                ->with('error', 'Error al actualizar paciente: ' . ($response->json()['message'] ?? 'Error desconocido'))
                ->withInput();

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al conectar con el servidor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified patient from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . request()->cookie('api_token'),
                'Accept' => 'application/json'
            ])->delete(config('app.api_url').'/api/pacientes/'.$id);

            if ($response->successful()) {
                return redirect()->route('pacientes.index')
                    ->with('success', 'Paciente eliminado correctamente');
            }

            return back()->with('error', 'Error al eliminar paciente: ' . ($response->json()['message'] ?? 'Error desconocido'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error al conectar con el servidor: ' . $e->getMessage());
        }
    }

    /**
     * Show the patient details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . request()->cookie('api_token'),
                'Accept' => 'application/json'
            ])->get(config('app.api_url').'/api/pacientes/'.$id);

            if ($response->successful()) {
                return view('pacientes.show', [
                    'paciente' => $response->json()['data']
                ]);
            }

            return back()->with('error', 'Error al cargar datos del paciente');

        } catch (\Exception $e) {
            return redirect()->route('pacientes.index')
                ->with('error', 'Error al cargar detalles: ' . $e->getMessage());
        }
    }

    /**
     * Upload patient photo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadPhoto(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $request->cookie('api_token'),
                'Accept' => 'application/json'
            ])->attach(
                'foto', 
                $request->file('foto')->get(),
                $request->file('foto')->getClientOriginalName()
            )->post(config('app.api_url').'/api/pacientes/'.$id.'/foto');

            if ($response->successful()) {
                return back()->with('success', 'Foto actualizada correctamente');
            }

            return back()->with('error', 'Error al subir foto: ' . ($response->json()['message'] ?? 'Error desconocido'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error al conectar con el servidor: ' . $e->getMessage());
        }
    }
}