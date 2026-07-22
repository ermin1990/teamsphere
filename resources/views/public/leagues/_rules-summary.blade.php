{{--
    Sport-aware read-only summary of every rule configured on the organizer's
    Postavke page - shared by the public "Pravila" tab and the inline rules
    section on the player's competition view. Expects $competition.
    Point-based sports (stoni tenis, badminton, padel bodovni) show the full
    set/point/deuce/tiebreak breakdown - sets-games sports (Tenis, Padel)
    only track sets_to_win, the rest of their match format is the fixed
    standard game/tiebreak format.
--}}
<div class="space-y-4 mb-6">
    <div>
        <h3 class="font-headline-md mb-3 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[20px]">sports_score</span> Format meča</h3>
        @if($competition->sport->isPointsBased())
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @if($competition->sets_to_win)
                    <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                        <p class="text-label-bold text-on-surface-variant uppercase">Setovi za pobjedu</p>
                        <p class="font-bold text-lg">{{ $competition->sets_to_win }}</p>
                    </div>
                @endif
                @if($competition->points_per_set)
                    <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                        <p class="text-label-bold text-on-surface-variant uppercase">Poena po setu</p>
                        <p class="font-bold text-lg">{{ $competition->points_per_set }}</p>
                    </div>
                @endif
                @if($competition->deuce_at)
                    <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                        <p class="text-label-bold text-on-surface-variant uppercase">Deuce na</p>
                        <p class="font-bold text-lg">{{ $competition->deuce_at }}</p>
                    </div>
                @endif
                <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                    <p class="text-label-bold text-on-surface-variant uppercase">Pobjeda razlikom od 2</p>
                    <p class="font-bold text-lg">{{ $competition->must_win_by_two ? 'Da' : 'Ne' }}</p>
                </div>
                <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                    <p class="text-label-bold text-on-surface-variant uppercase">Tiebreak</p>
                    <p class="font-bold text-lg">{{ $competition->has_tiebreak ? ('Da, do ' . $competition->tiebreak_points . ' poena') : 'Ne' }}</p>
                </div>
            </div>
        @elseif($competition->sport->isSetsGamesBased())
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
                @if($competition->sets_to_win)
                    <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                        <p class="text-label-bold text-on-surface-variant uppercase">Setovi za pobjedu</p>
                        <p class="font-bold text-lg">{{ $competition->sets_to_win }}</p>
                    </div>
                @endif
            </div>
            <p class="text-sm text-on-surface-variant bg-surface-container-low border border-outline-variant rounded-xl p-4">
                Set se igra do 6 gemova (razlika 2), sa tie-breakom na 6-6 - standardna pravila za {{ $competition->sport->name }}.
                Gem se igra po klasičnom sistemu (0, 15, 30, 40, deuce/prednost).
            </p>
        @endif
    </div>

    <div>
        <h3 class="font-headline-md mb-3 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[20px]">military_tech</span> Bodovanje</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                <p class="text-label-bold text-on-surface-variant uppercase">Pobjeda</p>
                <p class="font-bold text-lg">{{ $competition->points_for_win ?? '-' }} bod.</p>
            </div>
            <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                <p class="text-label-bold text-on-surface-variant uppercase">Neriješeno</p>
                <p class="font-bold text-lg">{{ $competition->points_for_draw ?? '-' }} bod.</p>
            </div>
            <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                <p class="text-label-bold text-on-surface-variant uppercase">Poraz</p>
                <p class="font-bold text-lg">{{ $competition->points_for_loss ?? '-' }} bod.</p>
            </div>
        </div>
    </div>

    @if($competition->isLeague())
        <div>
            <h3 class="font-headline-md mb-3 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[20px]">event_busy</span> Odustajanje (WO)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                    <p class="text-label-bold text-primary uppercase mb-2">Pobjednik</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-on-surface-variant">Bodovi</span>
                        <span class="font-bold">{{ $competition->forfeitWinnerPoints() }} bod.</span>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-sm text-on-surface-variant">Računa se kao odigran</span>
                        <span class="font-bold">{{ $competition->forfeit_winner_counts_as_played ? 'Da' : 'Ne' }}</span>
                    </div>
                </div>
                <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                    <p class="text-label-bold text-secondary uppercase mb-2">Onaj ko je odustao</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-on-surface-variant">Bodovi</span>
                        <span class="font-bold">{{ $competition->forfeit_loser_counts_as_played ? $competition->forfeitLoserPoints() : ($competition->forfeit_loser_points ?? 0) }} bod.</span>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-sm text-on-surface-variant">Računa se kao odigran</span>
                        <span class="font-bold">{{ $competition->forfeit_loser_counts_as_played ? 'Da' : 'Ne' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <h3 class="font-headline-md mb-3 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[20px]">tune</span> Postavke lige</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                    <p class="text-label-bold text-on-surface-variant uppercase">Dvokružni sistem</p>
                    <p class="font-bold text-lg">{{ $competition->is_double_round ? 'Da' : 'Ne' }}</p>
                </div>
                <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                    <p class="text-label-bold text-on-surface-variant uppercase">Rekreativna liga</p>
                    <p class="font-bold text-lg">{{ $competition->is_recreational ? 'Da' : 'Ne' }}</p>
                </div>
                <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                    <p class="text-label-bold text-on-surface-variant uppercase">Dozvoljeni revanš mečevi</p>
                    <p class="font-bold text-lg">{{ $competition->allow_rematches ? 'Da' : 'Ne' }}</p>
                </div>
            </div>
        </div>
    @else
        <div>
            <h3 class="font-headline-md mb-3 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[20px]">tune</span> Postavke turnira</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                    <p class="text-label-bold text-on-surface-variant uppercase">Igrača napreduje po grupi</p>
                    <p class="font-bold text-lg">{{ $competition->players_advancing_per_group ?? '-' }}</p>
                </div>
                <div class="p-4 bg-surface-container-low border border-outline-variant rounded-lg">
                    <p class="text-label-bold text-on-surface-variant uppercase">Krugova u grupama</p>
                    <p class="font-bold text-lg">{{ $competition->group_rounds ?? '-' }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
