@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 sm:p-8 border border-gray-700/50 shadow-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h2 class="text-2xl sm:text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Admin Dashboard
                </h2>
                <p class="text-gray-400 mt-2">Pregled sistema i upravljanje MojTurnir aplikacijom</p>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-sm text-gray-400">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-sm text-gray-500">{{ now()->format('H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        <!-- Total Users -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Ukupno Korisnika</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ \App\Models\User::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center self-start sm:self-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-green-400 font-medium">+{{ \App\Models\User::where('created_at', '>=', now()->subDays(30))->count() }}</span>
                    <span class="text-gray-500 ml-2">u zadnjih 30 dana</span>
                </div>
            </div>
        </div>

        <!-- Total Organizations -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Ukupno Organizacija</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ \App\Models\Organization::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center self-start sm:self-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-green-400 font-medium">+{{ \App\Models\Organization::where('created_at', '>=', now()->subDays(30))->count() }}</span>
                    <span class="text-gray-500 ml-2">u zadnjih 30 dana</span>
                </div>
            </div>
        </div>

        <!-- Total Leagues -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Ukupno Liga</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ \App\Models\League::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center self-start sm:self-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-green-400 font-medium">+{{ \App\Models\League::where('created_at', '>=', now()->subDays(30))->count() }}</span>
                    <span class="text-gray-500 ml-2">u zadnjih 30 dana</span>
                </div>
            </div>
        </div>

        <!-- Total Tournaments -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Ukupno Turnira</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ \App\Models\Competition::where('type', 'tournament')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-rose-600 rounded-xl flex items-center justify-center self-start sm:self-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4.5a3 3 0 116 0v1.5h1.5A2.25 2.25 0 0118.75 8v1a3 3 0 01-3 3h-.5a4.5 4.5 0 01-3.5 4.359V18h2a2 2 0 012 2H8.25a2 2 0 012-2h2v-1.641A4.5 4.5 0 018.75 12h-.5a3 3 0 01-3-3V8a2.25 2.25 0 012.25-2.25H9V4.5z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-green-400 font-medium">+{{ \App\Models\Competition::where('type', 'tournament')->where('created_at', '>=', now()->subDays(30))->count() }}</span>
                    <span class="text-gray-500 ml-2">u zadnjih 30 dana</span>
                </div>
            </div>
        </div>

        <!-- Total Players -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Ukupno Igrača</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ \App\Models\Player::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center self-start sm:self-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4.13a4 4 0 10-4-4 4 4 0 004 4zm6 4a4 4 0 10-4-4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-green-400 font-medium">+{{ \App\Models\Player::where('created_at', '>=', now()->subDays(30))->count() }}</span>
                    <span class="text-gray-500 ml-2">u zadnjih 30 dana</span>
                </div>
            </div>
        </div>

        <!-- Active Sports -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Aktivni Sportovi</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ \App\Models\Sport::active()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center self-start sm:self-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l.707.707A1 1 0 0012.414 11H13m-3 3a1 1 0 100 2 1 1 0 000-2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-gray-400">od {{ \App\Models\Sport::count() }} ukupno</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Users -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <h3 class="text-xl font-bold text-white mb-6">Nedavni Korisnici</h3>
            <div class="space-y-4">
                @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4 p-3 rounded-lg bg-gray-700/30">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center self-start sm:self-center">
                        <span class="text-white font-bold text-sm">{{ substr($user->name, 0, 2) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-medium">{{ $user->name }}</p>
                        <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-gray-400 text-xs">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Organizations -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <h3 class="text-xl font-bold text-white mb-6">Nedavne Organizacije</h3>
            <div class="space-y-4">
                @foreach(\App\Models\Organization::with('user')->latest()->take(5)->get() as $org)
                <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4 p-3 rounded-lg bg-gray-700/30">
                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center self-start sm:self-center">
                        <span class="text-white font-bold text-sm">{{ substr($org->name, 0, 2) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-medium">{{ $org->name }}</p>
                        <p class="text-gray-400 text-sm">od {{ $org->user->name }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-gray-400 text-xs">{{ $org->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
        <h3 class="text-xl font-bold text-white mb-6">Informacije o Sistemu</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h4 class="text-white font-semibold mb-2">Laravel</h4>
                <p class="text-gray-400">{{ app()->version() }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h4 class="text-white font-semibold mb-2">PHP</h4>
                <p class="text-gray-400">{{ PHP_VERSION }}</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                    </svg>
                </div>
                <h4 class="text-white font-semibold mb-2">Baza Podataka</h4>
                <p class="text-gray-400">SQLite</p>
            </div>
        </div>
    </div>
</div>
@endsection
