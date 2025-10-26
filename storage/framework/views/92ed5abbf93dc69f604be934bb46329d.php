
<?php
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
?>


<?php if($knockoutMatches && $knockoutMatches->count() > 0): ?>
    <?php echo $__env->make('organizations.competitions.partials.tournament.knockout-phase', [
        'knockoutMatches' => $knockoutMatches,
        'organization' => $organization,
        'competition' => $competition,
        'isOwner' => $isOwner,
        'isRefereeForMatch' => $isRefereeForMatch
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>


<?php if($competition->tournamentGroups->count() > 0): ?>
    <?php echo $__env->make('organizations.competitions.partials.tournament.group-phase', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/partials/tournament-content.blade.php ENDPATH**/ ?>