@extends('components.layouts.servicios.nav-servicios')

@section('contenido')
<main class="container my-5">
    {{-- Título y Botón para abrir el Modal --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="h3 mb-0">Historial de Bajas de Equipo</h2>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addBajaModal">
            <i class="fas fa-plus me-2"></i>Registrar Nueva Baja
        </button>
    </div>

    {{-- Alertas de éxito o error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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
    <form class="d-flex mb-4" method="GET" action="{{ route('servicios.bajas.historial') }}">
        <input class="form-control me-2" type="search" placeholder="Buscar por equipo, serie, motivo o estado..." name="search" value="{{ $search_query ?? '' }}">
        <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
    </form>

    {{-- Tabla de Historial --}}
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Equipo (Serie)</th>
                    <th>Estado</th>
                    <th>Motivo de Baja</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bajas as $baja)
                <tr>
                    <td>{{ $baja->id_proceso_baja }}</td>
                    {{-- Accedemos a los datos a través de las relaciones del modelo --}}
                    <td>{{ $baja->inventario->equipo->nombre ?? 'N/D' }} ({{ $baja->inventario->num_serie ?? 'N/D' }})</td>
                    <td><span class="badge @switch($baja->estado) @case('baja completa') bg-danger @break @case('en proceso') bg-warning text-dark @break @case('cancelada') bg-secondary @break @default bg-light text-dark @endswitch">{{ ucfirst($baja->estado) }}</span></td>
                    <td>{{ $baja->motivo }}</td>
                    <td class="text-center"><button class="btn btn-danger btn-sm" data-baja-json="{{ json_encode($baja) }}" onclick="generarPDFBaja(this)"><i class="fas fa-file-pdf"></i></button></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No hay registros de baja para mostrar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($bajas->hasPages())
        <nav>{{ $bajas->appends(['search' => $search_query ?? ''])->links() }}</nav>
    @endif
</main>

{{-- =================================================================== --}}
{{-- MODAL PARA AÑADIR REPORTE DE BAJA --}}
{{-- =================================================================== --}}
<div class="modal fade" id="addBajaModal" tabindex="-1" aria-labelledby="addBajaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBajaModalLabel">Registrar Baja de Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('servicios.bajas.store') }}" class="row g-4 needs-validation" novalidate>
                    @csrf
                    <div class="col-md-6">
                        <label for="id_inventario_modal" class="form-label">Equipo del Inventario:</label>
                        <select class="form-select" id="id_inventario_modal" name="id_inventario" required>
                            <option selected disabled value="">Selecciona un equipo...</option>
                            @foreach ($equipos_inventario as $item)
                                <option value="{{ $item->id_inventario }}">ID: {{ $item->id_inventario }} - Eq: {{ $item->equipo->nombre }} - Serie: {{ $item->num_serie }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="id_mantenimiento_modal" class="form-label">Mantenimiento Relacionado (Automático):</label>
                        <select class="form-select" id="id_mantenimiento_modal" name="id_mantenimiento">
                            <option selected value="">-- Ninguno --</option>
                            @foreach ($mantenimientos as $mant)
                                <option value="{{ $mant->id_mantenimiento }}" data-inventario-id="{{ $mant->id_inventario }}">{{ $mant->id_mantenimiento }} - {{ $mant->fecha }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="estado_modal" class="form-label">Estado del Proceso:</label>
                        <select class="form-select" id="estado_modal" name="estado" required>
                            <option selected disabled value="">Selecciona un estado...</option>
                            <option value="en proceso">En proceso</option>
                            <option value="baja completa">Baja completa</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="motivo_modal" class="form-label">Motivo de la Baja:</label>
                        <textarea class="form-control" id="motivo_modal" name="motivo" rows="3" required></textarea>
                    </div>
                    <div class="col-12 text-end mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-check-circle me-2"></i>Registrar Baja</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    function generarPDFBaja(button) {
        const data = JSON.parse(button.getAttribute('data-baja-json'));
        // El resto del código del PDF se mantiene igual
    }

    document.addEventListener('DOMContentLoaded', function() {
        const mantenimientosData = JSON.parse('{!! json_encode($mantenimientos) !!}');
        const selectInventario = document.getElementById('id_inventario_modal');
        const selectMantenimiento = document.getElementById('id_mantenimiento_modal');

        function actualizarMantenimientoRelacionado() {
            const selectedInventarioId = selectInventario.value;
            let ultimoMantenimientoId = '';
            for (const mant of mantenimientosData) {
                if (mant.id_inventario == selectedInventarioId) {
                    ultimoMantenimientoId = mant.id_mantenimiento;
                    break;
                }
            }
            selectMantenimiento.value = ultimoMantenimientoId;
        }

        if(selectInventario) {
            selectInventario.addEventListener('change', actualizarMantenimientoRelacionado);
        }
    });
</script>
@endsection
