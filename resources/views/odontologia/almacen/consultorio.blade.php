@extends('components.layouts.odontologia.almacen.navbar')

@section('contenido')

    <main class="container my-[4rem]">
        <div id="agregarInsumo" class="flex justify-between items-center mb-3">
            <h2>Insumos</h2>
        </div>

        <!-- Componente para mostrar insumos en consultorio -->
        @livewire('odontologia.consultorio.insumos-table')
    </main>

@endsection