@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900">
    <!-- Header -->
    <div class="bg-gray-800/50 backdrop-blur-xl border-b border-gray-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">{{ $league->name }}</h1>
                        <p class="mt-1 text-sm text-gray-400">{{ $league->organization->name }} • {{ $league->sport->name }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('referee.leagues') }}" class="inline-flex items-center px-6 py-3 border border-gray-600 text-sm font-medium rounded-xl text-gray-300 bg-gray-700/50 hover:bg-gray-600/50 transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-gray-500/25">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Nazad na Lige
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Ukupno Mečeva</dt>
                            <dd class="text-2xl font-bold text-white mt-1">{{ $league->matches->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Završeno</dt>
                            <dd class="text-2xl font-bold text-white mt-1">{{ $league->matches->where('status', 'completed')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Otkazano</dt>
                            <dd class="text-2xl font-bold text-white mt-1">{{ $league->matches->where('status', 'cancelled')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">U Toku</dt>
                            <dd class="text-2xl font-bold text-white mt-1">{{ $league->matches->where('status', 'in_progress')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matches List -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 border-b border-gray-700/50 px-6 py-5">
                <h3 class="text-lg leading-6 font-medium text-white">Mečevi</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-400">Svi mečevi u ovoj ligi</p>
            </div>

            @if($matches->count() > 0)
                @foreach($matches as $round => $roundMatches)
                    <div class="border-b border-gray-700/30 last:border-b-0">
                        <div class="px-6 py-3 bg-gray-700/20">
                            <h4 class="text-sm font-medium text-white">Kolo {{ $round }}</h4>
                        </div>
                        <ul class="divide-y divide-gray-700/30">
                            @foreach($roundMatches as $match)
                                <li>
                                    <div class="px-6 py-4 hover:bg-gray-700/20 transition-colors duration-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <span class="text-lg">{{ $league->sport->icon }}</span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-medium text-white">
                                                        @if($league->is_team_based)
                                                            {{ $match->homeTeam?->name ?? 'TBD' }} vs {{ $match->awayTeam?->name ?? 'TBD' }}
                                                        @else
                                                            {{ $match->homePlayer?->name ?? 'TBD' }} vs {{ $match->awayPlayer?->name ?? 'TBD' }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center space-x-4">
                                                <div class="text-right">
                                                    @if($match->status === 'completed')
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-300 border border-green-500/30">
                                                            {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                                        </span>
                                                    @elseif($match->status === 'in_progress')
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500/20 text-red-300 border border-red-500/30 animate-pulse">
                                                            Uživo: {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                                        </span>
                                                    @elseif($match->status === 'cancelled')
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-500/20 text-gray-300 border border-gray-500/30">
                                                            Otkazano
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                                            {{ ucfirst($match->status) }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('leagues.matches.show', [$league, $match]) }}"
                                                       class="inline-flex items-center px-4 py-2 border border-gray-600 text-sm leading-5 font-medium rounded-xl text-gray-300 bg-gray-700/50 hover:bg-gray-600/50 transition-all duration-200 transform hover:scale-[1.02]">
                                                        Pogledaj
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @else
                <div class="px-6 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-300">Nema mečeva</h3>
                    <p class="mt-1 text-sm text-gray-500">Ova liga još nema mečeva.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection