@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.leagues.index') }}" class="p-2 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $league->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $league->organization->name }} • {{ $league->sport->name }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-left sm:text-right">
                <p class="text-sm text-gray-400">Kreirana</p>
                <p class="text-lg font-semibold text-white">{{ $league->created_at->format('d.m.Y') }}</p>
            </div>
        </div>
    </div>

    <!-- League Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">{{ $league->sport->icon }}</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $league->sport->name }}</p>
                    <p class="text-gray-400 text-sm">Sport</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $league->matches->count() }}</p>
                    <p class="text-gray-400 text-sm">Utakmica</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $league->teams->count() }}</p>
                    <p class="text-gray-400 text-sm">Timova</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $league->max_teams ?? '∞' }}</p>
                    <p class="text-gray-400 text-sm">Max timova</p>
                </div>
            </div>
        </div>
    </div>

    <!-- League Settings -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Postavke Lige</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Naziv lige</label>
                    <p class="text-white bg-gray-700/50 px-4 py-3 rounded-lg">{{ $league->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Sport</label>
                    <p class="text-white bg-gray-700/50 px-4 py-3 rounded-lg">{{ $league->sport->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Organizacija</label>
                    <p class="text-white bg-gray-700/50 px-4 py-3 rounded-lg">{{ $league->organization->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Maksimalno timova</label>
                    <p class="text-white bg-gray-700/50 px-4 py-3 rounded-lg">{{ $league->max_teams ?? 'Neograničeno' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Teams -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Timovi</h3>
        </div>

        @if($league->teams->count() > 0)
        <div class="divide-y divide-gray-700/50">
            @foreach($league->teams as $team)
            <div class="p-6 hover:bg-gray-700/20 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold">{{ substr($team->name, 0, 2) }}</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">{{ $team->name }}</h4>
                            <p class="text-gray-400 text-sm">{{ $team->players->count() }} igrača</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-400">{{ $team->created_at->format('d.m.Y') }}</p>
                        </div>
                        <a href="{{ route('leagues.team-management', $league) }}" class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-colors border border-blue-500/30">
                            Upravljaj
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
            </div>
            <h4 class="text-white font-semibold mb-2">Nema timova</h4>
            <p class="text-gray-400">Ova liga još nema registrovanih timova.</p>
        </div>
        @endif
    </div>

    <!-- Matches -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Utakmice</h3>
        </div>

        @if($league->matches->count() > 0)
        <div class="divide-y divide-gray-700/50">
            @foreach($league->matches as $match)
            <div class="p-6 hover:bg-gray-700/20 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="text-center">
                            <p class="text-white font-semibold">{{ $match->homeTeam?->name ?? 'TBD' }}</p>
                            <p class="text-gray-400 text-sm">vs</p>
                            <p class="text-white font-semibold">{{ $match->awayTeam?->name ?? 'TBD' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-white">{{ $match->home_score ?? '-' }} : {{ $match->away_score ?? '-' }}</p>
                            <p class="text-gray-400 text-xs">Rezultat</p>
                        </div>

                        <div class="text-center">
                            <p class="text-sm text-gray-400">{{ $match->scheduled_at ? $match->scheduled_at->format('d.m.Y H:i') : 'Nije zakazano' }}</p>
                            <p class="text-gray-500 text-xs">{{ $match->scheduled_at ? $match->scheduled_at->diffForHumans() : '' }}</p>
                        </div>

                        <a href="{{ route('leagues.matches.show', [$league, $match]) }}" class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-colors border border-blue-500/30">
                            Pregledaj
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h4 class="text-white font-semibold mb-2">Nema utakmica</h4>
            <p class="text-gray-400">Ova liga još nema zakazanih utakmica.</p>
        </div>
        @endif
    </div>
</div>
@endsection