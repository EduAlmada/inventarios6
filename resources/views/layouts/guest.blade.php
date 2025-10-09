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
        <div class="min-h-screen bg-violet-100 flex flex-col">
            
            <nav class="bg-white shadow-md">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    
                    <a href="/">
                        <x-application-logo class="w-12 h-12 fill-current text-gray-500" />
                    </a>

                    <div class="flex items-center space-x-4">
                        <a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-900 font-medium">Home</a>
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium">Login</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900 font-medium">Registro</a>
                        @endif
                    </div>
                </div>
            </nav>

            <main class="flex-1 flex flex-col items-center justify-center">
                <div class="w-full sm:max-w-md px-6 py-4 bg-blue-200 shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>