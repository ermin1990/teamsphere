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
        <!-- Mobile Header -->
        <header class="md:hidden bg-gray-900/95 backdrop-blur-sm border-b border-gray-800/50 py-4 px-6 sticky top-0 z-50">
            <div class="flex items-center justify-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Team Sphere</span>
            </div>
        </header>

        <!-- Simple Hero -->
        <section class="min-h-screen flex items-center justify-center px-6 md:flex md:items-center md:justify-center pt-8 md:pt-0">
            <div class="max-w-4xl mx-auto text-center w-full">
                <!-- Logo -->
                <div class="hidden md:flex items-center justify-center space-x-3 mb-8">
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
                    <a href="#about" class="border-2 border-gray-700 hover:border-blue-500 px-10 py-4 rounded-xl text-gray-400 hover:text-blue-400 font-semibold text-lg transition-all">
                        O Aplikaciji
                    </a>
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

        <!-- About Section -->
        <section id="about" class="py-24 px-6 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 relative overflow-hidden">
            <!-- Decorative Background Elements -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-72 h-72 bg-blue-500 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-10 w-96 h-96 bg-purple-500 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-6xl mx-auto relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold mb-4">
                        <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">O Aplikaciji</span>
                    </h2>
                    <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto rounded-full"></div>
                </div>

                <!-- Mission Statement -->
                <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-3xl p-8 md:p-12 mb-12 border border-gray-700/50 shadow-2xl">
                    <div class="flex items-start space-x-4 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-4">Svrha Aplikacije</h3>
                            <p class="text-gray-300 text-lg leading-relaxed mb-6">
                                Team Sphere je moderna, sveobuhvatna platforma za organizaciju i upravljanje sportskim takmičenjima,
                                posebno dizajnirana za stoni tenis. Naša misija je da omogućimo klubovima, ligama i federacijama
                                jednostavno kreiranje turnira, praćenje rezultata u realnom vremenu i detaljnu analitiku performansi.
                            </p>
                            <div class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border-l-4 border-green-500 rounded-r-xl p-6">
                                <p class="text-green-400 font-semibold text-lg mb-2">💚 Besplatno za Osnovne Potrebe</p>
                                <p class="text-gray-400">
                                    Aplikacija je <strong class="text-green-300">potpuno besplatna</strong> ukoliko klijent
                                    ne želi više takmičenja i igrača od osnovnog paketa. Naša misija je učiniti sportsko upravljanje
                                    dostupnim svima.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Section -->
                <div class="mb-12">
                    <h3 class="text-3xl font-bold text-center mb-10 text-white">Tim</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Developer Card -->
                        <div class="group bg-gradient-to-br from-blue-500/10 to-purple-500/10 backdrop-blur-sm rounded-3xl p-8 border border-blue-500/30 hover:border-blue-500/60 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/20">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-xl">
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                    </svg>
                                </div>
                                <h4 class="text-2xl font-bold text-white mb-2">Ermin Selimović</h4>
                                <p class="text-blue-400 font-semibold mb-4">Dizajn & Razvoj</p>
                                <p class="text-gray-400 text-sm mb-6">
                                    Full-stack developer i dizajner odgovoran za kompletnu izradu aplikacije,
                                    od korisničkog interfejsa do serverske logike.
                                </p>
                                <div class="flex space-x-4">
                                    <a href="https://github.com/ermin1990" target="_blank" rel="noopener" class="flex items-center space-x-2 bg-gray-800/50 hover:bg-gray-700/50 px-4 py-2 rounded-xl transition-all group/link">
                                        <svg class="w-5 h-5 text-gray-400 group-hover/link:text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                        </svg>
                                        <span class="text-gray-400 group-hover/link:text-white text-sm font-medium">GitHub</span>
                                    </a>
                                    <a href="https://instagram.com/infinitycreative.agency" target="_blank" rel="noopener" class="flex items-center space-x-2 bg-gradient-to-r from-pink-500/10 to-purple-500/10 hover:from-pink-500/20 hover:to-purple-500/20 border border-pink-500/30 hover:border-pink-500/50 px-4 py-2 rounded-xl transition-all group/link">
                                        <svg class="w-5 h-5 text-pink-400 group-hover/link:text-pink-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                        </svg>
                                        <span class="text-pink-400 group-hover/link:text-pink-300 text-sm font-medium">Instagram</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Consultant Card -->
                        <div class="group bg-gradient-to-br from-purple-500/10 to-pink-500/10 backdrop-blur-sm rounded-3xl p-8 border border-purple-500/30 hover:border-purple-500/60 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/20">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-xl">
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-2xl font-bold text-white mb-2">Sanel Morankić</h4>
                                <p class="text-purple-400 font-semibold mb-4">Analiza & Takmičarska Logika</p>
                                <p class="text-gray-400 text-sm mb-6">
                                    Stručnjak za sportsku analitiku i sistem takmičenja, odgovoran za razvoj
                                    algoritama rangiranja, turnirske logike i sistema bodovanja.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thank You Note -->
                <div class="text-center">
                    <div class="inline-block bg-gradient-to-r from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-2xl px-8 py-6 border border-gray-700/50">
                        <p class="text-gray-300 text-sm flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span>Zahvaljujemo svim korisnicima na podršci tokom razvoja</span>
                        </p>
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
