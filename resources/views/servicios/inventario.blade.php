@extends('components.layouts.servicios.nav-servicios')

@section('title', 'Gestión de Inventario de Servicios')

@section('contenido')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Mensajes de sesión --}}
                    @if (session()->has('message'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('message') }}
                        </div>
                    @endif
                    
                    {{--
                    Aquí llamamos a los nuevos componentes de Livewire.
                    Cada uno es independiente y maneja su propia lógica y vista.
                    Esto hace que el código sea mucho más limpio y fácil de mantener.
                    --}}

                    @livewire('servicios.gestionar-inventarios')

                    <hr class="my-8 border-t border-gray-200">

                    @livewire('servicios.gestionar-equipos')

                    <hr class="my-8 border-t border-gray-200">

                    @livewire('servicios.gestionar-garantias')

                </div>
            </div>
        </div>
    </div>
@endsection
