@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900">
    <!-- Header -->
    <div class="bg-gray-800/50 backdrop-blur-xl border-b border-gray-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Moderator Dashboard</h1>
                        <p class="mt-1 text-sm text-gray-400">Pregled mečeva na kojima ste dodijeljeni kao sudija</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('referee.dashboard') }}" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-gray-500/25 font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Nazad na Sudijski Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Moderated Matches -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">Moderirani Mečevi</h2>

            @if($moderatedMatches->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($moderatedMatches as $match)
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6 hover:bg-gray-800/70 transition-all duration-200">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($match->status === 'completed') bg-green-500/20 text-green-400
                                    @elseif($match->status === 'forfeited') bg-red-500/20 text-red-400
                                    @else bg-gray-500/20 text-gray-400 @endif">
                                    {{ ucfirst($match->status) }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $match->played_at?->format('M j, Y') }}</span>
                            </div>

                            <div class="text-center mb-4">
                                <h3 class="text-lg font-semibold text-white">{{ $match->league->name }}</h3>
                                <p class="text-sm text-gray-400">{{ $match->league->organization->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">Round {{ $match->round }}</p>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-400">
                                        @if($match->league->is_team_based)
                                            {{ $match->homeTeam?->name ?? 'TBD' }}
                                        @else
                                            {{ $match->homePlayer?->name ?? 'TBD' }}
                                        @endif
                                    </span>
                                    <span class="font-bold text-white">{{ $match->home_score ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-400">
                                        @if($match->league->is_team_based)
                                            {{ $match->awayTeam?->name ?? 'TBD' }}
                                        @else
                                            {{ $match->awayPlayer?->name ?? 'TBD' }}
                                        @endif
                                    </span>
                                    <span class="font-bold text-white">{{ $match->away_score ?? 0 }}</span>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-700">
                                <a href="{{ route('leagues.matches.show', [$match->league, $match]) }}"
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-4 rounded-lg transition-colors text-center block">
                                    Pogledaj Meč
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-white mb-2">Još niste moderirali nijedan meč</h3>
                    <p class="text-gray-400">Još niste sudili nijedan meč. Mečevi koje moderirate će se pojaviti ovdje nakon što budu završeni.</p>
                </div>
            @endif
        </div>

        <!-- Assigned Matches -->
        <div>
            <h2 class="text-2xl font-bold text-white mb-6">Dodijeljeni Mečevi</h2>

            @if($assignedMatches->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($assignedMatches as $match)
                        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6 hover:bg-gray-800/70 transition-all duration-200">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">
                                    {{ ucfirst($match->status) }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $match->scheduled_at?->format('M j, Y H:i') }}</span>
                            </div>

                            <div class="text-center mb-4">
                                <h3 class="text-lg font-semibold text-white">{{ $match->league->name }}</h3>
                                <p class="text-sm text-gray-400">{{ $match->league->organization->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">Round {{ $match->round }}</p>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-400">
                                        @if($match->league->is_team_based)
                                            {{ $match->homeTeam?->name ?? 'TBD' }}
                                        @else
                                            {{ $match->homePlayer?->name ?? 'TBD' }}
                                        @endif
                                    </span>
                                    <span class="font-bold text-white">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-400">
                                        @if($match->league->is_team_based)
                                            {{ $match->awayTeam?->name ?? 'TBD' }}
                                        @else
                                            {{ $match->awayPlayer?->name ?? 'TBD' }}
                                        @endif
                                    </span>
                                    <span class="font-bold text-white">-</span>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-700 flex space-x-2">
                                <a href="{{ route('leagues.matches.show', [$match->league, $match]) }}"
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-4 rounded-lg transition-colors text-center">
                                    Pogledaj Meč
                                </a>
                                <a href="{{ route('leagues.matches.edit', [$match->league, $match]) }}"
                                   class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm py-2 px-4 rounded-lg transition-colors text-center">
                                    Upravljaj
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-white mb-2">Trenutno nema dodijeljenih mečeva</h3>
                    <p class="text-gray-400">Trenutno nemate dodijeljenih mečeva za sudjenje.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection