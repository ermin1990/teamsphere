{{-- Futsal Tournament Content --}}
@php
    // Generate knockout and group matches for futsal
    $knockoutMatches = App\Models\CompetitionMatch::where('competition_id', $competition->id)
        ->where('phase', 'knockout')
        ->with(['homeTeam', 'awayTeam'])
        ->orderBy('round_number')
        ->orderBy('match_order')
        ->get();

    // For futsal, group matches come from futsal_matches table via relationships
    $groupMatches = App\Models\TournamentGroup::where('competition_id', $competition->id)
        ->with([
            'futsalMatches' => function ($query) {
                $query->with(['homeTeam', 'awayTeam'])->orderBy('round');
            }
        ])
        ->orderBy('group_number')
        ->get()
        ->keyBy('id')
        ->map(function ($group) {
            return $group->futsalMatches->groupBy('round');
        });

    $isRefereeForMatch = function($match) use ($isReferee) {
        return $isReferee || $match->referee_user_id === auth()->id();
    };
@endphp

{{-- Tournament Winner Section --}}
@php
    $tournamentWinner = null;
    if($knockoutMatches && $knockoutMatches->count() > 0) {
        $maxRound = $knockoutMatches->max('round_number');
        $finalRoundMatches = $knockoutMatches->where('round_number', $maxRound);

        // If there's only 1 match in the final round and it's completed, show the winner
        if($finalRoundMatches->count() === 1) {
            $finalMatch = $finalRoundMatches->first();
            if($finalMatch->status === 'completed' && $finalMatch->getWinner()) {
                $tournamentWinner = $finalMatch->getWinner();
            }
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
    @include('organizations.competitions.futsal.partials.tournament.knockout-phase', [
        'knockoutMatches' => $knockoutMatches,
        'organization' => $organization,
        'competition' => $competition,
        'isOwner' => $isOwner,
        'isRefereeForMatch' => $isRefereeForMatch
    ])
@endif

{{-- Group Phase Section --}}
@if($competition->tournamentGroups->count() > 0)
    @include('organizations.competitions.futsal.partials.tournament.group-phase')
@endif