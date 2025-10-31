@extends('layouts.public')

@section('title', 'Live: ' . $match->homeTeam?->name ?? $match->homePlayer?->name ?? 'Home' . ' vs ' . $match->awayTeam?->name ?? $match->awayPlayer?->name ?? 'Away')

@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let lastUpdated = null;
        
        function updateMatchData() {
            fetch('{{ route("public.api.match", $match) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateMatchDisplay(data.data);
                        lastUpdated = data.last_updated;
                        
                        // Update last updated time
                        const lastUpdatedElement = document.getElementById('last-updated-time');
                        if (lastUpdatedElement) {
                            const time = new Date(data.last_updated);
                            lastUpdatedElement.textContent = 'Last updated: ' + time.toLocaleTimeString();
                        }
                    }
                })
                .catch(error => {
                    // Silent error handling
                });
        }
        
        function updateMatchDisplay(matchData) {
            // Update home score
            const homeScoreElement = document.getElementById('home-score');
            if (homeScoreElement) {
                homeScoreElement.textContent = matchData.home_score;
            }
            
            // Update away score
            const awayScoreElement = document.getElementById('away-score');
            if (awayScoreElement) {
                awayScoreElement.textContent = matchData.away_score;
            }
            
            // Update sets if they exist
            if (matchData.sets && matchData.sets.length > 0) {
                updateSetsDisplay(matchData.sets);
            }
            
            // Update status
            const statusElement = document.getElementById('match-status');
            if (statusElement) {
                if (matchData.status === 'in_progress') {
                    statusElement.innerHTML = '<div class="text-green-400 font-semibold text-sm md:text-base">🔴 LIVE</div>';
                } else if (matchData.status === 'completed') {
                    statusElement.innerHTML = '<div class="text-green-400 font-semibold text-sm md:text-base">✅ COMPLETED</div>';
                } else {
                    statusElement.innerHTML = '';
                }
            }
        }
        
        function updateSetsDisplay(sets) {
            // Update sets breakdown for individual matches
            const setsContainer = document.getElementById('sets-breakdown');
            if (setsContainer && sets.length > 0) {
                let html = '<h3 class="text-lg md:text-xl font-bold text-center mb-4 bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">Set Scores</h3>';
                html += '<div class="overflow-x-auto"><table class="w-full text-center text-sm md:text-base">';
                html += '<thead><tr class="border-b border-[var(--border-primary)]">';
                html += '<th class="pb-2 md:pb-3 text-[var(--text-secondary)] font-medium text-xs md:text-sm">Set</th>';
                html += '<th class="pb-2 md:pb-3 text-blue-400 font-medium text-xs md:text-sm">{{ $match->homePlayer->name ?? "Home" }}</th>';
                html += '<th class="pb-2 md:pb-3 text-[var(--text-muted)] font-medium text-xs md:text-sm">-</th>';
                html += '<th class="pb-2 md:pb-3 text-red-400 font-medium text-xs md:text-sm">{{ $match->awayPlayer->name ?? "Away" }}</th>';
                html += '</tr></thead><tbody>';
                
                sets.forEach((set, index) => {
                    html += '<tr class="border-b border-[var(--border-secondary)]">';
                    html += '<td class="py-2 md:py-3 text-[var(--text-secondary)] font-medium text-xs md:text-sm">' + (index + 1) + '</td>';
                    html += '<td class="py-2 md:py-3 text-blue-400 font-bold text-sm md:text-lg">' + (set.home_score ?? set.home ?? 0) + '</td>';
                    html += '<td class="py-2 md:py-3 text-[var(--text-muted)] text-xs md:text-sm">-</td>';
                    html += '<td class="py-2 md:py-3 text-red-400 font-bold text-sm md:text-lg">' + (set.away_score ?? set.away ?? 0) + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                setsContainer.innerHTML = html;
            }
        }
        
        // Update every 3 seconds
        setInterval(updateMatchData, 3000);

        // Initial update after a short delay
        setTimeout(updateMatchData, 1000);
    });
    </script>
@endsection

@section('content')
            <!-- Header -->
            <div class="bg-[var(--bg-card)] backdrop-blur-xl rounded-2xl p-4 border border-[var(--border-primary)] shadow-xl mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                            🏓 Live Table Tennis Score
                        </h1>
                        <p class="text-[var(--text-secondary)] text-sm md:text-base mt-1">{{ $competition->name }} • Round {{ $match->round_number ?? $match->round }}</p>
                    </div>
                    <div class="flex items-center space-x-2 md:space-x-4">
                        <span class="px-3 py-1 md:px-4 md:py-2 bg-[var(--bg-button)] hover:bg-[var(--bg-button-hover)] text-[var(--text-primary)] rounded-lg transition-colors text-sm">

            <!-- Live Score Display -->
            @livewire('public-live-score', ['match' => $match])

            <!-- Match Info -->
            <div class="mt-6 flex flex-col items-center gap-3 mb-8">
                @if($match->table)
                <div class="flex items-center gap-2 text-sm text-[var(--text-primary)] bg-[var(--bg-tertiary)] px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span>Sto: {{ $match->table->name }}</span>
                </div>
                @endif
                
                @if($match->referee)
                <div class="flex items-center gap-2 text-sm text-[var(--text-primary)] bg-[var(--bg-tertiary)] px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Sudija: {{ $match->referee->name }}</span>
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-[var(--text-secondary)] text-sm">
                <p>Powered by TeamSphere • {{ $organization->name }}</p>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu (Fixed Bottom) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-[var(--bg-nav)] backdrop-blur-xl border-t border-[var(--border-primary)] shadow-2xl z-50">
        <div class="flex items-center justify-between py-3 px-4 w-full">
            <a href="{{ route('home') }}" class="flex flex-col items-center text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors text-xs flex-1">
                <span class="text-lg">🏠</span>
                <span class="mt-1">Home</span>
            </a>
            <a href="{{ route('public.live-matches') }}" class="flex flex-col items-center text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors text-xs flex-1">
                <span class="text-lg">📺</span>
                <span class="mt-1">Live</span>
            </a>
            <a href="{{ route('public.leagues.index') }}" class="flex flex-col items-center text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors text-xs flex-1">
                <span class="text-lg">🏆</span>
                <span class="mt-1">Competitions</span>
            </a>
        </div>
    </nav>
@endsection
