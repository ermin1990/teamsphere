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

                <!-- Live Matches Banner -->
                <div class="mt-16">
                    <a href="{{ route('public.live-matches') }}" class="block max-w-2xl mx-auto bg-gradient-to-r from-green-500/10 to-emerald-500/10 border-2 border-green-500/30 rounded-2xl p-8 hover:border-green-500/50 transition-all group">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-16 w-16 bg-green-500 items-center justify-center">
                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="text-left">
                                    <div class="text-green-400 font-bold text-sm mb-1">🔴 UŽIVO SADA</div>
                                    <div class="text-white text-2xl font-black">
                                        @if(isset($liveMatchesCount) && $liveMatchesCount > 0)
                                            {{ $liveMatchesCount }} {{ $liveMatchesCount == 1 ? 'Meč u Toku' : ($liveMatchesCount < 5 ? 'Meča u Toku' : 'Mečeva u Toku') }}
                                        @else
                                            <span class="text-xl">Nema Mečeva u Toku</span>
                                        @endif
                                    </div>
                                    <div class="text-gray-400 text-sm mt-1">Pratiš uživo rezultate sa turnira</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 text-green-400 font-semibold group-hover:translate-x-2 transition-transform">
                                <span>Pogledaj</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
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
                                <h3 class="font-semibold text-white mb-1">Real-time Rezultati</h3>
                                <p class="text-gray-400 text-sm">Uživo praćenje svih mečeva</p>
                            </div>
                        </div>
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
