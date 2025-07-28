{{-- La ruta del layout debe coincidir con tu estructura de archivos.
     Basado en tu imagen, parece ser 'layouts.servicios.nav-servicios' o 'layouts.nav-servicios'.
     Ajusta esta línea si es necesario. --}}
@extends('components.layouts.servicios.nav-servicios')

@section('title', 'Gestión de Áreas y Encargados')

@section('contenido')
    
    <h1 class="mb-4 text-center">Gestión de Áreas y Encargados</h1>

    {{-- Aquí se renderiza el componente de Livewire.
         El nombre debe estar en minúsculas (kebab-case). --}}
    @livewire('servicios.gestion-areas-encargados')
    
@endsection