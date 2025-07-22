<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Odontología | Consultorio</title>
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

            <a class="navbar-brand logo px-5" href="{{ route('odontologia.consultorio.index') }}">
              <img src="/img/logo_IB.svg" alt="Logo Edo. Mex.">
            </a>

                <div class="navbar" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item @if(request()->routeIs('odontologia.consultorio.index')) font-bold @endif">
                            <a class="nav-link hover:opacity-75 duration-250" href="{{ route('odontologia.consultorio.index') }}">Consultorio</a>
                        </li>
                        <li class="nav-item @if(request()->routeIs('odontologia.consultorio.materiales')) font-bold @endif">
                            <a class="nav-link hover:opacity-75 duration-250" href="{{ route('odontologia.consultorio.materiales') }}">Materiales Externos</a>
                        </li>
                        <li class="nav-item @if(request()->routeIs('odontologia.consultorio.almacen')) font-bold @endif">
                            <a class="nav-link hover:opacity-75 duration-250" href="{{ route('odontologia.consultorio.almacen') }}">Almacén</a>
                        </li>
                        <li class="nav-item @if(request()->routeIs('odontologia.consultorio.insumos')) font-bold @endif">
                            <a class="nav-link hover:opacity-75 duration-250" href="{{ route('odontologia.consultorio.insumos') }}">Insumos</a>
                        </li>
                        <li class="nav-item @if(request()->routeIs('odontologia.consultorio.peticiones')) font-bold @endif">
                            <a class="nav-link hover:opacity-75 duration-250" href="{{ route('odontologia.consultorio.peticiones') }}">Peticiones</a>
                        </li>
                        <li class="nav-item @if(request()->routeIs('odontologia.consultorio.lyp')) font-bold @endif">
                            <a class="nav-link hover:opacity-75 duration-250" href="{{ route('odontologia.consultorio.lyp') }}">Laboratorios y Presentaciones</a>
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
