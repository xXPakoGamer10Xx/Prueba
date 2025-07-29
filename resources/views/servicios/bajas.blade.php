@extends('components.layouts.servicios.nav-servicios')

@section('contenido')
<main class="container my-5">
  <h2 class="mb-4 text-center">Reporte de Baja de Equipo</h2>

  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <form method="POST" action="{{ route('servicios.bajas.store') }}" class="row g-4 needs-validation" novalidate>
    @csrf
    <div class="col-md-6">
        <label for="id_inventario" class="form-label">Equipo del Inventario:</label>
        <select class="form-select" id="id_inventario" name="id_inventario" required>
            <option selected disabled value="">Selecciona un equipo...</option>
            @foreach ($equipos_inventario as $item)
                <option value="{{ $item->id_inventario }}">
                    ID: {{ $item->id_inventario }} - Eq: {{ $item->nombre_equipo }} - Serie: {{ $item->num_serie }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback">Por favor, selecciona un equipo.</div>
    </div>

    <div class="col-md-6">
        <label for="id_mantenimiento" class="form-label">Mantenimiento Relacionado (Automático):</label>
        <select class="form-select" id="id_mantenimiento" name="id_mantenimiento">
            <option selected value="">-- Ninguno --</option>
             @foreach ($mantenimientos as $mant)
                 <option value="{{ $mant->id_mantenimiento }}" data-inventario-id="{{ $mant->id_inventario }}">
                     ID: {{ $mant->id_mantenimiento }} - Fecha: {{ $mant->fecha }}
                 </option>
             @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label for="estado" class="form-label">Estado del Proceso de Baja:</label>
        <select class="form-select" id="estado" name="estado" required>
            <option selected disabled value="">Selecciona un estado...</option>
            <option value="en proceso">En proceso</option>
            <option value="baja completa">Baja completa</option>
            <option value="cancelada">Cancelada</option>
        </select>
        <div class="invalid-feedback">Por favor, selecciona el estado del proceso.</div>
    </div>

    <div class="col-md-6">
      <label for="motivo" class="form-label">Motivo de la Baja:</label>
      <textarea class="form-control" id="motivo" name="motivo" rows="3" required></textarea>
       <div class="invalid-feedback">Por favor, describe el motivo de la baja.</div>
    </div>

    <div class="col-12 d-flex justify-content-center gap-4 mt-3">
      <button type="submit" class="btn btn-danger px-5"><i class="fas fa-check-circle me-2"></i>Registrar Baja</button>
      <a href="{{ route('servicios.bajas.historial') }}" class="btn btn-secondary"><i class="fas fa-list-alt me-2"></i>Ver Historial</a>
    </div>
  </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- LÍNEA CORREGIDA ---
    // Pasamos los datos de PHP a JavaScript de una forma más segura
    const mantenimientosData = JSON.parse('{!! json_encode($mantenimientos) !!}');
    const selectInventario = document.getElementById('id_inventario');
    const selectMantenimiento = document.getElementById('id_mantenimiento');

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

    selectInventario.addEventListener('change', actualizarMantenimientoRelacionado);
    actualizarMantenimientoRelacionado();

    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
});
</script>
@endsection

