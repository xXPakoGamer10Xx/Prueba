@extends('components.layouts.odontologia.navbar')

@section('contenido')

    <main class="container my-[4rem]">
        <div id="agregarInsumo" class="flex justify-between items-center mb-3">
            <h2>Insumos</h2>

            <div class="flex gap-3">
                <!-- Boton para agregar insumo existente -->
                <button 
                    type="button"
                    class="border-0 bg-custom-brown text-white font-semibold rounded-2 px-3 py-2"
                    data-bs-toggle='modal' 
                    data-bs-target='#modalAgregarInsumo'
                >
                    Agregar Existente
                </button>

                <!-- Boton para agregar insumo nuevo -->
                <button
                    type="submit"
                    class="border-0 bg-custom-brown text-white font-semibold rounded-2 px-3 py-2"
                    data-bs-toggle='modal' 
                    data-bs-target='#modalAgregarNuevoInsumo'
                >Agregar Nuevo
                </button>
            </div>
        </div>

        <!-- Modal para agregar insumo existente -->
        @livewire('odontologia.consultorio.add-existing-insumo-modal')
        <!-- Modal para agergar un nuevo insumo a consultorio e inventario -->
        @livewire('odontologia.consultorio.add-new-insumo-modal')
        <!-- Componente para mostrar insumos en consultorio -->
        @livewire('odontologia.consultorio.insumos-table')
    </main>

@endsection