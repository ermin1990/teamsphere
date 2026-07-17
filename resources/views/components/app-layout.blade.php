@props(['header'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">

        <title>{{ config('app.name', 'MojTurnir') }}</title>

        {{-- CSS custom properties shared with resources/views/layouts/public.blade.php
             (dark values only - this shell has no light/dark toggle). --}}
        <style>
            [x-cloak] { display: none !important; }
            :root {
                --bg-primary: #0B0B12;
                --bg-secondary: #14141F;
                --bg-tertiary: #1F1F30;
                --bg-card: rgba(20, 20, 31, 0.7);
                --bg-card-solid: #14141F;
                --bg-accent: rgba(11, 11, 18, 0.95);
                --bg-hover: rgba(31, 31, 48, 0.6);

                --text-primary: #F6F6FB;
                --text-secondary: #CDCEDE;
                --text-tertiary: #9C9EB5;
                --text-muted: #6E7086;

                --border-primary: rgba(148, 130, 255, 0.16);
                --border-secondary: rgba(255, 255, 255, 0.07);
                --border-accent: rgba(148, 130, 255, 0.3);

                --accent-blue: #B4C0FF;
                --accent-green: rgba(34, 197, 94, 0.85);
                --accent-green-solid: #16a34a;
                --accent-red: #f87171;
                --accent-yellow: #eab308;
                --accent-cyan: #22d3ee;
                --accent-amber: #f59e0b;

                --shadow-primary: rgba(0, 0, 0, 0.65);
                --backdrop-blur: blur(12px);
                --glow-purple: radial-gradient(ellipse 900px 700px at 88% -5%, rgba(109, 40, 217, 0.35), transparent 60%);
            }
        </style>

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

        <x-pwa-install-prompt />

        @stack('scripts')
    </body>
</html>