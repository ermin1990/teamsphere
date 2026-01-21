<!-- Competition View Partial for Projector -->
<div class="h-full flex flex-col">
    @if($mode === 'standings' || $mode === 'both')
        <!-- Standings Section -->
        <div class="{{ $mode === 'both' && $layout === 'split' ? 'flex-1' : '' }}">
            @if($competition->type === 'league')
                @include('projector.partials.league-standings', ['competition' => $competition])
            @else
                @php
                    // Get phase selection (auto, groups, knockout)
                    $phaseSelection = $phase ?? 'auto';
                    
                    // Check if tournament has knockout matches
                    $hasKnockoutMatches = $competition->matches->where('phase', 'knockout')->isNotEmpty();
                    $hasGroupMatches = $competition->matches->whereNotNull('tournament_group_id')->isNotEmpty();
                    
                    // Determine which phase to show
                    if ($phaseSelection === 'knockout') {
                        // Manual selection: always show knockout view
                        $showKnockout = true;
                        $showAllGroups = false;
                    } elseif ($phaseSelection === 'groups') {
                        // Manual selection: show single group (rotated)
                        $showKnockout = false;
                        $showAllGroups = false;
                    } else {
                        // Auto detection: Show knockout if exists and groups are mostly completed
                        $showKnockout = $hasKnockoutMatches && (!$hasGroupMatches || $competition->matches->whereNotNull('tournament_group_id')->where('status', 'completed')->count() > 0);
                        // Auto mode shows single group when rotating through groups
                        $showAllGroups = false;
                    }
                @endphp
                
                @if($showKnockout)
                    {{-- Knockout Phase Display --}}
                    @include('projector.partials.knockout-bracket', ['competition' => $competition])
                @elseif($showAllGroups)
                    {{-- All Groups Display (for manual "groups" selection) --}}
                    @include('projector.partials.tournament-all-groups', ['competition' => $competition])
                @else
                    {{-- Single Group Display (for rotation or auto with single group) --}}
                    @include('projector.partials.tournament-standings', ['competition' => $competition, 'selectedGroup' => $selectedGroup ?? null])
                @endif
            @endif
        </div>
    @endif

    @if($mode === 'matches' || $mode === 'both')
        <!-- Matches Section -->
        <div class="{{ $mode === 'both' && $layout === 'split' ? 'flex-1 mt-4' : '' }}">
            @include('projector.partials.live-matches', ['competition' => $competition])
        </div>
    @endif
</div>
