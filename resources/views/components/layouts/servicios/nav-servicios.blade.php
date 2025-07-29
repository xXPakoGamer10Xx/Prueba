<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Servicios Generales')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- AÑADIDO: Carga los estilos de Livewire --}}
    @livewireStyles
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg bg-rojo">
            <div class="container-fluid container">
                <a class="navbar-brand logo" href="{{ route('home') }}">
                    <img src="/img/logo_Mexico.svg" alt="Logo de México">
                </a>
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Iniciar sesión</a>
                @endauth
            </div>
        </nav>
    </header>
    <nav class="navbar navbar-expand-lg bg-cafe text-white">
        <div class="container-fluid container">

            <a class="navbar-brand logo px-5" href="{{ route('servicios.index') }}">
            <img src="/img/logo_IB.svg" alt="Logo Edo. Mex.">
            </a>

                <div class="navbar" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('servicios.index') }}">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('servicios.inventario') }}">Inventario</a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link" href="{{ route('servicios.mantenimiento') }}">Mantenimiento</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="{{ route('servicios.bajas') }}">Bajas</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="{{ route('servicios.areas') }}">Áreas</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    <main class="container my-4 flex-grow-1">
        {{-- Aquí se insertará el contenido de cada página --}}
        {{ $slot ?? '' }}
        @yield('contenido')
    </main>

    <footer class="bg-rojo mt-auto">
        <p class="text-center p-4 text-white mb-0">© {{ date('Y') }} Hospital Municipal de Chiconcuac | Todos los derechos reservados</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- AÑADIDO CRÍTICO: Carga de Chart.js ANTES de Livewire --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>

    {{-- AÑADIDO: Carga los scripts de Livewire --}}
    @livewireScripts
</body>
</html>
