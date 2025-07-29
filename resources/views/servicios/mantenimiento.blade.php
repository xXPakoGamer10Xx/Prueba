@extends('components.layouts.servicios.nav-servicios')

@section('contenido')
<main class="container my-5">
    <h2 class="mb-4">Reporte de Mantenimiento</h2>

    {{-- Muestra un mensaje si se agregó un encargado exitosamente --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Muestra errores de validación si los hay --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="row g-3" method="POST" action="{{ route('servicios.mantenimiento.store') }}">
        @csrf
        <div class="col-md-6">
            <label for="id_inventario" class="form-label">Equipo de inventario:</label>
            <select id="id_inventario" class="form-select" name="id_inventario" required>
                <option value="" disabled selected>Seleccionar equipo...</option>
                {{-- Verifica si hay equipos para mostrar --}}
                @forelse ($equipos_inventario as $item)
                    <option value="{{ $item->id_inventario }}">
                        ID: {{ $item->id_inventario }} - {{ $item->nombre }} ({{ $item->num_serie }})
                    </option>
                @empty
                    <option value="" disabled>No hay equipos disponibles en la base de datos</option>
                @endforelse
            </select>
        </div>
        <div class="col-md-6">
            <label for="id_encargado_man" class="form-label">Encargado:</label>
            <div class="input-group">
                <select id="id_encargado_man" class="form-select" name="id_encargado_man" required>
                    <option value="" disabled selected>Seleccionar encargado...</option>
                    @forelse ($encargados as $encargado)
                        <option value="{{ $encargado->id_encargado_man }}">
                            {{ $encargado->nombre }} {{ $encargado->apellidos }}
                        </option>
                    @empty
                         <option value="" disabled>No hay encargados registrados</option>
                    @endforelse
                </select>
                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#addEncargadoModal" title="Agregar Encargado"><i class="fas fa-user-plus"></i></button>
            </div>
        </div>
        <div class="col-md-6"><label class="form-label">Fecha:</label><input type="date" class="form-control" name="fecha" required></div>
        <div class="col-md-6"><label class="form-label">Tipo:</label><select name="tipo" class="form-select" required><option value="">Seleccionar...</option><option value="preventivo">Preventivo</option><option value="correctivo">Correctivo</option></select></div>
        <div class="col-md-6"><label class="form-label">Refacciones/Material:</label><textarea class="form-control" name="refacciones_material" rows="3"></textarea></div>
        <div class="col-md-6"><label class="form-label">Observaciones:</label><textarea class="form-control" name="observaciones" rows="3"></textarea></div>
        <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar Reporte</button>
            <a href="{{ route('servicios.reportes') }}" class="btn btn-info"><i class="fas fa-list-alt me-2"></i>Ver Reportes</a>
        </div>
    </form>
</main>

<!-- Modal para agregar encargado -->
<div class="modal fade" id="addEncargadoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Nuevo Encargado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('servicios.encargados.store') }}" method="POST">
                    @csrf
                    <div class="mb-3"><label class="form-label">Nombre(s):</label><input type="text" class="form-control" name="nombre" required></div>
                    <div class="mb-3"><label class="form-label">Apellidos:</label><input type="text" class="form-control" name="apellidos" required></div>
                    <div class="mb-3"><label class="form-label">Cargo:</label><input type="text" class="form-control" name="cargo" required></div>
                    <div class="mb-3"><label class="form-label">Contacto:</label><input type="text" class="form-control" name="contacto"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Encargado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection