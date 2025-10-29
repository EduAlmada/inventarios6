<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/3er logo Vodelem.png') }}">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">

            <aside class="w-64 bg-gray-800 text-white min-h-screen p-4 flex flex-col justify-between">
                <nav>
                    @if (Auth::check() && Auth::user()->is_admin)
                        <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-md">Gestión de Usuarios</a>
                        <a href="{{ route('articulos.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-md">Artículos</a>
                        <a href="{{ route('tipos.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-md">Tipos de Actividad</a>
                        <a href="{{ route('depositos.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-md">Depósitos</a>
                        <a href="{{ route('zonas.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-md">Zonas</a>
                        <a href="{{ route('monitor.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-md">Monitor de Pedidos</a>
                    @endif
                    <a href="{{ route('actividades.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-md">Actividades</a>
                    <a href="{{ route('stock.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-md">Stock</a>
                    <a href="{{ route('notas.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded-md">Pedidos</a>
                </nav>
            </aside>

            <div class="flex-1">
                <livewire:layout.navigation />

                @if (isset($header))
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>