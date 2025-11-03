<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta ch                    window.location.reload();
                });
            }
            
            // PWA Install prompt      <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} (Beta)</title>

        <!-- PWA Meta Tags -->
        <meta name="application-name" content="TeamSphere Beta">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="TeamSphere">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="theme-color" content="#1f2937">
        <meta name="msapplication-TileColor" content="#1f2937">
        
        <!-- Manifest -->
        <link rel="manifest" href="/manifest.json">
        
        <!-- Apple Touch Icons -->
        <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.svg">
        <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.svg">

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

        <!-- Service Worker Registration -->
        <script>
            // Register service worker in all environments for PWA testing
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(registration) {
                            console.log('[PWA] Service Worker registered successfully:', registration.scope);
                            
                            // Check for updates every 60 seconds in beta
                            setInterval(function() {
                                registration.update();
                            }, 60000);
                            
                            // Listen for new service worker
                            registration.addEventListener('updatefound', function() {
                                const newWorker = registration.installing;
                                newWorker.addEventListener('statechange', function() {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        // New service worker available, show update notification
                                        if (confirm('Nova verzija aplikacije je dostupna! Želite li ažurirati?')) {
                                            newWorker.postMessage({ type: 'SKIP_WAITING' });
                                            window.location.reload();
                                        }
                                    }
                                });
                            });
                        })
                        .catch(function(error) {
                            console.log('[PWA] Service Worker registration failed:', error);
                        });
                    
                    // Reload page when new service worker takes control
                    navigator.serviceWorker.addEventListener('controllerchange', function() {
                        window.location.reload();
                    });
                });
            }
            @else
            // Unregister any existing service workers in development
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(function(registrations) {
                    for(let registration of registrations) {
                        registration.unregister().then(function(success) {
                            console.log('[PWA] Service Worker unregistered (development mode):', success);
                        });
                    }
                });
            }
            @endif
            
            // PWA Install prompt
            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                console.log('[PWA] Install prompt available');
                
                // Show install button or notification if needed
                // You can add custom UI here to prompt user to install
            });
            
            window.addEventListener('appinstalled', (e) => {
                console.log('[PWA] App installed successfully');
                deferredPrompt = null;
            });
        </script>

        @livewireScripts

        @stack('scripts')
    </body>
</html>
