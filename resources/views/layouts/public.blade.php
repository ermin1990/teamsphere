<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Configuration -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#121309">
    <link rel="apple-touch-icon" href="/icons/icon-192.svg">

    <title>@yield('title', 'MojTurnir')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=unbounded:600,700|manrope:400,500,600,700&display=swap" rel="stylesheet" />

      <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

    <!-- Theme Styles - kampanja paleta (skoro-crna + ljubicasti sjaj + periwinkle akcent) -->
    <style>
        :root {
            /* Dark Theme (Default) */
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

            --accent-blue: #B4C0FF; /* periwinkle/lavender - kampanja akcent */
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

        body {
            font-family: 'Manrope', ui-sans-serif, sans-serif;
        }
        h1, h2, h3, h4, .font-display {
            font-family: 'Unbounded', ui-sans-serif, sans-serif;
        }

        /* Ensure clickable elements are not blocked */
        select, input, button, a {
            position: relative;
            z-index: 10;
            pointer-events: auto !important;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="antialiased min-h-screen pb-16 md:pb-8" style="background-color: var(--bg-primary); background-image: var(--glow-purple); background-attachment: fixed; color: var(--text-primary);">
    <div class="py-8 p-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Menu (Desktop only) -->
            <nav class="hidden md:block rounded-2xl p-4 mb-6 shadow-xl border" style="background: var(--bg-card); backdrop-filter: var(--backdrop-blur); border-color: var(--border-primary); box-shadow: 0 10px 25px var(--shadow-primary);">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6 md:space-x-8">
                        <a href="{{ route('home') }}" class="transition-colors text-sm md:text-base font-medium hover:text-blue-400" style="color: var(--text-secondary);">
                            🏠 Home
                        </a>
                        <a href="{{ route('public.leagues.index') }}" class="transition-colors text-sm md:text-base font-medium hover:text-blue-400 {{ request()->routeIs('public.leagues*') ? 'font-semibold' : '' }}" style="color: {{ request()->routeIs('public.leagues*') ? 'var(--accent-blue)' : 'var(--text-secondary)' }};">
                            🏆 Takmičenja
                        </a>
                        <a href="{{ route('projector.builder') }}" class="transition-colors text-sm md:text-base font-medium hover:text-blue-400 {{ request()->routeIs('projector*') ? 'font-semibold' : '' }}" style="color: {{ request()->routeIs('projector*') ? 'var(--accent-blue)' : 'var(--text-secondary)' }};">
                            🎬 Projektor
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            @yield('content')
        </div>
    </div>

    <!-- Mobile Navigation Menu (Fixed Bottom) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 border-t shadow-2xl z-50" style="background: var(--bg-accent); backdrop-filter: var(--backdrop-blur); border-color: var(--border-primary); box-shadow: 0 -4px 20px var(--shadow-primary);">
        <div class="flex items-center justify-between py-3 px-4 w-full">
            <a href="{{ route('home') }}" class="flex flex-col items-center transition-colors text-xs flex-1 hover:text-blue-400" style="color: var(--text-secondary);">
                <span class="text-lg">🏠</span>
                <span class="mt-1">Home</span>
            </a>
            <a href="{{ route('public.leagues.index') }}" class="flex flex-col items-center transition-colors text-xs flex-1 hover:text-blue-400 {{ request()->routeIs('public.leagues*') ? 'text-blue-400' : '' }}" style="color: {{ request()->routeIs('public.leagues*') ? 'var(--accent-blue)' : 'var(--text-secondary)' }};">
                <span class="text-lg">🏆</span>
                <span class="mt-1">Takmičenja</span>
            </a>
            <a href="{{ route('projector.builder') }}" class="flex flex-col items-center transition-colors text-xs flex-1 hover:text-blue-400 {{ request()->routeIs('projector*') ? 'text-blue-400' : '' }}" style="color: {{ request()->routeIs('projector*') ? 'var(--accent-blue)' : 'var(--text-secondary)' }};">
                <span class="text-lg">🎬</span>
                <span class="mt-1">Projektor</span>
            </a>
        </div>
    </nav>

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

    @stack('scripts')
</body>
</html>