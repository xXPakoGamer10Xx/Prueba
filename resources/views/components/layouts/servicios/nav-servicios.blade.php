<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Servicios Generales</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg bg-rojo">
            <div class="container-fluid container">
                <a class="navbar-brand logo" href="{{ route('home') }}">
                    <img src="/img/logo_Mexico.svg" alt="Logo de México">
                </a>
                @auth
                            {{-- Si el usuario está autenticado, muestra el botón de Cerrar Sesión --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf {{-- Token CSRF para seguridad --}}
                                <button
                                    type="submit"
                                    class="inline-block px-5 py-1.5 text-white border hover:border-white rounded-sm text-sm leading-normal duration-250 cursor-pointer hover:opacity-75"
                                >
                                    Cerrar Sesión
                                </button>
                            </form>
                        @else
                            {{-- Si el usuario no está autenticado, muestra los botones de Iniciar Sesión y Registrar --}}
                            <a
                                href="{{ route('login') }}"
                                class="inline-block px-5 py-1.5 text-white border hover:border-white rounded-sm text-sm leading-normal duration-250"
                            >
                                Iniciar sesión
                            </a>

                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-block px-5 py-1.5 border-white hover:border-gray-500 border text-white rounded-sm text-sm leading-normal duration-250">
                                    Register
                                </a>
                            @endif
                        @endauth
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
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
                        <li class="nav-item">
                        <a class="nav-link" href="{{ route('servicios.encargados') }}">Encargados</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    @yield('contenido')

    <footer class="bg-rojo mt-auto">
        <p class="text-center p-4 text-white mb-0">© 2025 Hospital Municipal de Chiconcuac | Todos los derechos reservados</p>
    </footer>
</body>
</html>
