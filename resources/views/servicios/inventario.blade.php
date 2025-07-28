@extends('components.layouts.servicios.nav-servicios')

@section('title', 'Inventario de Equipos')

@section('contenido')
    
    {{-- Aquí se renderiza el componente de Livewire con toda la funcionalidad --}}
    @livewire('servicios.gestion-inventario')
    
@endsection
