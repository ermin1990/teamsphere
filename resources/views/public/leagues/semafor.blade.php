@extends('layouts.public')

@section('title', $competition->name . ' - Semafor - MojTurnir')

@push('styles')
<style>
/* Full screen semafor styles */
.semafor-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
}

.semafor-header {
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(20px);
    border-bottom: 2px solid rgba(59, 130, 246, 0.3);
}

.group-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.group-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-2px);
}

.standings-table {
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
}

.standings-row {
    transition: all 0.2s ease;
}

.standings-row:hover {
    background: rgba(59, 130, 246, 0.1);
}

.match-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.live-match {
    animation: pulse 2s infinite;
    border-color: #10b981 !important;
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.bracket-line {
    position: relative;
}

.bracket-line::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: rgba(59, 130, 246, 0.5);
    transform: translateY(-50%);
}

.bracket-connector {
    position: relative;
}

.bracket-connector::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 2px;
    background: rgba(59, 130, 246, 0.5);
    transform: translateX(-50%);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .semafor-container {
        padding: 1rem;
    }

    .group-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .standings-table {
        font-size: 0.75rem;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .group-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1025px) {
    .group-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>
@endpush

@section('content')
<div class="semafor-container">
    <!-- Header -->
    <div class="semafor-header sticky top-0 z-50 px-4 py-6 md:px-8 md:py-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        @if($competition->type === 'tournament')
                            <span class="text-2xl md:text-3xl">🏅</span>
                        @else
                            <span class="text-2xl md:text-3xl">🏆</span>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-4xl font-black text-white mb-1">
                            {{ $competition->name }}
                        </h1>
                        <p class="text-blue-400 text-sm md:text-lg font-medium">
                            {{ $competition->organization->name }} • {{ $competition->sport->name }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Phase Navigation -->
                    <div class="flex items-center space-x-2 bg-gray-800/50 rounded-lg p-1">
                        @if($competition->type === 'tournament')
                            <button onclick="showPhase('groups')"
                                    id="groups-tab"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all {{ $currentPhase === 'groups' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                Grupe
                            </button>
                            @if($matchesByPhase->has('knockout'))
                            <button onclick="showPhase('knockout')"
                                    id="knockout-tab"
                                    class="px-3 py-2 rounded-md text-sm font-medium transition-all {{ $currentPhase === 'knockout' ? 'bg-blue-600 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                                Eliminacija
                            </button>
                            @endif
                        @endif
                    </div>

                    <!-- Back Button -->
                    <a href="{{ route('public.leagues.show', $competition) }}"
                       class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-white font-medium transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="hidden md:inline">Nazad</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-6 md:px-8 md:py-8">
        <!-- Groups Phase -->
        @if($competition->type === 'tournament' && $competition->tournamentGroups->count() > 0)
        <div id="groups-content" class="phase-content {{ $currentPhase !== 'groups' ? 'hidden' : '' }}">
            <h2 class="text-xl md:text-2xl font-bold text-white mb-6 md:mb-8 text-center">
                Grupna faza
            </h2>

            <div class="group-grid grid gap-4 md:gap-6 lg:gap-8">
                @foreach($competition->tournamentGroups as $group)
                <div class="group-card rounded-xl p-4 md:p-6">
                    <h3 class="text-lg md:text-xl font-bold text-white mb-4 text-center bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                        Grupa {{ $group->name }}
                    </h3>

                    @if($group->standings && count($group->standings) > 0)
                    <div class="standings-table rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-800/50 border-b border-gray-600">
                                    <th class="text-left py-3 px-3 text-gray-300 font-semibold">#</th>
                                    <th class="text-left py-3 px-3 text-gray-300 font-semibold">Igrač</th>
                                    <th class="text-center py-3 px-2 text-gray-300 font-semibold">P</th>
                                    <th class="text-center py-3 px-2 text-gray-300 font-semibold">N</th>
                                    <th class="text-center py-3 px-2 text-gray-300 font-semibold">I</th>
                                    <th class="text-center py-3 px-2 text-gray-300 font-semibold">Setovi</th>
                                    <th class="text-center py-3 px-2 text-gray-300 font-semibold">Bodovi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(collect($group->standings)->sortBy('position') as $standing)
                                <tr class="standings-row border-b border-gray-700/30">
                                    <td class="py-3 px-3 font-bold text-gray-400 text-center">{{ $standing->position }}</td>
                                    <td class="py-3 px-3 text-white font-medium">
                                        {{ $standing->participant ? $standing->participant->name : 'N/A' }}
                                    </td>
                                    <td class="py-3 px-2 text-center text-green-400 font-bold">{{ $standing->won }}</td>
                                    <td class="py-3 px-2 text-center text-yellow-400 font-bold">{{ $standing->drawn }}</td>
                                    <td class="py-3 px-2 text-center text-red-400 font-bold">{{ $standing->lost }}</td>
                                    <td class="py-3 px-2 text-center text-cyan-400 font-bold">
                                        {{ $standing->sets_won - $standing->sets_lost }}
                                    </td>
                                    <td class="py-3 px-2 text-center text-blue-400 font-bold">{{ $standing->points }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-400">
                        <p>Nema rezultata za ovu grupu</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- League Standings -->
        @if($competition->type === 'league' && $competition->standings && count($competition->standings) > 0)
        <div id="league-content" class="phase-content">
            <h2 class="text-xl md:text-2xl font-bold text-white mb-6 md:mb-8 text-center">
                Tabela rezultata
            </h2>

            <div class="max-w-4xl mx-auto">
                <div class="standings-table rounded-xl overflow-hidden">
                    <table class="w-full text-sm md:text-base">
                        <thead>
                            <tr class="bg-gray-800/50 border-b border-gray-600">
                                <th class="text-left py-4 px-4 text-gray-300 font-semibold">#</th>
                                <th class="text-left py-4 px-4 text-gray-300 font-semibold">Tim/Igrač</th>
                                <th class="text-center py-4 px-3 text-gray-300 font-semibold">P</th>
                                <th class="text-center py-4 px-3 text-gray-300 font-semibold">N</th>
                                <th class="text-center py-4 px-3 text-gray-300 font-semibold">I</th>
                                <th class="text-center py-4 px-3 text-gray-300 font-semibold">Setovi</th>
                                <th class="text-center py-4 px-3 text-gray-300 font-semibold">Bodovi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($competition->standings)->sortBy('position') as $standing)
                            <tr class="standings-row border-b border-gray-700/30">
                                <td class="py-4 px-4 font-bold text-gray-400 text-center text-lg">{{ $standing->position }}</td>
                                <td class="py-4 px-4 text-white font-semibold text-lg">
                                    {{ $standing->participant ? $standing->participant->name : 'N/A' }}
                                </td>
                                <td class="py-4 px-3 text-center text-green-400 font-bold text-lg">{{ $standing->won }}</td>
                                <td class="py-4 px-3 text-center text-yellow-400 font-bold text-lg">{{ $standing->drawn }}</td>
                                <td class="py-4 px-3 text-center text-red-400 font-bold text-lg">{{ $standing->lost }}</td>
                                <td class="py-4 px-3 text-center text-cyan-400 font-bold text-lg">
                                    {{ $standing->sets_won - $standing->sets_lost }}
                                </td>
                                <td class="py-4 px-3 text-center text-blue-400 font-bold text-lg">{{ $standing->points }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Knockout Phase -->
        @if($matchesByPhase->has('knockout'))
        <div id="knockout-content" class="phase-content {{ $currentPhase !== 'knockout' ? 'hidden' : '' }}">
            <h2 class="text-xl md:text-2xl font-bold text-white mb-6 md:mb-8 text-center">
                Eliminaciona faza
            </h2>

            @php
                $totalRounds = $matchesByPhase['knockout']->max('round');
                $rounds = [];
                for ($round = 1; $round <= $totalRounds; $round++) {
                    $rounds[$round] = $matchesByPhase['knockout']->where('round', $round);
                }
            @endphp

            <div class="overflow-x-auto">
                <div class="min-w-max flex gap-8 md:gap-12 justify-center">
                    @for($round = 1; $round <= $totalRounds; $round++)
                    @php
                        $roundMatches = $rounds[$round] ?? collect();
                        $matchesInRound = $roundMatches->count();

                        if ($matchesInRound == 1 && $round === $totalRounds) {
                            $roundName = 'Finale';
                        } elseif ($matchesInRound == 1) {
                            $roundName = 'Polufinale';
                        } else {
                            $roundName = '1/' . $matchesInRound . ' Finala';
                        }
                    @endphp

                    <div class="flex flex-col">
                        <div class="text-center mb-6">
                            <h3 class="text-lg md:text-xl font-bold text-blue-400">{{ $roundName }}</h3>
                        </div>

                        <div class="flex flex-col gap-4">
                            @foreach($roundMatches as $match)
                            @php
                                $homeSetsWon = 0;
                                $awaySetsWon = 0;
                                $homeFinalScore = $match->home_score ?? 0;
                                $awayFinalScore = $match->away_score ?? 0;

                                if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
                                    foreach($match->sets as $set) {
                                        $homeSetScore = $set['home_score'] ?? $set['home'] ?? 0;
                                        $awaySetScore = $set['away_score'] ?? $set['away'] ?? 0;
                                        if($homeSetScore > $awaySetScore) {
                                            $homeSetsWon++;
                                        } elseif($awaySetScore > $homeSetScore) {
                                            $awaySetsWon++;
                                        }
                                    }
                                }

                                if($match->status === 'completed' && $homeSetsWon === 0 && $awaySetsWon === 0) {
                                    $homeSetsWon = $homeFinalScore;
                                    $awaySetsWon = $awayFinalScore;
                                }

                                $homePlayerName = $match->homePlayer->name ?? 'NEMA PROTIVNIKA';
                                $awayPlayerName = $match->awayPlayer->name ?? 'NEMA PROTIVNIKA';
                            @endphp

                            <div class="match-card rounded-lg p-4 min-w-[280px] {{ $match->status === 'in_progress' ? 'live-match' : '' }}">
                                @if($match->status === 'in_progress')
                                <div class="text-center mb-3">
                                    <span class="text-green-400 font-bold text-sm uppercase tracking-wider bg-green-900/30 px-2 py-1 rounded">
                                        Uživo
                                    </span>
                                </div>
                                @endif

                                <div class="space-y-3">
                                    <!-- Home Player -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                                                {{ $homeSetsWon }}
                                            </div>
                                            <span class="text-white font-semibold truncate {{ ($homeSetsWon > $awaySetsWon) && ($homeSetsWon > 0 || $awaySetsWon > 0) ? 'text-green-400' : '' }}">
                                                {{ $homePlayerName }}
                                            </span>
                                        </div>
                                        @if($match->status === 'in_progress' || $match->status === 'completed')
                                        <div class="flex-shrink-0 ml-3">
                                            <div class="w-8 h-8 bg-green-900/80 rounded flex items-center justify-center">
                                                <div class="text-sm font-bold text-green-300">
                                                    {{ $homeFinalScore ?: $homeSetsWon }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Away Player -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="w-8 h-8 rounded bg-white/20 flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                                                {{ $awaySetsWon }}
                                            </div>
                                            <span class="text-white font-semibold truncate {{ ($awaySetsWon > $homeSetsWon) && ($homeSetsWon > 0 || $awaySetsWon > 0) ? 'text-green-400' : '' }}">
                                                {{ $awayPlayerName }}
                                            </span>
                                        </div>
                                        @if($match->status === 'in_progress' || $match->status === 'completed')
                                        <div class="flex-shrink-0 ml-3">
                                            <div class="w-8 h-8 bg-green-900/80 rounded flex items-center justify-center">
                                                <div class="text-sm font-bold text-green-300">
                                                    {{ $awayFinalScore ?: $awaySetsWon }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
        @endif

        <!-- No Data Message -->
        @if((!$competition->standings || count($competition->standings) === 0) &&
            (!$competition->tournamentGroups || $competition->tournamentGroups->count() === 0) &&
            (!$matchesByPhase->has('knockout')))
        <div class="text-center py-16">
            <div class="text-6xl mb-6">🏓</div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-400 mb-4">Takmičenje još nije počelo</h2>
            <p class="text-gray-500 text-lg">Rezultati će biti prikazani kada takmičenje počne.</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function showPhase(phase) {
    // Hide all phase contents
    document.querySelectorAll('.phase-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active state from all tabs
    document.querySelectorAll('#groups-tab, #knockout-tab').forEach(tab => {
        tab.classList.remove('bg-blue-600', 'text-white');
        tab.classList.add('text-gray-300', 'hover:text-white', 'hover:bg-gray-700');
    });

    // Show selected phase content
    document.getElementById(phase + '-content').classList.remove('hidden');

    // Set active state for selected tab
    const activeTab = document.getElementById(phase + '-tab');
    if (activeTab) {
        activeTab.classList.remove('text-gray-300', 'hover:text-white', 'hover:bg-gray-700');
        activeTab.classList.add('bg-blue-600', 'text-white');
    }
}

// Auto-refresh functionality (optional)
let autoRefreshInterval;

function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        // Only refresh if page is visible
        if (!document.hidden) {
            location.reload();
        }
    }, 5000); // Refresh every 5 seconds
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// Start auto-refresh when page loads
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();

    // Stop auto-refresh when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoRefresh();
        } else {
            startAutoRefresh();
        }
    });
});
</script>
@endpush
@endsection