{{-- Tournament Content --}}
@php
    // Generate knockout and group matches
    $knockoutMatches = App\Models\CompetitionMatch::where('competition_id', $competition->id)
        ->where('phase', 'knockout')
        ->with(['homePlayer', 'awayPlayer'])
        ->orderBy('round_number')
        ->orderBy('match_order')
        ->get();

    $groupMatches = App\Models\CompetitionMatch::where('competition_id', $competition->id)
        ->whereNotNull('tournament_group_id')
        ->with(['homePlayer', 'awayPlayer', 'tournamentGroup'])
        ->orderBy('tournament_group_id')
        ->orderBy('id')
        ->get()
        ->groupBy('tournament_group_id');

    $isRefereeForMatch = function($match) use ($isReferee) {
        return $isReferee || $match->referee_user_id === auth()->id();
    };
@endphp

{{-- Knockout Phase Section (ABOVE Group Phase) --}}
@if($knockoutMatches && $knockoutMatches->count() > 0)
    @include('organizations.competitions.partials.tournament.knockout-phase', [
        'knockoutMatches' => $knockoutMatches,
        'organization' => $organization,
        'competition' => $competition,
        'isOwner' => $isOwner,
        'isRefereeForMatch' => $isRefereeForMatch
    ])
@endif

{{-- Group Phase Section --}}
@if($competition->tournamentGroups->count() > 0)
    @include('organizations.competitions.partials.tournament.group-phase')
@endif
