<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    {{-- Incluye los metadatos, fuentes y CSS principales --}}
    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Asegura que el color de fondo del body sea blanco/claro */
        body {
            background-color: #ffffff;
        }
    </style>
</head>
<body class="antialiased">
    <div class="flex flex-col min-h-screen">

        <!-- Encabezado con los colores del Gobierno -->
        <header class="bg-rojo shadow-lg">
            <nav class="h-[4.125rem] container mx-auto flex justify-between items-center">
                {{-- Logo del Gobierno de México --}}
                <div class="logo">
                    <img src="{{ asset('img/logo_Mexico.svg') }}" alt="Logo del Gobierno de México" class="">
                </div>
                
                @auth
                    <div class="flex gap-2">
                        @if(Auth::user()->rol == 'encargado_ginecologia')
                            <a href="{{ route('ginecologia.index') }}"
                            class="px-[10px] py-[6px] text-decoration-none text-sm font-semibold text-white border border-white rounded-md hover:opacity-75 hover:text-custom-red duration-300">
                                Ginecología
                            </a>
                        @elseif (Auth::user()->rol == 'odontologia_consultorio' || Auth::user()->rol == 'odontologia_almacen')
                            @if (Auth::user()->rol == 'odontologia_consultorio')
                                <a href="{{ route('odontologia.consultorio.index') }}"
                            @else
                                <a href="{{ route('odontologia.almacen.index') }}"
                            @endif
                            class="px-[10px] py-[6px] text-decoration-none text-sm font-semibold text-white border border-white rounded-md hover:opacity-75 hover:text-custom-red duration-300">
                                Odontología - @if (Auth::user()->rol == 'odontologia_consultorio') Consultorio @else Almacén @endif
                            </a>
                        @elseif (Auth::user()->rol == 'encargado_servicios')
                            <a href="{{ route('servicios.index') }}"
                            class="px-[10px] py-[6px] text-decoration-none text-sm font-semibold text-white border border-white rounded-md hover:opacity-75 hover:text-custom-red duration-300">
                                Servicios Generales
                            </a>
                        @endif
                    </div>
                @else
                    <div class="flex gap-2">
                        <a href="{{ route('login') }}"
                        class="px-[10px] py-[6px] text-decoration-none text-sm font-semibold text-white border border-white rounded-md hover:opacity-75 hover:text-custom-red duration-300">
                            Iniciar sesión
                        </a>
                        <a href="{{ route('register') }}"
                        class="px-[10px] py-[6px] text-decoration-none text-sm font-semibold text-white border border-white rounded-md hover:opacity-75 hover:text-custom-red duration-300">
                            Registrarse
                        </a>
                    </div>
                @endauth
            </nav>
        </header>

        <!-- Contenido Principal -->
        <main class="flex-grow container mx-auto py-16 px-6">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800">
                    Hospital Municipal de Chiconcuac
                </h1>
                <p class="mt-4 text-base md:text-lg text-gray-600 max-w-3xl mx-auto">
                    Bienvenido al sistema de gestión integral del Hospital Municipal. Por favor, inicie sesión o regístrese para acceder a los servicios correspondientes.
                </p>
            </div>
        </main>

        <!-- Pie de Página con los colores del Gobierno -->
        <footer class="bg-rojo mt-auto">
            <p class="text-center p-4 text-white mb-0">© {{ date('Y') }} Hospital Municipal de Chiconcuac | Todos los derechos reservados</p>
        </footer>

    </div>
</body>
</html>