@php
    // Leagues where this player can self-log a brand new match (same rules
    // as PlayerMatchController::currentPlayerIn - individual, active leagues).
    $newMatchCompetitions = \App\Models\Competition::whereHas('players', function ($q) {
            $q->where('players.user_id', auth()->id());
        })
        ->where('is_team_based', false)
        ->where('status', 'active')
        ->orderBy('name')
        ->get();
@endphp
@if($newMatchCompetitions->count() === 1)
    <a href="{{ route('player.matches.create', $newMatchCompetitions->first()) }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all active:scale-95"
       style="background: var(--accent-blue); color: #14141F;">
        + Novi meč
    </a>
@elseif($newMatchCompetitions->count() > 1)
    <select onchange="if(this.value) window.location = this.value;"
            class="mt-input px-3 py-2 rounded-full text-xs font-semibold focus:outline-none transition-all"
            style="background: var(--accent-blue); color: #14141F; border: none;">
        <option value="">+ Novi meč...</option>
        @foreach($newMatchCompetitions as $newMatchCompetition)
            <option value="{{ route('player.matches.create', $newMatchCompetition) }}">{{ $newMatchCompetition->name }}</option>
        @endforeach
    </select>
@endif
