<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Hospital Municipal de Chiconcuac</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <!-- Google fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-base bg-white flex items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full bg-custom-red py-4">
            @if (Route::has('login'))
                <nav class="container m-auto flex items-center justify-between gap-4">
                    <img src="/img/logo_Mexico.svg" alt="Logo de México">

                    <div>
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
                    </div>
                </nav>
            @endif
        </header>

        <div class="container flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="flex w-full flex-col">
                <h1 class="font-bold text-3xl">Hospital Municipal de Chiconcuac</h1>
                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Ipsam repellendus, quo cumque neque aperiam iusto temporibus praesentium, sequi voluptatibus, magni accusantium debitis delectus reiciendis fugit nobis veritatis quis. Pariatur, voluptatum.</p>
            </main>
        </div>


        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif

        <!-- Footer -->
        <footer class="w-full bg-custom-red py-8">
            <p class="text-center text-white">© 2025 Hospital Municipal de Chiconcuac | Todos los derechos reservados</p>
        </footer>
    </body>
</html>
