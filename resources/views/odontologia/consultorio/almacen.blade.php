@extends('components.layouts.odontologia.navbar')

@section('contenido')

    <main class="container my-[4rem]">
        <h2 class="mb-3">Insumos</h2>

        @livewire('odontologia.consultorio.almacen-table')
    </main>

@endsection
