<!DOCTYPE html>
<html lang="bs" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Odaberi Turnire - MojTurnir Live</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .league-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .league-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .league-card:hover::before {
            left: 100%;
        }
        
        .league-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 60px rgba(59, 130, 246, 0.4);
        }
        
        .league-card.selected {
            border-color: #3b82f6;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(147, 51, 234, 0.2) 100%);
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.5);
        }
        
        .league-card.selected .check-icon {
            opacity: 1;
            transform: scale(1) rotate(0deg);
        }
        
        .check-icon {
            opacity: 0;
            transform: scale(0) rotate(-180deg);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .pulse-ring {
            animation: pulseRing 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulseRing {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.5;
                transform: scale(1.1);
            }
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .glow-button {
            position: relative;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            transition: all 0.3s;
        }
        
        .glow-button:hover {
            box-shadow: 0 0 40px rgba(59, 130, 246, 0.8), 0 0 60px rgba(139, 92, 246, 0.6);
            transform: scale(1.05);
        }
        
        .glow-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: scale(1);
        }
        
        .shimmer {
            background: linear-gradient(90deg, 
                rgba(255,255,255,0) 0%, 
                rgba(255,255,255,0.3) 50%, 
                rgba(255,255,255,0) 100%);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
    </style>
</head>
<body class="h-full">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-8">
        <!-- Header -->
        <div class="text-center mb-8 floating">
            <div class="inline-flex items-center justify-center mb-4">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full blur-xl opacity-75 pulse-ring"></div>
                    <div class="relative w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-2xl">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-3 bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                LIVE TURNIRI
            </h1>
            <p class="text-lg text-gray-300 mb-2">Odaberite turnire za praćenje uživo</p>
            <div class="flex items-center justify-center space-x-2">
                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                <p class="text-sm text-gray-400">{{ $competitions->count() }} {{ $competitions->count() == 1 ? 'Turnir' : ($competitions->count() < 5 ? 'Turnira' : 'Turnira') }}</p>
            </div>
        </div>

        <!-- Counter -->
        <div class="mb-6">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl px-6 py-3 border border-gray-700/50 shadow-xl inline-block">
                <p class="text-center text-gray-400 text-xs mb-0.5">Odabrano</p>
                <p class="text-center text-3xl font-bold text-white" id="selected-count">0</p>
            </div>
        </div>

        <!-- Leagues Grid -->
        @if($competitions->count() > 0)
            <div class="w-full max-w-6xl mb-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="leagues-container">
                    @foreach($competitions as $competition)
                        <div class="league-card bg-gray-800/70 backdrop-blur-xl rounded-xl p-5 border-2 border-gray-700/50 cursor-pointer shadow-xl"
                             data-league-id="{{ $competition->id }}"
                             onclick="toggleLeague(this)">
                            
                            <!-- Check Icon -->
                            <div class="check-icon absolute top-3 right-3 w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            
                            <!-- Sport Icon -->
                            <div class="text-center mb-3">
                                <div class="text-5xl mb-2 filter drop-shadow-lg">{{ $competition->sport->icon }}</div>
                                <h3 class="text-white font-bold text-base mb-1 truncate">{{ $competition->name }}</h3>
                                <p class="text-gray-400 text-xs truncate">{{ $competition->organization->name }}</p>
                            </div>
                            
                            <!-- Stats -->
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-700/50">
                                @if($competition->live_matches_count > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-500 text-white animate-pulse">
                                        <span class="w-1 h-1 bg-white rounded-full mr-1 animate-ping"></span>
                                        LIVE
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-gray-600 text-white">
                                        ČEKA SE
                                    </span>
                                @endif
                                <div class="text-right">
                                    <p class="text-xl font-bold text-blue-400">{{ $competition->live_matches_count }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $competition->live_matches_count == 1 ? 'meč' : 'meča' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                <button onclick="selectAll()" class="w-full sm:w-auto px-6 py-3 bg-gray-700/50 hover:bg-gray-600/50 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 backdrop-blur-sm border border-gray-600/50 text-sm">
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Sve</span>
                    </span>
                </button>
                
                <button onclick="clearAll()" class="w-full sm:w-auto px-6 py-3 bg-gray-700/50 hover:bg-gray-600/50 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 backdrop-blur-sm border border-gray-600/50 text-sm">
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Očisti</span>
                    </span>
                </button>
                
                <button id="watch-btn" onclick="watchSelected()" disabled class="w-full sm:w-auto px-10 py-4 glow-button text-white font-black text-base rounded-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shimmer">
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>GLEDAJ UŽIVO</span>
                    </span>
                </button>
            </div>
        @else
            <!-- No Leagues Available -->
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-white mb-3">Nema Live Turnira</h2>
                <p class="text-gray-400 text-base mb-6">Trenutno nema aktivnih turnira sa live mečevima.</p>
                <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-700 text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    Početna Stranica
                </a>
            </div>
        @endif

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-gray-500 text-xs">
                <a href="{{ route('home') }}" class="text-blue-400 hover:text-blue-300 transition-colors">← Početna</a>
            </p>
        </div>
    </div>

    <script>
        let selectedLeagues = new Set();

        function toggleLeague(card) {
            const leagueId = card.dataset.leagueId;
            
            if (selectedLeagues.has(leagueId)) {
                selectedLeagues.delete(leagueId);
                card.classList.remove('selected');
            } else {
                selectedLeagues.add(leagueId);
                card.classList.add('selected');
            }
            
            updateCounter();
        }

        function selectAll() {
            const cards = document.querySelectorAll('.league-card');
            cards.forEach(card => {
                const leagueId = card.dataset.leagueId;
                selectedLeagues.add(leagueId);
                card.classList.add('selected');
            });
            updateCounter();
        }

        function clearAll() {
            const cards = document.querySelectorAll('.league-card');
            cards.forEach(card => {
                card.classList.remove('selected');
            });
            selectedLeagues.clear();
            updateCounter();
        }

        function updateCounter() {
            const count = selectedLeagues.size;
            document.getElementById('selected-count').textContent = count;
            
            const watchBtn = document.getElementById('watch-btn');
            if (count > 0) {
                watchBtn.disabled = false;
            } else {
                watchBtn.disabled = true;
            }
        }

        function watchSelected() {
            if (selectedLeagues.size === 0) {
                alert('Molimo odaberite barem jedan turnir!');
                return;
            }
            
            const leagueIds = Array.from(selectedLeagues).join(',');
            window.location.href = `/display?competitions=${leagueIds}`;
        }

        // Add keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && selectedLeagues.size > 0) {
                watchSelected();
            } else if (e.key === 'Escape') {
                clearAll();
            } else if (e.ctrlKey && e.key === 'a') {
                e.preventDefault();
                selectAll();
            }
        });
    </script>
</body>
</html>
