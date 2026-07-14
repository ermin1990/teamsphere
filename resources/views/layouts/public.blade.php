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

    <!-- Theme Styles - iste boje/fontovi kao MojTurnir landing (welcome.blade.php) -->
    <style>
        :root {
            /* Dark Theme (Default) */
            --bg-primary: #121309;
            --bg-secondary: #1A1C10;
            --bg-tertiary: #262918;
            --bg-card: rgba(26, 28, 16, 0.55);
            --bg-card-solid: #1A1C10;
            --bg-accent: rgba(18, 19, 9, 0.95);
            --bg-hover: rgba(44, 47, 29, 0.5);

            --text-primary: #F4F2E6;
            --text-secondary: #C7C9B4;
            --text-tertiary: #A7AB90;
            --text-muted: #7C8069;

            --border-primary: rgba(44, 47, 29, 0.7);
            --border-secondary: rgba(44, 47, 29, 0.4);
            --border-accent: rgba(44, 47, 29, 0.6);

            --accent-blue: #D7FF3F; /* MojTurnir brand accent (chartreuse) - var name kept for compatibility */
            --accent-green: rgba(34, 197, 94, 0.8);
            --accent-green-solid: #16a34a;
            --accent-red: #ef4444;
            --accent-yellow: #eab308;
            --accent-cyan: #06b6d4;
            --accent-amber: #f59e0b;

            --shadow-primary: rgba(0, 0, 0, 0.6);
            --backdrop-blur: blur(12px);
        }

        [data-theme="light"] {
            /* Light Theme */
            --bg-primary: #F6F5EF;
            --bg-secondary: #EDEBDD;
            --bg-tertiary: #E2E0CE;
            --bg-card: rgba(255, 255, 255, 0.85);
            --bg-card-solid: #ffffff;
            --bg-accent: rgba(246, 245, 239, 0.95);
            --bg-hover: rgba(226, 224, 206, 0.5);

            --text-primary: #16180D;
            --text-secondary: #3A3D28;
            --text-tertiary: #5C6047;
            --text-muted: #7C8069;

            --border-primary: rgba(92, 96, 71, 0.25);
            --border-secondary: rgba(92, 96, 71, 0.4);
            --border-accent: rgba(92, 96, 71, 0.2);

            --accent-blue: #5C8A00; /* darkened chartreuse for legible text/borders on light bg */
            --accent-green: rgba(34, 197, 94, 0.9);
            --accent-green-solid: #16a34a;
            --accent-red: #dc2626;
            --accent-yellow: #ca8a04;
            --accent-cyan: #0891b2;
            --accent-amber: #d97706;

            --shadow-primary: rgba(0, 0, 0, 0.1);
            --backdrop-blur: blur(16px);
        }

        body {
            font-family: 'Manrope', ui-sans-serif, sans-serif;
        }
        h1, h2, h3, h4, .font-display {
            font-family: 'Unbounded', ui-sans-serif, sans-serif;
        }

        /* Smooth transitions for theme changes */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
        }

        /* Ensure clickable elements are not blocked */
        select, input, button, a {
            position: relative;
            z-index: 10;
            pointer-events: auto !important;
        }

        /* Theme toggle button */
        .theme-toggle {
            position: relative;
            width: 48px;
            height: 24px;
            border-radius: 12px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-primary);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            padding: 2px;
        }

        .theme-toggle:hover {
            background: var(--bg-secondary);
            transform: scale(1.05);
        }

        .theme-toggle-slider {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--text-primary);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        [data-theme="light"] .theme-toggle-slider {
            transform: translateX(22px);
        }

        /* Custom scrollbar for light theme */
        [data-theme="light"] ::-webkit-scrollbar {
            width: 8px;
        }

        [data-theme="light"] ::-webkit-scrollbar-track {
            background: var(--bg-tertiary);
        }

        [data-theme="light"] ::-webkit-scrollbar-thumb {
            background: var(--border-primary);
            border-radius: 4px;
        }

        [data-theme="light"] ::-webkit-scrollbar-thumb:hover {
            background: var(--text-tertiary);
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="antialiased min-h-screen pb-16 md:pb-8" style="background-color: var(--bg-primary); color: var(--text-primary);">
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

                    <!-- Theme Toggle -->
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium" style="color: var(--text-tertiary);">Tema</span>
                        <button id="theme-toggle" class="theme-toggle" aria-label="Toggle theme">
                            <div class="theme-toggle-slider">
                                <span id="theme-icon">🌙</span>
                            </div>
                        </button>
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

    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const html = document.documentElement;

            // Load saved theme
            const savedTheme = localStorage.getItem('theme') || 'dark';
            html.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);

            // Theme toggle handler
            themeToggle.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(newTheme);

                // Add smooth transition effect
                document.body.style.transition = 'background-color 0.5s ease, color 0.5s ease';
                setTimeout(() => {
                    document.body.style.transition = '';
                }, 500);
            });

            function updateThemeIcon(theme) {
                themeIcon.textContent = theme === 'dark' ? '🌙' : '☀️';
            }
        });
    </script>

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