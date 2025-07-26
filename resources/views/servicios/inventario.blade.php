<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Inventario de Servicios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
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
</x-app-layout>
