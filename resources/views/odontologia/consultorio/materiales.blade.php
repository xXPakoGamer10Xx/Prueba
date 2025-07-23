@extends('components.layouts.odontologia.consultorio.navbar')

@section('contenido')

    <main class="container my-[4rem]">
        <div class="flex justify-between items-center mb-3">
            <h2>Materiales Externos</h2>
    
            <div>
              <!-- Boton para agregar insumo -->
              <button 
                class="border-0 bg-cafe text-white fw-semibold rounded-2 px-3 py-2"
                data-bs-toggle='modal' 
                data-bs-target='#modalAgregarMaterial'
              >
                Agregar Nuevo
              </button>
            </div>
        </div>

        @livewire('odontologia.consultorio.materiales-externos-table')
    </main>

@endsection
