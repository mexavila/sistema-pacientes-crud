@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Registrar Nuevo Paciente</h4>
                </div>
                <div class="card-body">
                    <form id="paciente-form">
                        @csrf

                        <!-- Documento -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo_documento_id" class="form-label">Tipo de Documento</label>
                                <select class="form-select" id="tipo_documento_id" name="tipo_documento_id" required>
                                    <option value="">Seleccione...</option>
                                    <!-- Opciones se cargarán via API -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="numero_documento" class="form-label">Número de Documento</label>
                                <input type="text" class="form-control" id="numero_documento" name="numero_documento" required>
                            </div>
                        </div>

                        <!-- Nombres -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre1" class="form-label">Primer Nombre</label>
                                <input type="text" class="form-control" id="nombre1" name="nombre1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nombre2" class="form-label">Segundo Nombre</label>
                                <input type="text" class="form-control" id="nombre2" name="nombre2">
                            </div>
                        </div>

                        <!-- Apellidos -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="apellido1" class="form-label">Primer Apellido</label>
                                <input type="text" class="form-control" id="apellido1" name="apellido1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="apellido2" class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" id="apellido2" name="apellido2">
                            </div>
                        </div>

                        <!-- Género y Ubicación -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="genero_id" class="form-label">Género</label>
                                <select class="form-select" id="genero_id" name="genero_id" required>
                                    <option value="">Seleccione...</option>
                                    <!-- Opciones se cargarán via API -->
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="departamento_id" class="form-label">Departamento</label>
                                <select class="form-select" id="departamento_id" name="departamento_id" required>
                                    <option value="">Seleccione...</option>
                                    <!-- Opciones se cargarán via API -->
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="municipio_id" class="form-label">Municipio</label>
                                <select class="form-select" id="municipio_id" name="municipio_id" required disabled>
                                    <option value="">Primero seleccione departamento</option>
                                </select>
                            </div>
                        </div>

                        <!-- Contacto y Foto -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo">
                            </div>
                            <div class="col-md-6">
                                <label for="foto" class="form-label">Foto (Opcional)</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-secondary me-md-2" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Paciente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@isset( $pacienteJson )
<script>const pacienteJson = '{{ $pacienteJson }}';</script>
@endisset
<script src="{{ asset('js/paciente-form.js') }}"></script>
@endsection

@section('scripts')
@endsection

