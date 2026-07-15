@php
    /** Shared match card (mockup style): two player rows with per-set game
     *  cells + a stacked score-box column (sets won), winner highlighted.
     *  Not a link - just a result summary (venue/time shown below if set). */
    $isTeamMatch = $competition->is_team_based && $competition->type === 'league';

    $homeName = $isTeamMatch
        ? ($match->homeTeam?->name ?? 'Domaći')
        : (($match->position_code === 'Dubl') ? 'Dubl' : ($match->homePlayer?->name ?? 'TBD'));
    $awayName = $isTeamMatch
        ? ($match->awayTeam?->name ?? 'Gosti')
        : (($match->position_code === 'Dubl') ? 'Dubl' : ($match->awayPlayer?->name ?? 'TBD'));

    $hs = $match->home_score ?? 0;
    $as = $match->away_score ?? 0;
    $completed = in_array($match->status, ['completed', 'forfeited']);
    $live = $match->status === 'in_progress';
    $scheduled = $match->status === 'scheduled';
    $homeWin = $completed && $hs > $as;
    $awayWin = $completed && $as > $hs;

    // Per-set game scores (individual matches only)
    $sets = (!$isTeamMatch)
        ? collect($match->sets ?? [])->map(fn ($s) => [
            'h' => $s['home'] ?? $s['home_score'] ?? $s['p1'] ?? null,
            'a' => $s['away'] ?? $s['away_score'] ?? $s['p2'] ?? null,
        ])->filter(fn ($s) => !(($s['h'] === null || $s['h'] === '') && ($s['a'] === null || $s['a'] === '')))->values()
        : collect();
    // Cap the empty-cell placeholders at the league's actual max sets
    // (best-of-(2*sets_to_win - 1)) instead of a hardcoded 5.
    $maxPossibleSets = max(1, (2 * ($competition->sets_to_win ?: 1)) - 1);
    $cellCount = max($maxPossibleSets, $sets->count());
@endphp
@once
<style>
    .mt-match { background: var(--bg-hover); border: 1px solid var(--border-secondary); transition: border-color .2s ease; }
    .mt-match:hover { border-color: var(--border-accent); }
</style>
@endonce
<div class="mt-match relative overflow-hidden block rounded-xl transition-all duration-300">
    @if($completed)
        <div class="absolute left-0 top-0 bottom-0 w-[3px]" style="background: var(--accent-green-solid);"></div>
    @elseif($live)
        <div class="absolute left-0 top-0 bottom-0 w-[3px] animate-pulse" style="background: var(--accent-blue);"></div>
    @endif
    @if($live)
        <div class="px-3 md:px-3.5 pl-4 pt-2">
            <span class="inline-block px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide animate-pulse" style="background: rgba(248,113,113,0.2); color: #f87171;">🔴 Uživo</span>
        </div>
    @endif
    <div class="p-3 md:p-3.5 pl-4">
        <div class="flex items-stretch gap-3">
            <div class="flex-1 flex flex-col justify-between gap-2.5 min-w-0">
                {{-- Home row --}}
                <div class="flex items-center justify-between gap-2 min-w-0">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full" style="background: {{ $homeWin ? 'var(--accent-green-solid)' : 'transparent' }};"></div>
                        <div class="text-[12px] md:text-[13px] font-bold truncate" style="color: {{ $homeWin ? 'var(--text-primary)' : ($awayWin ? 'var(--text-muted)' : 'var(--text-secondary)') }};">{{ $homeName }}</div>
                    </div>
                    <div class="flex items-center gap-0.5 ml-auto">
                        @for($i = 0; $i < $cellCount; $i++)
                            @php $c = $sets[$i] ?? null; $winCell = $c && (int) $c['h'] >= (int) $c['a']; @endphp
                            <div class="w-[18px] text-center">
                                @if($c && $c['h'] !== null && $c['h'] !== '')
                                    <span class="text-[10px] font-black" style="color: {{ $winCell ? 'var(--accent-green-solid)' : 'var(--text-muted)' }};">{{ $c['h'] }}</span>
                                @else
                                    <span class="text-[10px]" style="color: var(--text-muted); opacity: .4;">-</span>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
                {{-- Away row --}}
                <div class="flex items-center justify-between gap-2 min-w-0">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full" style="background: {{ $awayWin ? 'var(--accent-green-solid)' : 'transparent' }};"></div>
                        <div class="text-[12px] md:text-[13px] font-bold truncate" style="color: {{ $awayWin ? 'var(--text-primary)' : ($homeWin ? 'var(--text-muted)' : 'var(--text-secondary)') }};">{{ $awayName }}</div>
                    </div>
                    <div class="flex items-center gap-0.5 ml-auto">
                        @for($i = 0; $i < $cellCount; $i++)
                            @php $c = $sets[$i] ?? null; $winCell = $c && (int) $c['a'] >= (int) $c['h']; @endphp
                            <div class="w-[18px] text-center">
                                @if($c && $c['a'] !== null && $c['a'] !== '')
                                    <span class="text-[10px] font-black" style="color: {{ $winCell ? 'var(--accent-green-solid)' : 'var(--text-muted)' }};">{{ $c['a'] }}</span>
                                @else
                                    <span class="text-[10px]" style="color: var(--text-muted); opacity: .4;">-</span>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
            {{-- Score boxes (sets won) --}}
            <div class="flex flex-col items-center justify-center gap-1.5 pl-3 min-w-[36px]" style="border-left: 1px solid var(--border-secondary);">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-300" style="{{ $homeWin ? 'background: var(--accent-green-solid); color: #fff;' : 'background: var(--bg-tertiary); color: var(--text-muted);' }}">
                    <span class="text-[13px] font-black italic">{{ $scheduled ? 0 : $hs }}</span>
                </div>
                <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-300" style="{{ $awayWin ? 'background: var(--accent-green-solid); color: #fff;' : 'background: var(--bg-tertiary); color: var(--text-muted);' }}">
                    <span class="text-[13px] font-black italic">{{ $scheduled ? 0 : $as }}</span>
                </div>
            </div>
        </div>
        @if($match->venue || $match->played_at)
            <div class="flex items-center justify-center gap-2 mt-2 pt-2 text-[9px]" style="border-top: 1px solid var(--border-secondary); color: var(--text-muted);">
                @if($match->venue)
                    <span>📍 {{ $match->venue->name }}</span>
                @endif
                @if($match->played_at)
                    <span>{{ $match->played_at->format('H:i') }}</span>
                @endif
            </div>
        @endif
    </div>
</div>
