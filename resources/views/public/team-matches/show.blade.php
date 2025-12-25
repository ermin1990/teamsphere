@extends('layouts.public')

@section('title', 'Detalji ekipnog meča - ' . $competition->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('public.leagues.show', $competition->slug) }}" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-white transition-colors mb-4 group">
                <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Nazad na {{ $competition->name }}
            </a>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight">Detalji Ekipnog Meča</h1>
                    <p class="text-gray-500 text-sm mt-1 font-medium">Pregled rezultata po pojedinačnim mečevima i setovima</p>
                </div>
                <div class="flex items-center gap-3">
                    @if($teamMatch->round)
                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-gray-400 uppercase tracking-widest">
                            Kolo {{ $teamMatch->round }}
                        </span>
                    @endif
                    <span class="px-3 py-1 rounded-full {{ $teamMatch->status === 'completed' ? 'bg-green-500/10 border-green-500/20 text-green-400' : 'bg-blue-500/10 border-blue-500/20 text-blue-400' }} border text-xs font-bold uppercase tracking-widest">
                        {{ $teamMatch->status === 'completed' ? 'Završeno' : 'U toku' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Scoreboard -->
        <div class="backdrop-blur-xl rounded-2xl border shadow-xl overflow-hidden mb-8" style="background: var(--bg-card); border-color: var(--border-primary);">
            <div class="px-6 py-4 border-b flex justify-between items-center" style="background: rgba(0,0,0,0.2); border-color: var(--border-primary);">
                <span class="text-sm font-medium text-gray-400 uppercase tracking-wider">Rezultat Susreta</span>
            </div>
            <div class="p-8 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="text-center flex-1">
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2 block">Domaćin</span>
                    <a href="{{ route('public.teams.show', $teamMatch->home_team_id) }}" class="hover:text-blue-400 transition-colors">
                        <h2 class="text-2xl md:text-4xl font-black text-white tracking-tight">{{ $teamMatch->homeTeam->name ?? 'Nepoznato' }}</h2>
                    </a>
                </div>
                <div class="flex flex-col items-center px-8 py-4 bg-white/5 rounded-2xl border border-white/5">
                    <div class="text-5xl md:text-7xl font-black text-white tracking-tighter leading-none">
                        {{ $teamMatch->home_score }}<span class="text-gray-600 mx-2">:</span>{{ $teamMatch->away_score }}
                    </div>
                    @if($teamMatch->scheduled_at)
                        <div class="text-[11px] text-gray-500 mt-4 font-bold uppercase tracking-widest">
                            {{ $teamMatch->scheduled_at->format('d. M Y. • H:i') }}
                        </div>
                    @endif
                </div>
                <div class="text-center flex-1">
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-2 block">Gost</span>
                    <a href="{{ route('public.teams.show', $teamMatch->away_team_id) }}" class="hover:text-blue-400 transition-colors">
                        <h2 class="text-2xl md:text-4xl font-black text-white tracking-tight">{{ $teamMatch->awayTeam->name ?? 'Nepoznato' }}</h2>
                    </a>
                </div>
            </div>
        </div>

        <!-- Captains and Referee -->
        <div class="backdrop-blur-xl rounded-2xl border shadow-xl overflow-hidden mb-8" style="background: var(--bg-card); border-color: var(--border-primary);">
            <div class="px-6 py-4 border-b" style="background: rgba(0,0,0,0.2); border-color: var(--border-primary);">
                <h3 class="font-bold text-white uppercase tracking-wider">Kapetani i Sudija</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Home Captain -->
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kapetan domaćina</p>
                        <p class="text-lg font-bold text-white">
                            @if($teamMatch->homeCaptain)
                                {{ $teamMatch->homeCaptain->name }}
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </p>
                    </div>

                    <!-- Away Captain -->
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kapetan gosta</p>
                        <p class="text-lg font-bold text-white">
                            @if($teamMatch->awayCaptain)
                                {{ $teamMatch->awayCaptain->name }}
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </p>
                    </div>

                    <!-- Referee -->
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sudija</p>
                        <p class="text-lg font-bold text-white">
                            @if($teamMatch->referee_name)
                                {{ $teamMatch->referee_name }}
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Individual Matches -->
        <div class="backdrop-blur-xl rounded-2xl border shadow-xl overflow-hidden" style="background: var(--bg-card); border-color: var(--border-primary);">
            <div class="px-6 py-4 border-b flex items-center justify-between" style="background: rgba(0,0,0,0.2); border-color: var(--border-primary);">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                    <h3 class="font-bold text-white text-sm md:text-base uppercase tracking-wider">Pojedinačni Mečevi</h3>
                </div>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest hidden md:block">Corbillon sistem</span>
            </div>
            
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead style="background: rgba(0,0,0,0.3);">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Domaćin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Gost</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Rezultat</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($matches as $match)
                        <tr class="{{ $match->status === 'completed' ? 'bg-gray-900/20' : 'hover:bg-gray-700/30' }} transition-colors duration-150 cursor-pointer" onclick="toggleSets({{ $match->id }})">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-400">
                                {{ $match->match_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                @if($match->position_code === 'Dubl')
                                    <div class="flex flex-col {{ $match->status === 'completed' && $match->home_score > $match->away_score ? 'text-green-400 font-bold' : '' }}">
                                        <span class="font-medium">{{ $doublesPlayers['home_1']->name ?? '?' }}</span>
                                        <span class="font-medium">{{ $doublesPlayers['home_2']->name ?? '?' }}</span>
                                    </div>
                                @else
                                    <span class="font-medium {{ $match->status === 'completed' && $match->home_score > $match->away_score ? 'text-green-400 font-bold' : '' }}">
                                        {{ $match->homePlayer->name ?? 'Nepoznat igrač' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                @if($match->position_code === 'Dubl')
                                    <div class="flex flex-col {{ $match->status === 'completed' && $match->away_score > $match->home_score ? 'text-green-400 font-bold' : '' }}">
                                        <span class="font-medium">{{ $doublesPlayers['away_1']->name ?? '?' }}</span>
                                        <span class="font-medium">{{ $doublesPlayers['away_2']->name ?? '?' }}</span>
                                    </div>
                                @else
                                    <span class="font-medium {{ $match->status === 'completed' && $match->away_score > $match->home_score ? 'text-green-400 font-bold' : '' }}">
                                        {{ $match->awayPlayer->name ?? 'Nepoznat igrač' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold">
                                @if($match->status === 'completed' || ($match->home_score > 0 || $match->away_score > 0))
                                    <span class="{{ $match->status === 'completed' ? 'text-white' : 'text-blue-400' }}">
                                        {{ $match->home_score }} : {{ $match->away_score }}
                                    </span>
                                @else
                                    <span class="text-gray-600">- : -</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-gray-400 hover:text-white transition-colors">
                                    <svg id="icon-{{ $match->id }}" class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr id="sets-{{ $match->id }}" class="hidden bg-black/40">
                            <td colspan="5" class="px-6 py-4">
                                <div class="flex flex-wrap justify-center gap-4">
                                    @php
                                        $sets = is_array($match->sets) ? $match->sets : (json_decode($match->sets, true) ?? []);
                                    @endphp
                                    @if(count($sets) > 0)
                                        @foreach($sets as $index => $set)
                                            @php
                                                $hScore = $set['home_score'] ?? $set['home'] ?? 0;
                                                $aScore = $set['away_score'] ?? $set['away'] ?? 0;
                                            @endphp
                                            <div class="flex flex-col items-center bg-gray-800/50 rounded-lg p-3 border border-gray-700 min-w-[80px]">
                                                <span class="text-[10px] uppercase text-gray-500 mb-1">Set {{ $index + 1 }}</span>
                                                <div class="flex items-center gap-2 font-bold text-lg">
                                                    <span class="{{ $hScore > $aScore ? 'text-green-400' : 'text-white' }}">
                                                        {{ $hScore }}
                                                    </span>
                                                    <span class="text-gray-600">:</span>
                                                    <span class="{{ $aScore > $hScore ? 'text-green-400' : 'text-white' }}">
                                                        {{ $aScore }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-gray-500 text-sm italic">Nema unesenih setova</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile List -->
            <div class="md:hidden divide-y divide-gray-700">
                @foreach($matches as $match)
                <div class="p-4 {{ $match->status === 'completed' ? 'bg-gray-900/10' : '' }} active:bg-gray-800 transition-colors" onclick="toggleSets({{ $match->id }})">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Meč {{ $match->match_order }}</span>
                        <svg id="icon-mobile-{{ $match->id }}" class="w-4 h-4 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    
                    <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                        <div class="text-left">
                            @if($match->position_code === 'Dubl')
                                <div class="flex flex-col text-xs">
                                    <span class="font-bold text-white">{{ $doublesPlayers['home_1']->name ?? '?' }}</span>
                                    <span class="font-bold text-white">{{ $doublesPlayers['home_2']->name ?? '?' }}</span>
                                </div>
                            @else
                                <span class="text-sm font-bold {{ $match->status === 'completed' && $match->home_score > $match->away_score ? 'text-green-400' : 'text-white' }}">
                                    {{ $match->homePlayer->name ?? 'Nepoznat' }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex flex-col items-center min-w-[50px]">
                            @if($match->status === 'completed' || ($match->home_score > 0 || $match->away_score > 0))
                                <span class="text-lg font-black {{ $match->status === 'completed' ? 'text-white' : 'text-blue-400' }}">
                                    {{ $match->home_score }} : {{ $match->away_score }}
                                </span>
                            @else
                                <span class="text-xs font-bold text-gray-600 uppercase">VS</span>
                            @endif
                        </div>

                        <div class="text-right">
                            @if($match->position_code === 'Dubl')
                                <div class="flex flex-col text-xs">
                                    <span class="font-bold text-white">{{ $doublesPlayers['away_1']->name ?? '?' }}</span>
                                    <span class="font-bold text-white">{{ $doublesPlayers['away_2']->name ?? '?' }}</span>
                                </div>
                            @else
                                <span class="text-sm font-bold {{ $match->status === 'completed' && $match->away_score > $match->home_score ? 'text-green-400' : 'text-white' }}">
                                    {{ $match->awayPlayer->name ?? 'Nepoznat' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Mobile Sets -->
                    <div id="sets-mobile-{{ $match->id }}" class="hidden mt-4 pt-4 border-t border-gray-700/50">
                        <div class="flex flex-wrap justify-center gap-2">
                            @php
                                $sets = is_array($match->sets) ? $match->sets : (json_decode($match->sets, true) ?? []);
                            @endphp
                            @if(count($sets) > 0)
                                @foreach($sets as $index => $set)
                                    @php
                                        $hScore = $set['home_score'] ?? $set['home'] ?? 0;
                                        $aScore = $set['away_score'] ?? $set['away'] ?? 0;
                                    @endphp
                                    <div class="flex flex-col items-center bg-gray-800/30 rounded-lg p-2 border border-gray-700/50 min-w-[60px]">
                                        <span class="text-[9px] uppercase text-gray-500 mb-1">S{{ $index + 1 }}</span>
                                        <div class="flex items-center gap-1 font-bold text-sm">
                                            <span class="{{ $hScore > $aScore ? 'text-green-400' : 'text-white' }}">{{ $hScore }}</span>
                                            <span class="text-gray-600">:</span>
                                            <span class="{{ $aScore > $hScore ? 'text-green-400' : 'text-white' }}">{{ $aScore }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-gray-500 text-xs italic">Nema unesenih setova</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function toggleSets(matchId) {
    // Desktop
    const row = document.getElementById('sets-' + matchId);
    const icon = document.getElementById('icon-' + matchId);
    
    if (row) {
        if (row.classList.contains('hidden')) {
            row.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
        } else {
            row.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
        }
    }

    // Mobile
    const mobileRow = document.getElementById('sets-mobile-' + matchId);
    const mobileIcon = document.getElementById('icon-mobile-' + matchId);
    
    if (mobileRow) {
        if (mobileRow.classList.contains('hidden')) {
            mobileRow.classList.remove('hidden');
            if (mobileIcon) mobileIcon.classList.add('rotate-180');
        } else {
            mobileRow.classList.add('hidden');
            if (mobileIcon) mobileIcon.classList.remove('rotate-180');
        }
    }
}
</script>
@endsection
