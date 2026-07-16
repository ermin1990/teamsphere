@extends('layouts.public')

@section('title', ($match->homeTeam?->name ?? $match->homePlayer?->name ?? 'Domaći') . ' vs ' . ($match->awayTeam?->name ?? $match->awayPlayer?->name ?? 'Gost') . ' - ' . $organization->name)

@push('scripts')
    <script>
        let lastUpdate = null;

        function updateMatchDetails() {
            fetch('{{ route("api.match", $match) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const matchData = data.data;
                        
                        // Update home score
                        const homeScoreElement = document.getElementById('home-score');
                        if (homeScoreElement) {
                            if (matchData.status === 'completed' && matchData.sets && matchData.sets.length > 0) {
                                // Show final sets won for completed matches
                                const homeSetsWon = matchData.sets.filter(set => 
                                    (set.home_score ?? set.home ?? 0) > (set.away_score ?? set.away ?? 0)
                                ).length;
                                homeScoreElement.textContent = homeSetsWon;
                            } else {
                                homeScoreElement.textContent = matchData.home_score ?? 0;
                            }
                        }
                        
                        // Update away score
                        const awayScoreElement = document.getElementById('away-score');
                        if (awayScoreElement) {
                            if (matchData.status === 'completed' && matchData.sets && matchData.sets.length > 0) {
                                // Show final sets won for completed matches
                                const awaySetsWon = matchData.sets.filter(set => 
                                    (set.away_score ?? set.away ?? 0) > (set.home_score ?? set.home ?? 0)
                                ).length;
                                awayScoreElement.textContent = awaySetsWon;
                            } else {
                                awayScoreElement.textContent = matchData.away_score ?? 0;
                            }
                        }
                        
                        // Update set history table
                        if (matchData.sets && matchData.sets.length > 0) {
                            updateSetHistoryTable(matchData.sets);
                        }
                        
                        // Update match status
                        const matchStatusElement = document.getElementById('match-status');
                        if (matchStatusElement) {
                            if (matchData.status === 'in_progress') {
                                matchStatusElement.innerHTML = '<div class="text-green-400 font-semibold text-sm md:text-base">🔴 UŽIVO</div>';
                            } else if (matchData.status === 'completed') {
                                matchStatusElement.innerHTML = '<div class="text-green-400 font-semibold text-sm md:text-base">✅ ZAVRŠENO</div>';
                            } else {
                                matchStatusElement.innerHTML = '';
                            }
                        }
                        
                        // Update last updated time if element exists
                        const lastUpdatedElement = document.getElementById('last-updated-time');
                        if (lastUpdatedElement && data.last_updated) {
                            const date = new Date(data.last_updated);
                            lastUpdatedElement.textContent = 'Ažurirano: ' + date.toLocaleTimeString();
                        }
                        
                        lastUpdate = data.last_updated;
                    }
                })
                .catch(error => {
                    // Silent error handling
                });
        }

        function updateSetHistoryTable(sets) {
            const tbody = document.querySelector('#sets-breakdown tbody');
            if (!tbody) {
                return;
            }

            // Clear existing rows
            tbody.innerHTML = '';

            // Add new rows for each set
            sets.forEach((set, index) => {
                const homeScore = set.home_score ?? set.home ?? 0;
                const awayScore = set.away_score ?? set.away ?? 0;
                
                const tr = document.createElement('tr');
                tr.className = 'border-b border-[var(--border-secondary)]';
                tr.innerHTML = `
                    <td class="py-2 md:py-3 text-[var(--text-secondary)] font-medium text-xs md:text-sm">${index + 1}</td>
                    <td class="py-2 md:py-3 text-blue-400 font-bold text-sm md:text-lg">${homeScore}</td>
                    <td class="py-2 md:py-3 text-[var(--text-muted)] text-xs md:text-sm">-</td>
                    <td class="py-2 md:py-3 text-red-400 font-bold text-sm md:text-lg">${awayScore}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Update every 5 seconds when page is visible
        let updateInterval;

        function startUpdates() {
            if (updateInterval) clearInterval(updateInterval);
            updateInterval = setInterval(updateMatchDetails, 5000);
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

        // Initial update
        document.addEventListener('DOMContentLoaded', function() {
            updateMatchDetails();
            if (!document.hidden) {
                startUpdates();
            }
        });
    </script>
@endpush

@section('content')
            <!-- Header -->
            <div class="bg-[var(--bg-card)] backdrop-blur-xl rounded-xl p-4 border border-[var(--border-primary)] shadow-xl mb-6">
                <div class="text-center">
                    <div class="text-xs text-[var(--text-secondary)] mb-2">{{ $competition->sport->name }} • {{ $competition->name }}</div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-1">
                        Detalji Meča
                    </h1>
                    <p class="text-sm text-[var(--text-secondary)] mb-3">Kolo {{ $match->round_number ?? $match->round }}</p>
                    
                    <!-- Match Info -->
                    <div class="flex flex-wrap justify-center gap-2">
                        @if($match->table)
                        <div class="flex items-center gap-1.5 text-xs text-[var(--text-primary)] bg-[var(--bg-tertiary)] px-3 py-1.5 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Sto: {{ $match->table->name }}</span>
                        </div>
                        @endif
                        
                        @if($match->referee)
                        <div class="flex items-center gap-1.5 text-xs text-[var(--text-primary)] bg-[var(--bg-tertiary)] px-3 py-1.5 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Sudija: {{ $match->referee->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Live Score Component -->
            @livewire('public-live-score', ['match' => $match])

            <!-- Back to League -->
            <div class="text-center mt-8 mb-20 md:mb-8">
                <a href="{{ route('competitions.show', $competition) }}"
                   class="inline-flex items-center px-6 py-3 bg-[var(--bg-tertiary)] hover:bg-[var(--bg-hover)] text-[var(--text-primary)] rounded-lg transition-colors">
                    ← Nazad na takmičenje
                </a>
            </div>
@endsection
