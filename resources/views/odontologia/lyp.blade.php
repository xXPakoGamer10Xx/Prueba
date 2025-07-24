@extends('components.layouts.odontologia.navbar')

@section('contenido')

    <main class="container my-[4rem]">
        <div class="grid grid-rows-1 grid-cols-2 gap-[3rem]">
          <section class="">
            <h2 class="mb-3">Laboratorios</h2>
            @livewire('odontologia.laboratorios-table')
          </section>

          <section class="">
            <h2 class="mb-3">Presentaciones</h2>
            @livewire('odontologia.presentaciones-table')
          </section>
        </div>

    </main>

@endsection
