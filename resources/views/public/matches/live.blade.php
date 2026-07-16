@extends('layouts.public')

@section('title', 'Uživo: ' . ($match->homeTeam?->name ?? $match->homePlayer?->name ?? 'Domaći') . ' vs ' . ($match->awayTeam?->name ?? $match->awayPlayer?->name ?? 'Gost'))

@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let lastUpdated = null;
        
        function updateMatchData() {
            fetch('{{ route("api.match", $match) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateMatchDisplay(data.data);
                        lastUpdated = data.last_updated;
                        
                        // Update last updated time
                        const lastUpdatedElement = document.getElementById('last-updated-time');
                        if (lastUpdatedElement) {
                            const time = new Date(data.last_updated);
                            lastUpdatedElement.textContent = 'Ažurirano: ' + time.toLocaleTimeString();
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
                    statusElement.innerHTML = '<div class="text-green-400 font-semibold text-sm md:text-base">🔴 UŽIVO</div>';
                } else if (matchData.status === 'completed') {
                    statusElement.innerHTML = '<div class="text-green-400 font-semibold text-sm md:text-base">✅ ZAVRŠENO</div>';
                } else {
                    statusElement.innerHTML = '';
                }
            }
        }
        
        function updateSetsDisplay(sets) {
            // Update sets breakdown for individual matches
            const setsContainer = document.getElementById('sets-breakdown');
            if (setsContainer && sets.length > 0) {
                let html = '<h3 class="text-lg md:text-xl font-bold text-center mb-4 bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">Rezultati po setovima</h3>';
                html += '<div class="overflow-x-auto"><table class="w-full text-center text-sm md:text-base">';
                html += '<thead><tr class="border-b border-[var(--border-primary)]">';
                html += '<th class="pb-2 md:pb-3 text-[var(--text-secondary)] font-medium text-xs md:text-sm">Set</th>';
                html += '<th class="pb-2 md:pb-3 text-blue-400 font-medium text-xs md:text-sm">{{ $match->homePlayer->name ?? "Domaći" }}</th>';
                html += '<th class="pb-2 md:pb-3 text-[var(--text-muted)] font-medium text-xs md:text-sm">-</th>';
                html += '<th class="pb-2 md:pb-3 text-red-400 font-medium text-xs md:text-sm">{{ $match->awayPlayer->name ?? "Gost" }}</th>';
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
        
        // Update every 5 seconds when page is visible
        let updateInterval;

        function startUpdates() {
            if (updateInterval) clearInterval(updateInterval);
            updateInterval = setInterval(updateMatchData, 5000);
        }

        function stopUpdates() {
            if (updateInterval) {
                clearInterval(updateInterval);
                updateInterval = null;
            }
        }

        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopUpdates();
            } else {
                startUpdates();
            }
        });

        // Initial update after a short delay
        setTimeout(function() {
            updateMatchData();
            if (!document.hidden) {
                startUpdates();
            }
        }, 1000);
    });
    </script>
@endpush

@section('content')
            <!-- Header -->
            <div class="bg-[var(--bg-card)] backdrop-blur-xl rounded-2xl p-4 border border-[var(--border-primary)] shadow-xl mb-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                        🏓 Uživo rezultat
                    </h1>
                    <p class="text-[var(--text-secondary)] text-sm md:text-base mt-1">{{ $competition->name }} • Kolo {{ $match->round_number ?? $match->round }}</p>
                </div>
            </div>

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
                <p>Powered by MojTurnir • {{ $organization->name }}</p>
            </div>
@endsection
