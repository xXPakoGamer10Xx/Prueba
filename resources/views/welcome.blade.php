<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Incluye los metadatos, fuentes y CSS principales --}}
    @include('partials.head')
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
            <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
                {{-- Logo del Gobierno de México --}}
                <div>
                    <img src="{{ asset('img/logo_Mexico.svg') }}" alt="Logo del Gobierno de México" class="h-10 md:h-12">
                </div>

                {{-- Botones de acción --}}
                <div class="space-x-4">
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-sm font-semibold text-white border border-white rounded-md hover:bg-white hover:text-rojo transition-colors duration-300">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 text-sm font-semibold text-white bg-transparent border border-white rounded-md hover:bg-white hover:text-rojo transition-colors duration-300">
                        Registrarse
                    </a>
                </div>
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
        <footer class="bg-rojo text-white py-6">
            <div class="container mx-auto text-center text-sm">
                &copy; {{ date('Y') }} Hospital Municipal de Chiconcuac | Todos los derechos reservados.
            </div>
        </footer>

    </div>
</body>
</html>