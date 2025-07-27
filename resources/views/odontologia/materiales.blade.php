@extends('components.layouts.odontologia.navbar')

@section('contenido')

    <main class="container my-[4rem]">
        <div class="flex justify-between items-center mb-3">
            <h2>Materiales Externos</h2>
    
            <div>
              <!-- Boton para agregar insumo -->
              <button 
                class="border-0 bg-custom-brown text-white font-semibold rounded-2 px-3 py-2 duration-250"
                data-bs-toggle='modal' 
                data-bs-target='#modalAgregarMaterial'
              >
                Agregar Nuevo
              </button>
            </div>
        </div>

        <!-- Modal para agregar material -->
        @livewire('odontologia.add-new-material-modal')
        <!-- Componente para mostrar materiales externos registrados -->
        @livewire('odontologia.materiales-externos-table')
    </main>

@endsection
