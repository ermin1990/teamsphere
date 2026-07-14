<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- PWA Configuration -->
        <link rel="manifest" href="/manifest.webmanifest">
        <meta name="theme-color" content="#1e40af">
        <link rel="apple-touch-icon" href="/icons/icon-192.svg">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <title>{{ config('app.name', 'MojTurnir') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-900 text-white min-h-screen">
        <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-900/95 to-gray-800">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gray-800/50 backdrop-blur-xl border-b border-gray-700/50 shadow-2xl">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="relative">
                <!-- Background Effects -->
                <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-900/95 to-gray-800"></div>

                <!-- Floating Elements -->
                <div class="hidden md:block absolute top-20 left-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="hidden md:block absolute bottom-20 right-10 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: -3s;"></div>
                <div class="hidden md:block absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: -6s;"></div>

                <div class="relative z-10 p-2">
                    @hasSection('content')
                        @yield('content')
                    @else
                        @if(isset($slot))
                            {{ $slot }}
                        @endif
                    @endif
                </div>
            </main>
        </div>

        <!-- PWA Install Prompt -->
        <x-pwa-install-prompt />

        <!-- Service Worker Registration -->
        <script>
            // Register Service Worker for PWA
            if ('serviceWorker' in navigator) {
                console.log('PWA: Service Worker supported');
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(registration) {
                            console.log('PWA: Service Worker registered successfully:', registration.scope);
                        })
                        .catch(function(error) {
                            console.log('PWA: Service Worker registration failed:', error);
                        });
                });
            } else {
                console.log('PWA: Service Worker not supported');
            }
        </script>

        @livewireScripts

        @stack('scripts')
    </body>
</html>
