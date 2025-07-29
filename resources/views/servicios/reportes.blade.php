@extends('components.layouts.servicios.nav-servicios')

@section('contenido')
<main class="container my-5">
    {{-- Título y Botón para abrir el Modal --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="h3 mb-0">Historial de Mantenimientos</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMantenimientoModal">
            <i class="fas fa-plus me-2"></i>Añadir Nuevo Reporte
        </button>
    </div>

    {{-- Alertas de éxito o error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>¡Error!</strong> Por favor, corrige los siguientes problemas en el formulario:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Barra de Búsqueda --}}
    <form class="d-flex mb-4" method="GET" action="{{ route('servicios.reportes') }}">
        <input class="form-control me-2" type="search" placeholder="Buscar por equipo, encargado o tipo..." name="search" value="{{ $search_query ?? '' }}">
        <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
    </form>

    {{-- Tabla de Historial --}}
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Equipo (Serie)</th>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Encargado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reportes as $reporte)
                <tr>
                    <td>{{ $reporte->id_mantenimiento }}</td>
                    {{-- Accedemos a los datos a través de las relaciones del modelo --}}
                    <td>{{ $reporte->inventario->equipo->nombre ?? 'N/D' }} ({{ $reporte->inventario->num_serie ?? 'N/D' }})</td>
                    <td>{{ $reporte->fecha }}</td>
                    <td><span class="badge {{ $reporte->tipo == 'correctivo' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">{{ ucfirst($reporte->tipo) }}</span></td>
                    <td>{{ $reporte->encargadoMantenimiento->nombre ?? '' }} {{ $reporte->encargadoMantenimiento->apellidos ?? '' }}</td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm" data-reporte-json="{{ json_encode($reporte) }}" onclick="generarPDFReporte(this)">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class='text-center fst-italic'>No hay reportes disponibles.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Paginación --}}
    @if($reportes->hasPages())
        <nav>{{ $reportes->appends(['search' => $search_query ?? ''])->links() }}</nav>
    @endif
</main>

{{-- =================================================================== --}}
{{-- MODAL PARA AÑADIR REPORTE DE MANTENIMIENTO --}}
{{-- =================================================================== --}}
<div class="modal fade" id="addMantenimientoModal" tabindex="-1" aria-labelledby="addMantenimientoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMantenimientoModalLabel">Añadir Nuevo Reporte de Mantenimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" method="POST" action="{{ route('servicios.mantenimiento.store') }}">
                    @csrf
                    <div class="col-md-6">
                        <label for="id_inventario" class="form-label">Equipo de inventario:</label>
                        <select id="id_inventario" class="form-select" name="id_inventario" required>
                            <option value="" disabled selected>Seleccionar equipo...</option>
                            @foreach ($equipos_inventario as $item)
                                <option value="{{ $item->id_inventario }}">{{ $item->equipo->nombre }} ({{ $item->num_serie }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="id_encargado_man" class="form-label">Encargado:</label>
                        <div class="input-group">
                            <select id="id_encargado_man" class="form-select" name="id_encargado_man" required>
                                <option value="" disabled selected>Seleccionar encargado...</option>
                                @foreach ($encargados as $encargado)
                                    <option value="{{ $encargado->id_encargado_man }}">{{ $encargado->nombre }} {{ $encargado->apellidos }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#addEncargadoModal" title="Agregar Encargado"><i class="fas fa-user-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="form-label">Fecha:</label><input type="date" class="form-control" name="fecha" required></div>
                    <div class="col-md-6"><label class="form-label">Tipo:</label><select name="tipo" class="form-select" required><option value="">Seleccionar...</option><option value="preventivo">Preventivo</option><option value="correctivo">Correctivo</option></select></div>
                    <div class="col-md-6"><label class="form-label">Refacciones/Material:</label><textarea class="form-control" name="refacciones_material" rows="3"></textarea></div>
                    <div class="col-md-6"><label class="form-label">Observaciones:</label><textarea class="form-control" name="observaciones" rows="3"></textarea></div>
                    <div class="col-12 text-end mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar Reporte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal anidado para agregar encargado --}}
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

{{-- Script para generar PDF --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    function generarPDFReporte(button) {
        const data = JSON.parse(button.getAttribute('data-reporte-json'));
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        // El resto del código del PDF se mantiene igual
    }
</script>
@endsection
