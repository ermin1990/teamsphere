@extends('layouts.public')

@section('title', $team->name . ' - ' . $competition->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="backdrop-blur-xl rounded-2xl p-6 md:p-10 shadow-xl mb-8 border" style="background: var(--bg-card); border-color: var(--border-primary);">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center text-2xl font-black text-white shadow-xl">
                    {{ substr($team->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                        {{ $team->name }}
                    </h1>
                    <p class="text-blue-400 font-bold uppercase tracking-widest text-xs mt-1">
                        {{ $competition->name }}
                    </p>
                </div>
            </div>
            <a href="{{ route('teams.show', $team->id) }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-white font-bold transition-all flex items-center gap-2">
                <span>&larr;</span> Nazad na profil
            </a>
        </div>
    </div>

    <!-- Matches List -->
    <div class="backdrop-blur-xl rounded-2xl border shadow-xl overflow-hidden" style="background: var(--bg-card); border-color: var(--border-primary);">
        <div class="px-6 py-4 border-b flex items-center gap-3" style="background: rgba(0,0,0,0.2); border-color: var(--border-primary);">
            <span class="text-xl">🎯</span>
            <h3 class="font-bold text-white uppercase tracking-wider">Sve utakmice u takmičenju</h3>
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
                    @foreach($matches as $match)
                    @php
                        $isHome = $match->home_team_id === $team->id;
                        $opponent = $isHome ? $match->awayTeam : $match->homeTeam;
                        $won = ($isHome && $match->home_score > $match->away_score) || (!$isHome && $match->away_score > $match->home_score);
                        $lost = ($isHome && $match->home_score < $match->away_score) || (!$isHome && $match->away_score < $match->home_score);
                        $matchUrl = route('competitions.team-matches.show', [$match->competition->slug, $match->id]);
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
                                <a href="{{ route('teams.show', $opponent->id) }}" class="text-sm font-bold text-white hover:text-blue-400 transition-colors" onclick="event.stopPropagation()">
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
        @if($matches->hasPages())
            <div class="px-6 py-4 border-t border-gray-800 bg-black/20">
                {{ $matches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
