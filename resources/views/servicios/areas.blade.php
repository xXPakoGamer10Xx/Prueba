{{-- resources/views/servicios/areas.blade.php --}}
@extends('components.layouts.servicios.nav-servicios') {{-- O tu layout principal --}}

@section('title', 'Gestión de Áreas y Encargados') {{-- Título apropiado --}}

@section('contenido')
    <main class="container my-5"> {{-- Mantén un contenedor si lo necesitas --}}
        @livewire('servicios.gestion-areas-encargados') {{-- ¡Aquí se llama al componente! --}}
    </main>
@endsection
