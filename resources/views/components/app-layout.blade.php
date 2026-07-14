@props(['header'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="/icons/icon-192.svg">

        <title>{{ config('app.name', 'MojTurnir') }}</title>

    <!-- PWA manifest -->
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#4f46e5">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-900 text-white min-h-screen">
        <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-900/95 to-gray-800">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if(isset($header))
                <header class="bg-gray-800/50 backdrop-blur-xl border-b border-gray-700/50 shadow-2xl">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="relative">
                <!-- Background Effects -->
                <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-900/95 to-gray-800"></div>

                <!-- Floating Elements -->
                <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl animate-pulse z-0"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse z-0" style="animation-delay: -3s;"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl animate-pulse z-0" style="animation-delay: -6s;"></div>

                <div class="relative z-10">
                    {{ $slot }}
                </div>
            </main>
        </div>

        {{-- Global toast helper (bottom-center) to ensure consistent placement --}}
        <style>
            /* If any element still uses the old utility classes top-4 right-4, force it to bottom-center.
               This is a safe override to handle cached/legacy inline toasts without changing markup everywhere. */
            .top-4.right-4.fixed {
                bottom: 1.5rem !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                top: auto !important;
                right: auto !important;
            }
        </style>
        <script>
            window.showNotification = function(message, type = 'info') {
                let container = document.getElementById('toast-container-bottom-center');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'toast-container-bottom-center';
                    container.className = 'fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 flex flex-col items-center gap-3 pointer-events-none';
                    document.body.appendChild(container);
                }

                const notification = document.createElement('div');
                notification.className = `pointer-events-auto max-w-xl w-full px-6 py-3 rounded-lg shadow-lg transition-opacity duration-300 ease-out ${
                    type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                } text-white opacity-0`;
                notification.textContent = message;

                container.appendChild(notification);
                requestAnimationFrame(() => { notification.classList.remove('opacity-0'); notification.classList.add('opacity-100'); });

                setTimeout(() => {
                    notification.classList.remove('opacity-100');
                    notification.classList.add('opacity-0');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            };
        </script>
        <script>
            // Register service worker to enable installability. The service worker
            // is intentionally network-only (does not cache) per project request.
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(reg) {
                            console.log('Service worker registered:', reg.scope);
                        }).catch(function(err) {
                            console.warn('Service worker registration failed:', err);
                        });
                });
            }
        </script>
            </main>
        </div>
    </body>
</html>