<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased">
        <header class="w-full bg-custom-red py-4">
                <nav class="container m-auto flex items-center justify-between gap-4">
                    <a href="./">
                        <img src="/img/logo_Mexico.svg" alt="">
                    </a>
                </nav>
        </header>

        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts

        <footer class="w-full bg-custom-red py-8">
            <p class="text-center text-white">© 2025 Hospital Municipal de Chiconcuac | Todos los derechos reservados</p>
        </footer>
    </body>
</html>
