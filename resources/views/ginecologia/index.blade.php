@extends('components.layouts.ginecologia.nav-ginecologia')

@section('contenido')

    <main class="mb-5">
  <div  style = "max-width: 720px;
    margin: 0 auto;">
      <div style = "display: flex; /* Activa Flexbox */
    align-items: center; /* Alinea verticalmente */
    justify-content: start;
    gap: 100px;">
        <img class="imagenes" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcShELmOXV-51dLxDdObIcBoWByLpU4wGkAaUQ&s" alt="referencia">
      <nav>
        <ul>
            <li><p>Expedientes</p>
            <p>Permite interactuar con la información relacionada a los pacientes</p></li>
            <li><p>Reporte</p>
              <p>Te permite realizar reportes de irregularidades en el área</p></li>
            <li><p>Material</p>
              <p>Permite interactuar con la información relacionada a los materiales quirurgicos</p></li>
            <li><p>Cirugia</p>
              <p>Permite interactuar con la información relacionada a las cirugias del hospital</p></li>
        </ul>
      </div>

  </main>

@endsection