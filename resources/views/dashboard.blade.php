<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <div>
                <h2 class="font-bold text-2xl md:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Kontrolna Tabla
                </h2>
                <p class="text-gray-400 mt-1">Dobrodošli nazad, {{ Auth::user()->name }}!</p>
            </div>
        </div>
    </x-slot>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <div>
                <h2 class="font-bold text-2xl md:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Kontrolna Tabla
                </h2>
                <p class="text-gray-400 mt-1">Dobrodošli nazad, {{ Auth::user()->name }}!</p>
            </div>
        </div>
    </x-slot>

    <!-- Navigation Bar -->
    <div x-data="{ open: false }" class="flex justify-between h-16">
        <div class="flex">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Team Sphere</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <a class="inline-flex items-center px-1 pt-1 border-b-2 border-blue-400 text-sm font-medium leading-5 text-white focus:outline-none focus:border-blue-300 transition duration-150 ease-in-out text-white hover:text-blue-400 transition-colors" href="{{ route('dashboard') }}">
                    Dashboard
                </a>
            </div>
        </div>

        <!-- Settings Dropdown -->
        <div class="hidden sm:flex sm:items-center sm:ms-6">
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                <div @click="open = ! open">
                    <button class="inline-flex items-center px-4 py-2 bg-gray-700/50 border border-gray-600/50 text-sm leading-4 font-medium rounded-xl text-white hover:bg-gray-600/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-200 backdrop-blur-sm">
                        <div>{{ Auth::user()->name }}</div>

                        <div class="ms-2">
                            <svg class="fill-current h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </button>
                </div>

                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute z-50 mt-2 w-48 rounded-md shadow-lg ltr:origin-top-right rtl:origin-top-left end-0" style="display: none;" @click="open = false">
                    <div class="rounded-md ring-1 ring-gray-600/50 py-1 bg-gray-800/95 backdrop-blur-xl border border-gray-700/50">
                        <a class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-200 hover:bg-gray-700/50 hover:text-white focus:outline-none focus:bg-gray-700/50 focus:text-white transition duration-150 ease-in-out text-white hover:bg-gray-700/50" href="{{ route('profile.edit') }}">Profile</a>

                        @if(Auth::user()->isAdmin())
                            <a class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-200 hover:bg-gray-700/50 hover:text-white focus:outline-none focus:bg-gray-700/50 focus:text-white transition duration-150 ease-in-out text-white hover:bg-gray-700/50" href="{{ route('admin.dashboard') }}">Admin Panel</a>
                        @endif

                        <a class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-200 hover:bg-gray-700/50 hover:text-white focus:outline-none focus:bg-gray-700/50 focus:text-white transition duration-150 ease-in-out text-white hover:bg-gray-700/50" href="{{ route('feedback.create') }}">Prijavi grešku | Sugestija</a>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-200 hover:bg-gray-700/50 hover:text-white focus:outline-none focus:bg-gray-700/50 focus:text-white transition duration-150 ease-in-out text-red-400 hover:text-red-300 hover:bg-red-900/20" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hamburger -->
        <div class="-me-2 flex items-center sm:hidden">
            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-white hover:bg-gray-700/50 focus:outline-none focus:bg-gray-700/50 focus:text-white transition duration-200 ease-in-out backdrop-blur-sm">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="sm:hidden" style="display: none;">
        <div class="pt-2 pb-3 space-y-1 bg-gray-800/95 backdrop-blur-xl border-t border-gray-700/50">
            <a class="block pl-3 pr-4 py-2 border-l-4 border-blue-400 text-base font-medium text-white bg-gray-700/50 focus:outline-none focus:text-white focus:bg-gray-700/50 focus:border-blue-300 transition duration-150 ease-in-out" href="{{ route('dashboard') }}">
                Dashboard
            </a>
        </div>
        <div class="pt-4 pb-1 border-t border-gray-600/50">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-medium text-sm">{{ substr(Auth::user()->name, 0, 2) }}</span>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <a class="block px-4 py-2 text-base font-medium text-gray-200 hover:text-white hover:bg-gray-700/50 focus:outline-none focus:text-white focus:bg-gray-700/50 transition duration-150 ease-in-out" href="{{ route('profile.edit') }}">
                    Profile
                </a>

                @if(Auth::user()->isAdmin())
                    <a class="block px-4 py-2 text-base font-medium text-gray-200 hover:text-white hover:bg-gray-700/50 focus:outline-none focus:text-white focus:bg-gray-700/50 transition duration-150 ease-in-out" href="{{ route('admin.dashboard') }}">
                        Admin Panel
                    </a>
                @endif

                <a class="block px-4 py-2 text-base font-medium text-gray-200 hover:text-white hover:bg-gray-700/50 focus:outline-none focus:text-white focus:bg-gray-700/50 transition duration-150 ease-in-out" href="{{ route('feedback.create') }}">
                    Prijavi grešku | Sugestija
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="block px-4 py-2 text-base font-medium text-red-400 hover:text-red-300 hover:bg-red-900/20 focus:outline-none focus:text-red-300 focus:bg-red-900/20 transition duration-150 ease-in-out" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </a>
                </form>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Quick Links Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">Brzi Linkovi</h3>
                        <p class="text-gray-400">Pristupite javnim stranicama i početnoj stranici</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-4">
                    <a href="{{ url('/') }}" target="_blank" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 inline-block">
                        <span class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Početna Stranica</span>
                        </span>
                    </a>
                    <a href="{{ route('public.leagues.index') }}" target="_blank" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-green-500/25 inline-block">
                        <span class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span>Javni Turniri</span>
                        </span>
                    </a>
                </div>
            </div>

            <!-- Referee Section -->
            @if($isReferee)
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">Sudijska Kontrolna Tabla</h3>
                        <p class="text-gray-400">Vi ste sudija u jednoj ili više organizacija</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('referee.dashboard') }}" class="bg-gradient-to-r from-orange-600 to-red-700 hover:from-orange-700 hover:to-red-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-orange-500/25 inline-block">
                        <span class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Idi na Sudijsku Kontrolnu Tablu</span>
                        </span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- My Organizations -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl lg:col-span-2">
                    <div class="mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h3 class="text-xl sm:text-2xl font-bold text-white">Moje Organizacije</h3>
                                <p class="text-gray-400 text-sm">Upravljajte svojim organizacijama i takmičenjima</p>
                            </div>
                            @if(Auth::user()->canCreateMoreOrganizations())
                                <a href="{{ route('organizations.create') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 sm:px-6 sm:py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 inline-flex items-center space-x-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="text-sm sm:text-base">Kreiraj Organizaciju</span>
                                </a>
                            @endif
                        </div>
                    </div>

                @if($organizations->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($organizations as $organization)
                            <div class="bg-gray-700/30 rounded-xl p-6 hover:bg-gray-600/30 transition-all duration-200 border border-gray-600/20 hover:border-gray-500/30">
                                <!-- Organization Header -->
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                        <span class="text-white font-bold text-lg">{{ substr($organization->name, 0, 2) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-white font-bold text-lg">{{ $organization->name }}</h4>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-y-3">
                                    <a href="{{ route('organizations.show', $organization) }}" class="block bg-gray-600/50 hover:bg-gray-500/50 text-white px-4 py-3 rounded-lg transition-all duration-200 text-center font-semibold">
                                        <span class="flex items-center justify-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <span>Pogledaj Organizaciju</span>
                                        </span>
                                    </a>

                                    @if($organization->user_id === Auth::id())
                                        <div class="text-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Vlasnik
                                            </span>
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Vi ste u toj organizaciji
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-white mb-3">Još nema organizacija</h4>
                        <p class="text-gray-400 mb-6 max-w-md mx-auto">Još niste dodani kao igrač ni u jednu organizaciju. Kreirajte svoju prvu organizaciju da započnete sa upravljanjem takmičenjima.</p>
                        @if(Auth::user()->canCreateMoreOrganizations())
                        <a href="{{ route('organizations.create') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 inline-block">
                            <span class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="text-lg font-semibold">Kreirajte svoju prvu organizaciju</span>
                            </span>
                        </a>
                        @endif
                    </div>
                @endif
            </div>

                <!-- Upcoming Matches -->
                @if($upcomingMatches->count() > 0)
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-4">Nadolazeći Mečevi</h3>
                    <p class="text-gray-400 mb-6">Vaši zakazani mečevi</p>

                    <div class="space-y-4">
                        @foreach($upcomingMatches->sortBy('scheduled_at') as $match)
                            <div class="bg-gray-700/30 rounded-xl p-4 hover:bg-gray-600/30 transition-all duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-2xl">{{ $match->league->sport->icon }}</div>
                                        <div>
                                            <h4 class="text-white font-semibold">
                                                {{ $match->homePlayer?->name ?? 'TBD' }} vs {{ $match->awayPlayer?->name ?? 'TBD' }}
                                                @if($match->home_player_id == $players->first()->id || $match->away_player_id == $players->first()->id)
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        You
                                                    </span>
                                                @endif
                                            </h4>
                                            <p class="text-gray-400 text-sm">
                                                {{ $match->league->name }} • {{ $match->scheduled_at ? $match->scheduled_at->format('M d, Y H:i') : 'Nije zakazano' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($match->status === 'scheduled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Scheduled
                                            </span>
                                        @elseif($match->status === 'in_progress')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                                LIVE
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($match->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

            <!-- Plan & Limits Section -->
            @php
                $currentPlan = Auth::user()->currentPlan();
            @endphp
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Vaš Plan i Ograničenja</h3>
                        <p class="text-gray-400 text-sm">Trenutni plan i dostupne mogućnosti</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Current Plan -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Trenutni Plan</span>
                            @if($currentPlan)
                                <span class="px-2 py-1 bg-green-600/20 text-green-400 text-xs rounded-full">{{ $currentPlan->name }}</span>
                            @else
                                <span class="px-2 py-1 bg-gray-600/20 text-gray-400 text-xs rounded-full">Free</span>
                            @endif
                        </div>
                        <div class="text-2xl font-bold text-white">
                            @if($currentPlan)
                                {{ $currentPlan->price }} {{ $currentPlan->currency }}/mo
                            @else
                                Free
                            @endif
                        </div>
                    </div>

                    <!-- Organizations Used -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Organizacije</span>
                            @if($currentPlan)
                                <span class="text-xs text-gray-500">{{ $usageStats['organizations_used'] }}/{{ $usageStats['max_organizations'] }}</span>
                            @endif
                        </div>
                        <div class="text-2xl font-bold text-white">{{ $usageStats['organizations_used'] }}</div>
                        <div class="text-xs text-gray-400">
                            @if($currentPlan)
                                {{ $usageStats['max_organizations'] - $usageStats['organizations_used'] }} preostalo
                            @else
                                Neograničeno
                            @endif
                        </div>
                    </div>

                    <!-- Competitions Used -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Turniri</span>
                            @if($currentPlan)
                                <span class="text-xs text-gray-500">{{ $usageStats['competitions_used'] }}/{{ $organizations->count() * $usageStats['max_competitions_per_organization'] }}</span>
                            @endif
                        </div>
                        <div class="text-2xl font-bold text-white">{{ $usageStats['competitions_used'] }}</div>
                        <div class="text-xs text-gray-400">
                            @if($currentPlan)
                                {{ ($organizations->count() * $usageStats['max_competitions_per_organization']) - $usageStats['competitions_used'] }} preostalo
                            @else
                                Neograničeno
                            @endif
                        </div>
                    </div>

                    <!-- Leagues Used -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Lige</span>
                            @if($currentPlan)
                                <span class="text-xs text-gray-500">{{ $usageStats['leagues_used'] }}/{{ $organizations->count() * $usageStats['max_leagues_per_organization'] }}</span>
                            @endif
                        </div>
                        <div class="text-2xl font-bold text-white">{{ $usageStats['leagues_used'] }}</div>
                        <div class="text-xs text-gray-400">
                            @if($currentPlan)
                                {{ ($organizations->count() * $usageStats['max_leagues_per_organization']) - $usageStats['leagues_used'] }} preostalo
                            @else
                                Neograničeno
                            @endif
                        </div>
                    </div>
                </div>

                                        @if(!$currentPlan || $usageStats['organizations_used'] >= $usageStats['max_organizations'])
                <div class="text-center">
                    <a href="#" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-purple-500/25 inline-block">
                        <span class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>Upgrade Plan</span>
                        </span>
                    </a>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
