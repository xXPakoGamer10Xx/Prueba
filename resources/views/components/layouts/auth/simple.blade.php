<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased">
        <header class="bg-rojo shadow-lg">
            <nav class="h-[4.125rem] container mx-auto flex justify-between items-center">
                {{-- Logo del Gobierno de México --}}
                <div class="logo">
                    <a href="./">
                        <img src="{{ asset('img/logo_Mexico.svg') }}" alt="Logo del Gobierno de México" class="">
                    </a>
                </div>
            </nav>
        </header>

        <div class="flex-grow container mx-auto py-16 px-6 flex flex-col items-center justify-center">
            <div class="w-full max-w-md flex flex-col gap-6">
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
