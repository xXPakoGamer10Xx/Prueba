@extends('components.layouts.ginecologia.nav-ginecologia')

@section('contenido')

    <main class="container my-5">
    <h3 class="text-center fw-bold mb-4">Gestión de Expedientes de Pacientes</h3>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Error!</strong> Por favor, corrige los siguientes errores:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('expediente.index') }}" method="GET" class="d-flex">
            <div class="input-group w-auto">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Buscar paciente..." 
                    value="{{ $searchTerm ?? '' }}">
                <button class="btn btn-outline-secondary" type="submit">🔍</button>
            </div>
        </form>
        <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalAgregar">+</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID Paciente</th>
                    <th>Nombre Completo</th>
                    <th>Género</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pacientes as $paciente)
                    <tr>
                        <td>{{ $paciente->id_paciente }}</td>
                        <td class="text-start">{{ $paciente->nombre_completo }}</td>
                        <td>{{ $paciente->genero_paciente }}</td>
                        <td>{{ $paciente->fecha_nac ? \Carbon\Carbon::parse($paciente->fecha_nac)->format('d/m/Y') : 'N/A' }}</td>
                        <td>
                            <button class="icon-btn text-warning btn-editar" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditar"
                                    data-url="{{ route('expediente.update', $paciente) }}"
                                    data-id="{{ $paciente->id_paciente }}"
                                    data-nombre="{{ $paciente->nombre_paciente }}"
                                    data-apellido1="{{ $paciente->apellido1_paciente }}"
                                    data-apellido2="{{ $paciente->apellido2_paciente }}"
                                    data-fecha_nac="{{ $paciente->fecha_nac }}"
                                    data-genero="{{ $paciente->genero_paciente }}">
                                ✏️
                            </button>
                            <button class="icon-btn text-danger btn-eliminar" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEliminar"
                                    data-url="{{ route('expediente.destroy', $paciente) }}">
                                🗑️
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No se encontraron pacientes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {!! $pacientes->appends(['search' => $searchTerm])->links() !!}
    </div>
</main>

{{-- MODALES --}}

<div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nuevo Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('expediente.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_paciente" class="form-label fw-bold">ID Paciente</label>
                            <input type="text" name="id_paciente" class="form-control" value="{{ old('id_paciente') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nombre_paciente" class="form-label fw-bold">Nombre(s)</label>
                            <input type="text" name="nombre_paciente" class="form-control" value="{{ old('nombre_paciente') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="apellido1_paciente" class="form-label fw-bold">Apellido Paterno</label>
                            <input type="text" name="apellido1_paciente" class="form-control" value="{{ old('apellido1_paciente') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="apellido2_paciente" class="form-label fw-bold">Apellido Materno</label>
                            <input type="text" name="apellido2_paciente" class="form-control" value="{{ old('apellido2_paciente') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_nac" class="form-label fw-bold">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nac" class="form-control" value="{{ old('fecha_nac') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="genero_paciente" class="form-label fw-bold">Género</label>
                            <select name="genero_paciente" class="form-select" required>
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="Masculino" @if(old('genero_paciente') == 'Masculino') selected @endif>Masculino</option>
                                <option value="Femenino" @if(old('genero_paciente') == 'Femenino') selected @endif>Femenino</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Paciente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditar" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">ID Paciente</label>
                            <input type="text" id="edit_id_paciente" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_nombre_paciente" class="form-label fw-bold">Nombre(s)</label>
                            <input type="text" name="nombre_paciente" id="edit_nombre_paciente" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_apellido1_paciente" class="form-label fw-bold">Apellido Paterno</label>
                            <input type="text" name="apellido1_paciente" id="edit_apellido1_paciente" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_apellido2_paciente" class="form-label fw-bold">Apellido Materno</label>
                            <input type="text" name="apellido2_paciente" id="edit_apellido2_paciente" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_fecha_nac" class="form-label fw-bold">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nac" id="edit_fecha_nac" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_genero_paciente" class="form-label fw-bold">Género</label>
                            <select name="genero_paciente" id="edit_genero_paciente" class="form-select" required>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEliminar" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar a este paciente? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection