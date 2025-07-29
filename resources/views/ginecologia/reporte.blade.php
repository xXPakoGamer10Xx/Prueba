@extends('layouts.app') {{-- Tu plantilla principal --}}

@section('contenido')
<div class="container mt-4">
    <h2 class="text-center mb-4">Reporte de problemática</h2>

    <div class="mb-3">
        <label for="encargado" class="form-label">Encargado de área</label>
        <input type="text" class="form-control" id="encargado">
    </div>

    <div class="mb-3">
        <label for="correo" class="form-label">Correo del destinatario</label>
        <input type="email" class="form-control" id="correo">
    </div>

    <div class="mb-3">
        <label for="motivo" class="form-label">Ingrese el motivo del reporte</label>
        <textarea class="form-control" id="motivo" rows="5"></textarea>
    </div>

    <div class="mb-3">
        <label for="tipoReporte" class="form-label">Tipo de reporte</label>
        <select class="form-select" id="tipoReporte">
            <option value="problemática">Reporte de problemática</option>
            <option value="material">Reporte de material</option>
        </select>
    </div>

    <button onclick="generarPDF()" class="btn btn-danger">Generar PDF</button>
</div>
@endsection