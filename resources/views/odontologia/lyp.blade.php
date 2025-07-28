@extends('components.layouts.odontologia.navbar')

@section('contenido')

    <main class="container my-[4rem]">
        <div class="grid grid-rows-1 grid-cols-2 gap-[3rem]">
          <section class="">
            <div class="flex justify-between items-center mb-3">
              <h2 class="mb-3">Laboratorios</h2>
              
              <button 
              type="button"
              class="border-0 bg-custom-brown text-white font-semibold rounded-2 px-3 py-2 duration-250"
              data-bs-toggle='modal' 
              data-bs-target='#modalAgregarLaboratorio'
              >
                Agregar Nuevo
              </button>
            </div>

            @livewire('odontologia.add-new-laboratorio-modal')
            @livewire('odontologia.laboratorios-table')
          </section>

          <section class="">
            <div class="flex justify-between items-center mb-3">

              <h2 class="mb-3">Presentaciones</h2>
              
              <button 
              type="button"
              class="border-0 bg-custom-brown text-white font-semibold rounded-2 px-3 py-2 duration-250"
              data-bs-toggle='modal' 
              data-bs-target='#modalAgregarPresentacion'
              >
                Agregar Nuevo
              </button>
            </div>

            @livewire('odontologia.add-new-presentacion-modal')
            @livewire('odontologia.presentaciones-table')
          </section>
        </div>

    </main>

@endsection
