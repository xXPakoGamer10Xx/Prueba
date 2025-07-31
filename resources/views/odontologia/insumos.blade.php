@section('title', 'Insumos')

@extends('components.layouts.odontologia.navbar')

@section('contenido')

    <main class="container my-[4rem]">
        <div id="agregarInsumo" class="flex justify-between items-center mb-3">
            <h2>Insumos</h2>

                <div class="flex gap-3">
                    <!-- Boton para agregar insumo nuevo -->
                    <button
                        type="submit"
                        class="border-0 bg-custom-brown text-white font-semibold rounded-2 px-3 py-2 duration-250"
                        data-bs-toggle='modal' 
                        data-bs-target='#modalAgregarNuevoInsumo'
                    >Agregar Nuevo
                    </button>
                </div>

                <!-- Modal para agergar un nuevo insumo a consultorio e inventario -->
                @livewire('odontologia.add-new-insumo-modal', ['formulario' => 'insumos'])
        </div>
        <!-- Componente para mostrar insumos en consultorio -->
        @livewire('odontologia.insumos-table')
    </main>

@endsection