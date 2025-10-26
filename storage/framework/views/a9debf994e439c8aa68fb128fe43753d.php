<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans antialiased bg-gray-900 text-white min-h-screen">
        <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-900/95 to-gray-800">
            <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Page Heading -->
            <?php if(isset($header)): ?>
                <header class="bg-gray-800/50 backdrop-blur-xl border-b border-gray-700/50 shadow-2xl">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <?php echo e($header); ?>

                    </div>
                </header>
            <?php endif; ?>

            <!-- Page Content -->
            <main class="relative">
                <!-- Background Effects -->
                <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-900/95 to-gray-800"></div>

                <!-- Floating Elements -->
                <div class="hidden md:block absolute top-20 left-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="hidden md:block absolute bottom-20 right-10 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: -3s;"></div>
                <div class="hidden md:block absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: -6s;"></div>

                <div class="relative z-10">
                    <?php if (! empty(trim($__env->yieldContent('content')))): ?>
                        <?php echo $__env->yieldContent('content'); ?>
                    <?php else: ?>
                        <?php if(isset($slot)): ?>
                            <?php echo e($slot); ?>

                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>

        <!-- Service Worker Registration - Disabled for development -->
        <script>
            // Unregister any existing service workers to prevent caching issues
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(function(registrations) {
                    for(let registration of registrations) {
                        registration.unregister().then(function(success) {
                            console.log('Service Worker unregistered:', success);
                        });
                    }
                });
            }
            
            // Only register in production
            <?php if(app()->environment('production')): ?>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(registration) {
                            console.log('Service Worker registered successfully:', registration.scope);
                        })
                        .catch(function(error) {
                            console.log('Service Worker registration failed:', error);
                        });
                });
            }
            <?php endif; ?>
        </script>

        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>


        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html>
<?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/layouts/app.blade.php ENDPATH**/ ?>