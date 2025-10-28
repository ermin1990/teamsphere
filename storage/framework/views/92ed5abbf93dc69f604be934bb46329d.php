
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


<?php
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
?>

<?php if($tournamentWinner): ?>
<div class="bg-gradient-to-r from-yellow-500/20 to-orange-500/20 backdrop-blur-xl rounded-2xl p-6 border border-yellow-500/30 shadow-xl">
    <div class="text-center">
        <div class="text-sm text-yellow-400 font-medium mb-1">Pobjednik/Pobjednica turnira</div>
        <div class="text-xl font-bold text-white"><?php echo e($tournamentWinner->name); ?></div>
    </div>
</div>
<?php endif; ?>


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