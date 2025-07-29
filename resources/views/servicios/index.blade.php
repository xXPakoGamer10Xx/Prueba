@extends('components.layouts.servicios.nav-servicios')

@section('title', 'Resumen de Equipos') {{-- Puedes ajustar el título de la página aquí --}}

@section('contenido')
    <main class="container my-5"> {{-- Mantén el contenedor y el margen para el diseño --}}
        {{-- Aquí se renderiza el componente de Livewire con toda la funcionalidad --}}
        @livewire('servicios.dashboard-equipos') {{-- Llama a tu nuevo componente Livewire --}}
    </main>
@endsection
