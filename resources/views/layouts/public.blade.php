<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'TeamSphere')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

      <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

    <!-- Theme Styles -->
    <style>
        :root {
            /* Dark Theme (Default) */
            --bg-primary: #111827; /* gray-900 */
            --bg-secondary: #1f2937; /* gray-800 */
            --bg-tertiary: #374151; /* gray-700 */
            --bg-card: rgba(31, 41, 55, 0.5); /* gray-800/50 */
            --bg-card-solid: #1f2937; /* gray-800 */
            --bg-accent: rgba(31, 41, 55, 0.95); /* gray-800/95 */
            --bg-hover: rgba(55, 65, 81, 0.4); /* gray-700/40 */

            --text-primary: #ffffff; /* white */
            --text-secondary: #d1d5db; /* gray-300 */
            --text-tertiary: #9ca3af; /* gray-400 */
            --text-muted: #6b7280; /* gray-500 */

            --border-primary: rgba(55, 65, 81, 0.5); /* gray-700/50 */
            --border-secondary: rgba(75, 85, 99, 0.3); /* gray-600/30 */
            --border-accent: rgba(55, 65, 81, 0.5); /* gray-700/50 */

            --accent-blue: #60a5fa; /* blue-400 */
            --accent-green: rgba(34, 197, 94, 0.8); /* green-900/80 */
            --accent-green-solid: #16a34a; /* green-600 */
            --accent-red: #ef4444; /* red-400 */
            --accent-yellow: #eab308; /* yellow-400 */
            --accent-cyan: #06b6d4; /* cyan-400 */
            --accent-amber: #f59e0b; /* amber-400 */

            --shadow-primary: rgba(0, 0, 0, 0.5);
            --backdrop-blur: blur(12px);
        }

        [data-theme="light"] {
            /* Light Theme */
            --bg-primary: #ffffff; /* white */
            --bg-secondary: #f9fafb; /* gray-50 */
            --bg-tertiary: #e5e7eb; /* gray-200 - increased contrast */
            --bg-card: rgba(255, 255, 255, 0.9); /* white/90 */
            --bg-card-solid: #ffffff; /* white */
            --bg-accent: rgba(249, 250, 251, 0.95); /* gray-50/95 */
            --bg-hover: rgba(243, 244, 246, 0.6); /* gray-100/60 */

            --text-primary: #111827; /* gray-900 */
            --text-secondary: #374151; /* gray-700 */
            --text-tertiary: #4b5563; /* gray-600 - increased contrast */
            --text-muted: #6b7280; /* gray-500 - increased contrast */

            --border-primary: rgba(229, 231, 235, 0.8); /* gray-200/80 */
            --border-secondary: rgba(156, 163, 175, 0.6); /* gray-400/60 - increased contrast */
            --border-accent: rgba(156, 163, 175, 0.3); /* gray-400/30 */

            --accent-blue: #3b82f6; /* blue-500 */
            --accent-green: rgba(34, 197, 94, 0.9); /* green-500/90 */
            --accent-green-solid: #16a34a; /* green-600 */
            --accent-red: #dc2626; /* red-600 */
            --accent-yellow: #ca8a04; /* yellow-600 */
            --accent-cyan: #0891b2; /* cyan-600 */
            --accent-amber: #d97706; /* amber-600 */

            --shadow-primary: rgba(0, 0, 0, 0.1);
            --backdrop-blur: blur(16px);
        }

        /* Smooth transitions for theme changes */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
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
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="antialiased min-h-screen pb-16 md:pb-8" style="background-color: var(--bg-primary); color: var(--text-primary);">
    <div class="py-8">
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

    @stack('scripts')
</body>
</html>