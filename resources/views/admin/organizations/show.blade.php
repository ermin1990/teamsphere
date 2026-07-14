@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.organizations.index') }}" class="p-2 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $organization->name }}
                </h2>
                <p class="text-gray-400 mt-1">od {{ $organization->user->name }}</p>
            </div>
        </div>
        <div class="text-left sm:text-right">
            <p class="text-sm text-gray-400">Kreirana</p>
            <p class="text-lg font-semibold text-white">{{ $organization->created_at->format('d.m.Y') }}</p>
        </div>
    </div>

    <!-- Organization Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $organization->competitions->count() }}</p>
                    <p class="text-gray-400 text-sm">Takmičenja</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $organization->competitions->sum(fn($c) => $c->matches->count()) }}</p>
                    <p class="text-gray-400 text-sm">Utakmica</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4.13a4 4 0 10-4-4 4 4 0 004 4zm6 4a4 4 0 10-4-4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $organization->players->count() }}</p>
                    <p class="text-gray-400 text-sm">Igrača</p>
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
                    <p class="text-2xl font-bold text-white">{{ $organization->user->currentPlan() ? $organization->user->currentPlan()->name : 'Free' }}</p>
                    <p class="text-gray-400 text-sm">Plan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Usage -->
    @php $plan = $organization->user->currentPlan(); @endphp
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
        <h3 class="text-xl font-bold text-white mb-4">Iskorištenost Plana</h3>
        @if($plan)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $competitionsUsed = $organization->competitions->count();
                $competitionsMax = $plan->max_competitions_per_organization;
                $competitionsPct = $competitionsMax > 0 ? min(100, round($competitionsUsed / $competitionsMax * 100)) : 0;

                $leaguesUsed = $organization->competitions->where('type', 'league')->count();
                $leaguesMax = $plan->max_leagues_per_organization;
                $leaguesPct = $leaguesMax > 0 ? min(100, round($leaguesUsed / $leaguesMax * 100)) : 0;

                $orgsUsed = $organization->user->organizations->count();
                $orgsMax = $plan->max_organizations;
                $orgsPct = $orgsMax > 0 ? min(100, round($orgsUsed / $orgsMax * 100)) : 0;
            @endphp

            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-400">Organizacije (vlasnik)</span>
                    <span class="text-white font-medium">{{ $orgsUsed }} / {{ $orgsMax }}</span>
                </div>
                <div class="w-full bg-gray-700/50 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $orgsPct >= 100 ? 'bg-red-500' : 'bg-blue-500' }}" style="width: {{ $orgsPct }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-400">Takmičenja u organizaciji</span>
                    <span class="text-white font-medium">{{ $competitionsUsed }} / {{ $competitionsMax }}</span>
                </div>
                <div class="w-full bg-gray-700/50 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $competitionsPct >= 100 ? 'bg-red-500' : 'bg-purple-500' }}" style="width: {{ $competitionsPct }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-400">Lige u organizaciji</span>
                    <span class="text-white font-medium">{{ $leaguesUsed }} / {{ $leaguesMax }}</span>
                </div>
                <div class="w-full bg-gray-700/50 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $leaguesPct >= 100 ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ $leaguesPct }}%"></div>
                </div>
            </div>
        </div>
        @else
        <p class="text-gray-400">Korisnik nema aktivan plan (Free - bez ograničenja).</p>
        @endif
    </div>

    <!-- Competitions (turniri i lige) -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Takmičenja</h3>
        </div>

        @if($organization->competitions->count() > 0)
        <div class="divide-y divide-gray-700/50">
            @foreach($organization->competitions->sortByDesc('created_at') as $competition)
            <div class="p-6 hover:bg-gray-700/20 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">{{ $competition->sport->icon ?? '🏆' }}</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">{{ $competition->name }}</h4>
                            <p class="text-gray-400 text-sm">
                                {{ $competition->sport->name ?? '' }} • {{ $competition->matches->count() }} utakmica
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $competition->isTournament() ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' }}">
                            {{ $competition->isTournament() ? 'Turnir' : 'Liga' }}
                        </span>

                        @if($competition->isTournament())
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-700/50 text-gray-300 border border-gray-600/50">
                                Faza: {{ $competition->current_phase ?? '—' }}
                            </span>
                        @endif

                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $competition->status === 'completed' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : 'bg-green-500/20 text-green-400 border border-green-500/30' }}">
                            {{ $competition->status }}
                        </span>

                        <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-colors border border-blue-500/30">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h4 class="text-white font-semibold mb-2">Nema takmičenja</h4>
            <p class="text-gray-400">Ova organizacija još nema kreiranih turnira ili liga.</p>
        </div>
        @endif
    </div>
</div>
@endsection