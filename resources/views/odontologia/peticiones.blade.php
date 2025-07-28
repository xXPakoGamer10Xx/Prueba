@extends('components.layouts.odontologia.navbar')

@section('contenido')

    <main class="container my-[4rem]">
        <div class="mb-3">
            <h1>Peticiones</h1>

        </div>

        @livewire('odontologia.peticiones-table')
    </main>

@endsection
