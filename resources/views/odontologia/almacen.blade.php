@section('title', 'Almacén')

@extends('components.layouts.odontologia.navbar')

@section('contenido')

    <main class="container my-[4rem]">
        <div class="flex justify-between items-center mb-3">
            <h2 class="mb-3">Insumos</h2>

            @if(Auth::user()->rol == 'odontologia_almacen')
                <div class="flex gap-3">
                    <!-- Boton para agregar insumo existente -->
                    <button 
                        type="button"
                        class="border-0 bg-custom-brown text-white font-semibold rounded-2 px-3 py-2 duration-250"
                        data-bs-toggle='modal' 
                        data-bs-target='#modalAgregarInsumo'
                    >
                        Agregar Existente
                    </button>

                    <!-- Boton para agregar insumo nuevo -->
                    <button
                        type="submit"
                        class="border-0 bg-custom-brown text-white font-semibold rounded-2 px-3 py-2 duration-250"
                        data-bs-toggle='modal' 
                        data-bs-target='#modalAgregarNuevoInsumo'
                    >Agregar Nuevo
                    </button>
                </div>

                <!-- Modal para agregar insumo existente -->
                @livewire('odontologia.add-existing-insumo-modal', ['formulario' => 'almacen'])
                <!-- Modal para agregar un nuevo insumo a almacen e inventario -->
                @livewire('odontologia.add-new-insumo-modal', ['formulario' => 'almacen'])
            @endif
        </div>

        <!-- Modal para pedir insumo si rol es odontologia_consultorio -->
        @livewire('odontologia.add-new-peticion-modal')
        @livewire('odontologia.almacen-table')
    </main>

@endsection
