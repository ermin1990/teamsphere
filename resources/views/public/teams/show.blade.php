@extends('layouts.public')

@section('title', $team->name . ' - Profil kluba')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Team Header -->
    <div class="backdrop-blur-xl rounded-2xl p-6 md:p-10 shadow-xl mb-8 border" style="background: var(--bg-card); border-color: var(--border-primary);">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="w-24 h-24 md:w-32 md:h-32 bg-gradient-to-br from-blue-600 to-purple-600 rounded-3xl flex items-center justify-center text-4xl md:text-5xl font-black text-white shadow-2xl shadow-blue-500/20">
                {{ substr($team->name, 0, 1) }}
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight mb-2">
                    {{ $team->name }}
                </h1>
                <div class="flex flex-wrap justify-center md:justify-start items-center gap-4 text-gray-400 font-medium">
                    <span class="flex items-center gap-2">
                        <span class="text-blue-400">🏢</span>
                        {{ $team->organization->name }}
                    </span>
                    @if($team->league)
                    <span class="flex items-center gap-2">
                        <span class="text-purple-400">🏆</span>
                        {{ $team->league->name }}
                    </span>
                    @endif
                    @php $activeCoach = $team->activeCoach(); @endphp
                    @if($activeCoach)
                    <span class="flex items-center gap-2">
                        <span class="text-emerald-400">📋</span>
                        Trener: {{ $activeCoach->name }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Roster -->
        <div class="lg:col-span-1 space-y-8">
            <div class="backdrop-blur-xl rounded-2xl border shadow-xl overflow-hidden" style="background: var(--bg-card); border-color: var(--border-primary);">
                <div class="px-6 py-4 border-b flex items-center gap-3" style="background: rgba(0,0,0,0.2); border-color: var(--border-primary);">
                    <span class="text-xl">👥</span>
                    <h3 class="font-bold text-white uppercase tracking-wider">Sastav Ekipe</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        @forelse($team->players as $player)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5 hover:border-blue-500/30 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-800 flex items-center justify-center text-xs font-bold text-gray-400 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    {{ $loop->iteration }}
                                </div>
                                <span class="font-bold text-gray-200">{{ $player->name }}</span>
                            </div>
                            @if($player->pivot->role === 'captain')
                            <span class="text-[10px] font-black bg-yellow-500/20 text-yellow-500 px-2 py-0.5 rounded-md uppercase tracking-tighter">Kapiten</span>
                            @endif
                        </div>
                        @empty
                        <p class="text-center text-gray-500 py-4">Nema registrovanih igrača.</p>
                        @endforelse
                    </div>

                    @if($activeCoach)
                    <div class="mt-6 pt-4 border-t border-white/5">
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-blue-500/5 border border-blue-500/10">
                            <div class="w-10 h-10 rounded-lg bg-blue-600/20 flex items-center justify-center text-xl">
                                📋
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Trener ekipe</div>
                                <div class="font-bold text-white">{{ $activeCoach->name }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Matches -->
        <div class="lg:col-span-2 space-y-8">
            @forelse($matches as $competitionId => $competitionMatches)
                @php
                    $competition = $competitionMatches->first()->competition;
                    $displayMatches = $competitionMatches->take(10);
                    $hasMore = $competitionMatches->count() > 10;
                @endphp
                <div class="backdrop-blur-xl rounded-2xl border shadow-xl overflow-hidden" style="background: var(--bg-card); border-color: var(--border-primary);">
                    <div class="px-6 py-4 border-b flex items-center justify-between" style="background: rgba(0,0,0,0.2); border-color: var(--border-primary);">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🎯</span>
                            <h3 class="font-bold text-white uppercase tracking-wider">{{ $competition->name }}</h3>
                        </div>
                        @if($hasMore)
                            <a href="{{ route('public.teams.competition-matches', [$team->id, $competition->id]) }}" class="text-xs font-bold text-blue-500 hover:text-blue-400 uppercase tracking-widest">Prikaži sve &rarr;</a>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-800">
                            <thead style="background: rgba(0,0,0,0.3);">
                                <tr>
                                    <th class="hidden md:table-cell px-6 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Datum</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Protivnik</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Rezultat</th>
                                    <th class="hidden md:table-cell px-6 py-4 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @foreach($displayMatches as $match)
                                @php
                                    $isHome = $match->home_team_id === $team->id;
                                    $opponent = $isHome ? $match->awayTeam : $match->homeTeam;
                                    $won = ($isHome && $match->home_score > $match->away_score) || (!$isHome && $match->away_score > $match->home_score);
                                    $lost = ($isHome && $match->home_score < $match->away_score) || (!$isHome && $match->away_score < $match->home_score);
                                    $matchUrl = route('public.team-matches.show', [$match->competition->slug, $match->id]);
                                @endphp
                                <tr class="hover:bg-white/5 transition-colors cursor-pointer" onclick="window.location='{{ $matchUrl }}'">
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs font-bold text-gray-400">
                                            {{ $match->scheduled_at ? $match->scheduled_at->format('d.m.Y') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-left">
                                        <div class="flex items-center justify-start gap-3">
                                            <span class="text-[9px] font-black px-2 py-0.5 rounded {{ $isHome ? 'bg-blue-500/20 text-blue-400' : 'bg-purple-500/20 text-purple-400' }} uppercase tracking-tighter min-w-[45px] text-center">
                                                {{ $isHome ? 'Kući' : 'Gosti' }}
                                            </span>
                                            <a href="{{ route('public.teams.show', $opponent->id) }}" class="text-sm font-bold text-white hover:text-blue-400 transition-colors" onclick="event.stopPropagation()">
                                                {{ $opponent->name }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($match->status === 'completed')
                                        <div class="inline-flex items-center gap-2">
                                            <span class="text-lg font-black text-white">
                                                {{ $isHome ? $match->home_score : $match->away_score }} : {{ $isHome ? $match->away_score : $match->home_score }}
                                            </span>
                                            <span class="w-6 h-6 rounded-md flex items-center justify-center text-[10px] font-black uppercase {{ $won ? 'bg-green-500/20 text-green-500' : ($lost ? 'bg-red-500/20 text-red-500' : 'bg-gray-500/20 text-gray-500') }}">
                                                {{ $won ? 'P' : ($lost ? 'I' : 'N') }}
                                            </span>
                                        </div>
                                        @else
                                        <span class="text-xs font-bold text-gray-600 uppercase tracking-widest">Nije odigrano</span>
                                        @endif
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ $matchUrl }}" class="text-xs font-bold text-blue-500 hover:text-blue-400 uppercase tracking-widest">Detalji &rarr;</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="backdrop-blur-xl rounded-2xl border shadow-xl overflow-hidden p-10 text-center" style="background: var(--bg-card); border-color: var(--border-primary);">
                    <p class="text-gray-500 font-medium">Nema odigranih utakmica.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection