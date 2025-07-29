<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white antialiased">
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg bg-rojo">
            <div class="container-fluid container">
                <a class="navbar-brand logo" href="{{ route('home') }}">
                    <img src="/img/logo_Mexico.svg" alt="Logo de México">
                </a>
            </div>
        </nav>
    </header>

        <div class="flex-grow container mx-auto py-16 px-6 flex flex-col items-center justify-center">
            <div class="w-full max-w-sm flex flex-col gap-6">
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                {{ $slot }}
            </div>
        </div>
        @fluxScripts

    <footer class="bg-rojo mt-auto">
        <p class="text-center p-4 text-white mb-0">© 2025 Hospital Municipal de Chiconcuac | Todos los derechos reservados</p>
    </footer>
    </body>
</html> 