@extends('layouts.public')

@section('title', $match->homeTeam?->name ?? $match->homePlayer?->name ?? 'Home' . ' vs ' . $match->awayTeam?->name ?? $match->awayPlayer?->name ?? 'Away' . ' - ' . $organization->name)

@push('scripts')
    <script>
        let lastUpdate = null;

        function updateMatchDetails() {
            fetch('{{ route("public.api.match", $match) }}')
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
                                matchStatusElement.innerHTML = '<div class="text-green-400 font-semibold text-sm md:text-base">🔴 LIVE</div>';
                            } else if (matchData.status === 'completed') {
                                matchStatusElement.innerHTML = '<div class="text-green-400 font-semibold text-sm md:text-base">✅ COMPLETED</div>';
                            } else {
                                matchStatusElement.innerHTML = '';
                            }
                        }
                        
                        // Update last updated time if element exists
                        const lastUpdatedElement = document.getElementById('last-updated-time');
                        if (lastUpdatedElement && data.last_updated) {
                            const date = new Date(data.last_updated);
                            lastUpdatedElement.textContent = 'Last updated: ' + date.toLocaleTimeString();
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
                tr.className = 'border-b border-gray-700/50';
                tr.innerHTML = `
                    <td class="py-2 md:py-3 text-gray-300 font-medium text-xs md:text-sm">${index + 1}</td>
                    <td class="py-2 md:py-3 text-blue-400 font-bold text-sm md:text-lg">${homeScore}</td>
                    <td class="py-2 md:py-3 text-gray-400 text-xs md:text-sm">-</td>
                    <td class="py-2 md:py-3 text-red-400 font-bold text-sm md:text-lg">${awayScore}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Update every 3 seconds
        setInterval(updateMatchDetails, 3000);

        // Initial update
        document.addEventListener('DOMContentLoaded', function() {
            updateMatchDetails();
        });
    </script>
@endsection

@section('content')
            <!-- Header -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50 shadow-xl mb-6">
                <div class="text-center">
                    <div class="text-xs text-gray-400 mb-2">{{ $competition->sport->name }} • {{ $competition->name }}</div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-1">
                        Detalji Meča
                    </h1>
                    <p class="text-sm text-gray-400 mb-3">Kolo {{ $match->round_number ?? $match->round }}</p>
                    
                    <!-- Match Info -->
                    <div class="flex flex-wrap justify-center gap-2">
                        @if($match->table)
                        <div class="flex items-center gap-1.5 text-xs text-gray-300 bg-gray-700/30 px-3 py-1.5 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Sto: {{ $match->table->name }}</span>
                        </div>
                        @endif
                        
                        @if($match->referee)
                        <div class="flex items-center gap-1.5 text-xs text-gray-300 bg-gray-700/30 px-3 py-1.5 rounded-lg">
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
                <a href="{{ route('public.leagues.show', $competition) }}"
                   class="inline-flex items-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    ← Back to League
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu (Fixed Bottom) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-gray-800/95 backdrop-blur-xl border-t border-gray-700/50 shadow-2xl z-50">
        <div class="flex items-center justify-between py-3 px-4 w-full">
            <a href="{{ route('home') }}" class="flex flex-col items-center text-gray-300 hover:text-white transition-colors text-xs flex-1">
                <span class="text-lg">🏠</span>
                <span class="mt-1">Home</span>
            </a>
            <a href="{{ route('public.live-matches') }}" class="flex flex-col items-center text-gray-300 hover:text-white transition-colors text-xs flex-1">
                <span class="text-lg">📺</span>
                <span class="mt-1">Live</span>
            </a>
            <a href="{{ route('public.leagues.index') }}" class="flex flex-col items-center text-gray-300 hover:text-white transition-colors text-xs flex-1">
                <span class="text-lg">🏆</span>
                <span class="mt-1">Takmičenja</span>
            </a>
        </div>
    </nav>
@endsection
