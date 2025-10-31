<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-900 text-white">
        <!-- Simple Hero -->
        <section class="min-h-screen flex items-center justify-center px-6">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Logo -->
                <div class="flex items-center justify-center space-x-3 mb-8">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Team Sphere</span>
                </div>

                <!-- Headline -->
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black mb-6">
                    <span class="text-white">Upravljanje Sportskim</span><br>
                    <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Timovima i Ligama</span>
                </h1>

                <!-- Description -->
                <p class="text-xl text-gray-400 mb-12 max-w-2xl mx-auto">
                    Platforma za organizaciju turnira, praćenje rezultata i upravljanje sportskim organizacijama.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 px-10 py-4 rounded-xl text-white font-semibold text-lg transition-all">
                            Počni Besplatno
                        </a>
                    @endif
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="border-2 border-gray-600 hover:border-gray-500 px-10 py-4 rounded-xl text-gray-300 hover:text-white font-semibold text-lg transition-all">
                            Prijavi se
                        </a>
                    @endif
                </div>

                <!-- Quick Access Banner -->
                <div class="mt-16">
                    <div class="max-w-4xl mx-auto">
                        <div class="text-center mb-6">
                            <h2 class="text-2xl font-bold text-white mb-2">Istraži Takmičenja</h2>
                            <p class="text-gray-400">Pregledaj lige i turnire</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 max-w-md mx-auto">
                            <!-- View Competitions -->
                            <a href="{{ route('public.leagues.index') }}" class="block bg-gradient-to-r from-blue-500/10 to-purple-500/10 border-2 border-blue-500/30 rounded-2xl p-6 hover:border-blue-500/50 transition-all group">
                                <div class="flex items-center justify-center space-x-4">
                                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-blue-400 font-bold text-lg">🏆 Takmičenja</div>
                                        <div class="text-white text-sm font-medium">Pregledaj lige i turnire</div>
                                        <div class="text-gray-400 text-xs mt-1">Rezultati i tabele</div>
                                    </div>
                                    <svg class="w-5 h-5 text-blue-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Who & Why Section -->
        <section class="py-20 px-6 bg-gray-800/50">
            <div class="max-w-5xl mx-auto">
                <!-- Who Can Use -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-center mb-8 text-white">Ko Može Koristiti?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-4xl mb-3">⚽</div>
                            <h3 class="text-lg font-semibold text-white mb-2">Lokalni Klubovi</h3>
                            <p class="text-gray-400 text-sm">Upravljanje članstvom i treninzima</p>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl mb-3">🏆</div>
                            <h3 class="text-lg font-semibold text-white mb-2">Profesionalne Lige</h3>
                            <p class="text-gray-400 text-sm">Organizacija turnira i praćenje</p>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl mb-3">🎯</div>
                            <h3 class="text-lg font-semibold text-white mb-2">Sportske Federacije</h3>
                            <p class="text-gray-400 text-sm">Centralizovano upravljanje ligama</p>
                        </div>
                    </div>
                </div>

                <!-- Why Use -->
                <div>
                    <h2 class="text-3xl font-bold text-center mb-8 text-white">Zašto Team Sphere?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white mb-1">Jednostavna Organizacija</h3>
                                <p class="text-gray-400 text-sm">Rasporedi i timovi na jednom mjestu</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white mb-1">Detaljne Statistike</h3>
                                <p class="text-gray-400 text-sm">Analitika performansi igrača i timova</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white mb-1">Stoni Tenis</h3>
                                <p class="text-gray-400 text-sm">Za sada samo stoni tenis, uskoro i ostali sportovi</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white mb-1">Turniri i Lige</h3>
                                <p class="text-gray-400 text-sm">Potpuna podrška za organizaciju takmičenja</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Simple Footer -->
        <footer class="border-t border-gray-800 py-8">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <p class="text-gray-400 text-sm">
                    © 2025 Team Sphere. Sva prava zadržana.
                </p>
            </div>
        </footer>
    </body>
</html>
