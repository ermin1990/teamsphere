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

{{-- Tournament Winner Section --}}
@php
    $tournamentWinner = null;
    if($knockoutMatches && $knockoutMatches->count() > 0) {
        // Find the final match (highest round number)
        $finalMatch = $knockoutMatches->where('status', 'completed')->sortByDesc('round_number')->first();
        if($finalMatch && $finalMatch->getWinner()) {
            $tournamentWinner = $finalMatch->getWinner();
        }
    }
@endphp

@if($tournamentWinner)
<div class="bg-gradient-to-r from-yellow-500/20 to-orange-500/20 backdrop-blur-xl rounded-2xl p-6 border border-yellow-500/30 shadow-xl">
    <div class="text-center">
        <div class="text-sm text-yellow-400 font-medium mb-1">Pobjednik/Pobjednica turnira</div>
        <div class="text-xl font-bold text-white">{{ $tournamentWinner->name }}</div>
    </div>
</div>
@endif

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
