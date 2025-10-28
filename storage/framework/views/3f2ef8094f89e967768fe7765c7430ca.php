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
    <body class="bg-gray-900 text-white min-h-screen">
        <!-- Language Switcher -->
        <div class="absolute top-6 right-6 z-10 flex items-center space-x-2">
            <a href="<?php echo e(route('locale', ['locale' => 'en'])); ?>" class="text-gray-300 hover:text-white font-medium transition-colors <?php echo e(app()->getLocale() === 'en' ? 'text-blue-400' : ''); ?>">EN</a>
            <span class="text-gray-600">|</span>
            <a href="<?php echo e(route('locale', ['locale' => 'bs'])); ?>" class="text-gray-300 hover:text-white font-medium transition-colors <?php echo e(app()->getLocale() === 'bs' ? 'text-blue-400' : ''); ?>">BS</a>
        </div>

        <div class="min-h-screen flex flex-col items-center justify-center px-6 py-12">
            <!-- Background Effects -->
            <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-900/95 to-gray-800"></div>

            <!-- Floating Elements -->
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: -3s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: -6s;"></div>

            <!-- Logo -->
            <div class="relative z-10 mb-8">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-2xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Team Sphere</span>
                </a>
            </div>

            <!-- Auth Card -->
            <div class="relative z-10 w-full max-w-md">
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-8 border border-gray-700/50 shadow-2xl">
                    <?php echo e($slot); ?>

                </div>
            </div>
        </div>
    </body>
</html>
<?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/layouts/guest.blade.php ENDPATH**/ ?>