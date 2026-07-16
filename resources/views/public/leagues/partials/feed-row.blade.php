@php
    /** Compact cross-league match row: used for the "recent results" /
     *  "upcoming matches" feeds on the public leagues browse page, where
     *  matches from many different competitions/sports are mixed together
     *  so the competition name and sport matter more than per-set detail. */
    $competition = $match->competition;
    $isTeamMatch = $competition->is_team_based;

    $homeName = $isTeamMatch
        ? ($match->homeTeam->name ?? 'Domaći')
        : ($match->homePlayer->name ?? 'Domaći');
    $awayName = $isTeamMatch
        ? ($match->awayTeam->name ?? 'Gost')
        : ($match->awayPlayer->name ?? 'Gost');

    $live = $match->status === 'in_progress';
@endphp
<a href="{{ route('competitions.show', $competition) }}"
   class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl transition-all duration-200"
   style="background: var(--bg-hover); border: 1px solid var(--border-secondary);">
    <div class="flex items-center gap-2.5 min-w-0">
        <span class="text-lg shrink-0">{{ $competition->sport->icon ?? '🏆' }}</span>
        <div class="min-w-0">
            <div class="text-sm font-semibold truncate" style="color: var(--text-primary);">
                {{ $homeName }} <span style="color: var(--text-muted);">vs</span> {{ $awayName }}
            </div>
            <div class="text-xs truncate" style="color: var(--text-tertiary);">{{ $competition->name }}</div>
        </div>
    </div>
    <div class="text-right shrink-0">
        @if($type === 'result')
            <div class="text-sm font-black" style="color: var(--text-primary);">{{ $match->home_score }}:{{ $match->away_score }}</div>
            <div class="text-[10px]" style="color: var(--text-muted);">{{ optional($match->played_at)->format('d.m.') }}</div>
        @else
            @if($live)
                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide animate-pulse" style="background: rgba(248,113,113,0.2); color: #f87171;">🔴 Uživo</span>
            @else
                <div class="text-xs font-semibold" style="color: var(--text-primary);">{{ optional($match->scheduled_at)->format('d.m.') }}</div>
                <div class="text-[10px]" style="color: var(--text-muted);">{{ optional($match->scheduled_at)->format('H:i') }}</div>
            @endif
        @endif
    </div>
</a>
