@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900">
    <!-- Header -->
    <div class="bg-gray-800/50 backdrop-blur-xl border-b border-gray-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Sudijska Kontrolna Tabla</h1>
                        <p class="mt-1 text-sm text-gray-400">Upravljajte mečevima i rezultatima za vaše dodijeljene lige</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('referee.moderator.dashboard') }}" class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-purple-500/25 font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Moji Mečevi
                        </a>
                        <a href="{{ route('referee.leagues') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Pogledaj Sve
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Moje Lige</dt>
                            <dd class="text-2xl font-bold text-white mt-1">{{ $leagues->count() }}</dd>
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
                            <dt class="text-sm font-medium text-gray-400 truncate">Završeni Mečevi</dt>
                            <dd class="text-2xl font-bold text-white mt-1">{{ $completedMatches }}</dd>
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
                            <dd class="text-2xl font-bold text-white mt-1">{{ $inProgressMatches }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-400 truncate">Zakazani</dt>
                            <dd class="text-2xl font-bold text-white mt-1">{{ $scheduledMatches }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Organizations -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 border-b border-gray-700/50 px-6 py-5">
                <h3 class="text-lg leading-6 font-medium text-white">Moje Organizacije</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-400">Organizacije gdje ste dodijeljeni kao sudija</p>
            </div>
            <ul class="divide-y divide-gray-700/30">
                @forelse($organizations as $organization)
                    <li>
                        <a href="{{ route('referee.leagues') }}" class="block hover:bg-gray-700/20 transition-colors duration-200">
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-semibold text-sm">{{ substr($organization->name, 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white">{{ $organization->name }}</div>
                                            <div class="text-sm text-gray-400">{{ $organization->leagues->count() }} Lige • {{ $organization->leagues->sum(function($league) { return $league->matches->count(); }) }} Mečevi</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="px-6 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-300">Još nema organizacija</h3>
                        <p class="mt-1 text-sm text-gray-500">Još niste dodijeljeni kao sudija ni u jednoj organizaciji.</p>
                    </li>
                @endforelse
            </ul>
        </div>

        <!-- My Leagues -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-green-600/20 to-blue-600/20 border-b border-gray-700/50 px-6 py-5">
                <h3 class="text-lg leading-6 font-medium text-white">Moje Lige</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-400">Lige gdje ste dodijeljeni kao sudija</p>
            </div>
            <ul class="divide-y divide-gray-700/30">
                @forelse($leagues as $league)
                    <li>
                        <a href="{{ route('referee.league.matches', $league) }}" class="block hover:bg-gray-700/20 transition-colors duration-200">
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <span class="text-2xl">{{ $league->sport->icon }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white">{{ $league->name }}</div>
                                            <div class="text-sm text-gray-400">{{ $league->organization->name }} • {{ $league->matches->count() }} Mečevi</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="px-6 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-300">Još nema liga</h3>
                        <p class="mt-1 text-sm text-gray-500">Još niste dodijeljeni kao sudija ni u jednoj ligi.</p>
                    </li>
                @endforelse
            </ul>
        </div>

        <!-- My Moderator Matches -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-purple-600/20 to-indigo-600/20 border-b border-gray-700/50 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-white">Moji Mečevi kao Sudija</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-400">Mečevi gdje ste dodijeljeni kao moderator/sudija</p>
                    </div>
                    <a href="{{ route('referee.moderator.dashboard') }}" class="text-sm text-purple-400 hover:text-purple-300 font-medium">
                        Pogledaj sve →
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($moderatorRecentMatches->count() > 0 || $moderatorUpcomingMatches->count() > 0)
                    <div class="space-y-6">
                        <!-- Recent Moderator Matches -->
                        @if($moderatorRecentMatches->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-300 mb-3">Nedavni Mečevi</h4>
                                <div class="space-y-3">
                                    @foreach($moderatorRecentMatches as $match)
                                        <a href="{{ route('leagues.matches.show', [$match->league, $match]) }}" class="block hover:bg-gray-700/50 transition-colors duration-200">
                                            <div class="flex items-center justify-between p-3 bg-gray-700/30 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-xs font-bold">
                                                            @if($match->status === 'completed')
                                                                ✓
                                                            @else
                                                                ⚡
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-white">{{ $match->league->name }}</div>
                                                        <div class="text-xs text-gray-400">
                                                            @if($match->league->is_team_based)
                                                                {{ $match->homeTeam?->name ?? 'TBD' }} vs {{ $match->awayTeam?->name ?? 'TBD' }}
                                                            @else
                                                                {{ $match->homePlayer?->name ?? 'TBD' }} vs {{ $match->awayPlayer?->name ?? 'TBD' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-sm font-medium text-white">
                                                        {{ $match->home_score ?? 0 }}-{{ $match->away_score ?? 0 }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">{{ $match->played_at?->format('M j') }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Upcoming Moderator Matches -->
                        @if($moderatorUpcomingMatches->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-300 mb-3">Zakazani Mečevi (7 dana)</h4>
                                <div class="space-y-3">
                                    @foreach($moderatorUpcomingMatches as $match)
                                        <a href="{{ route('leagues.matches.show', [$match->league, $match]) }}" class="block hover:bg-gray-700/50 transition-colors duration-200">
                                            <div class="flex items-center justify-between p-3 bg-gray-700/30 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-xs">📅</span>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-white">{{ $match->league->name }}</div>
                                                        <div class="text-xs text-gray-400">
                                                            @if($match->league->is_team_based)
                                                                {{ $match->homeTeam?->name ?? 'TBD' }} vs {{ $match->awayTeam?->name ?? 'TBD' }}
                                                            @else
                                                                {{ $match->homePlayer?->name ?? 'TBD' }} vs {{ $match->awayPlayer?->name ?? 'TBD' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-sm font-medium text-white">VS</div>
                                                    <div class="text-xs text-gray-400">{{ $match->scheduled_at?->format('M j, H:i') }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-white mb-2">Još nema moderator mečeva</h3>
                        <p class="text-gray-400 text-sm">Još niste dodijeljeni kao sudija ni na jednom meču.</p>
                        <p class="text-gray-500 text-xs mt-2">Organizacijski owner-i vas mogu dodijeliti kao moderatora u edit formi meča.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection