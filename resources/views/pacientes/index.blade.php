@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Listado de Pacientes</h1>
        <a href="{{ route('pacientes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Paciente
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            @if(isset($pacientes) && count($pacientes) > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Género</th>
                            <th>Departamento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pacientes as $paciente)
                        <tr>
                            <td>{{ $paciente['numero_documento'] }}</td>
                            <td>{{ $paciente['nombre1'] }} {{ $paciente['nombre2'] ?? '' }}</td>
                            <td>{{ $paciente['apellido1'] }} {{ $paciente['apellido2'] ?? '' }}</td>
                            <td>{{ $paciente['genero']['nombre'] ?? '' }}</td>
                            <td>{{ $paciente['departamento']['nombre'] ?? '' }}</td>
                            <td>
                                <a href="{{ route('pacientes.edit', $paciente['id']) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info">
                No se encontraron pacientes registrados.
            </div>
            @endif
        </div>
    </div>

    @if(isset($pagination))
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                @if($pagination['current_page'] > 1)
                <li class="page-item">
                    <a class="page-link" href="?page={{ $pagination['current_page'] - 1 }}" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                @endif
                
                @for($i = 1; $i <= $pagination['last_page']; $i++)
                <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                    <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                </li>
                @endfor
                
                @if($pagination['current_page'] < $pagination['last_page'])
                <li class="page-item">
                    <a class="page-link" href="?page={{ $pagination['current_page'] + 1 }}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
    
</div>
@endsection