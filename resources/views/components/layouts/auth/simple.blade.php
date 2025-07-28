<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased">
        <header class="w-full bg-custom-red py-0.01">
                <nav class="container m-auto flex items-center justify-between gap-4">
                    <a href="./">
                        <img src="/img/logo_Mexico.svg" alt="Logo del gobierno del Méxco">
                    </a>
                </nav>
        </header>

        <div class="flex-grow container mx-auto py-16 px-6 flex flex-col items-center justify-center">
            <div class="w-full max-w-md flex flex-col gap-6">
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                {{ $slot }}
            </div>
        </div>
        @fluxScripts

        <footer class="w-full bg-custom-red py-3">
            <p class="text-center text-white">© 2025 Hospital Municipal de Chiconcuac | Todos los derechos reservados</p>
        </footer>
    </body>
</html>
