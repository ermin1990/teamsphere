<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Projektor - TeamSphere</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --bg-card: rgba(31, 41, 55, 0.5);
            --text-primary: #ffffff;
            --text-secondary: #d1d5db;
            --text-tertiary: #9ca3af;
            --border-primary: rgba(55, 65, 81, 0.5);
            --accent-blue: #60a5fa;
            --accent-green: #10b981;
            --accent-red: #ef4444;
        }

        body {
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            overflow: hidden;
        }

        .competition-view {
            opacity: 0;
            transition: opacity {{ $transitionSpeed }}ms ease-in-out;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
        }

        .competition-view.active {
            opacity: 1;
            position: relative;
        }

        .progress-bar {
            height: 4px;
            background: linear-gradient(90deg, var(--accent-blue) 0%, var(--accent-green) 100%);
            transition: width linear;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .live-indicator {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-blue);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3b82f6;
        }
    </style>
</head>
<body>
    <!-- Competition Info Header -->
    <div class="fixed bottom-4 left-0 right-0 z-40 flex justify-between items-center px-8">
        <div><!-- Spacer to keep layout if needed, or just empty --></div>

        <div class="flex items-center gap-4">
            <!-- Live Indicator -->
            <div id="liveIndicator" class="hidden bg-red-500/20 backdrop-blur-lg rounded-full px-4 py-2 border border-red-500/50">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full live-indicator"></div>
                    <span class="text-red-400 font-semibold text-sm">UŽIVO</span>
                </div>
            </div>

            <!-- Competition Counter -->
            <div class="bg-gray-900/80 backdrop-blur-lg rounded-full px-6 py-3 shadow-2xl border border-gray-700/50">
                <p class="text-white font-bold">
                    <span id="currentIndex">1</span> / <span id="totalCount">{{ $totalCompetitions }}</span>
                </p>
            </div>

            <!-- Time Remaining -->
            <div class="bg-gray-900/80 backdrop-blur-lg rounded-full px-6 py-3 shadow-2xl border border-gray-700/50">
                <p class="text-white font-bold">
                    ⏱️ <span id="timeRemaining">--</span>s
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div id="projectorContainer" class="pt-8 pb-8 px-8 h-screen overflow-y-auto">
        @foreach($rotationConfig as $index => $config)
            <div 
                class="competition-view {{ $index === 0 ? 'active' : '' }}" 
                id="competition-{{ $config['id'] }}"
                data-competition-id="{{ $config['id'] }}"
                data-phase="{{ $config['phase'] ?? 'auto' }}"
                data-duration="{{ $config['duration'] }}"
                data-has-live="{{ $config['has_live'] ? 'true' : 'false' }}"
            >
                @include('projector.partials.competition-view', [
                    'competition' => $config['competition'],
                    'selectedGroup' => $config['group'] ?? null,
                    'mode' => $mode,
                    'layout' => $layout,
                    'phase' => $config['phase'] ?? 'auto'
                ])
            </div>
        @endforeach
    </div>

    <!-- Rotation Data for JavaScript -->
    <script>
        const rotationData = @json($rotationDataForJs);

        const config = {
            transitionSpeed: {{ $transitionSpeed }},
            livePriority: {{ $livePriority ? 'true' : 'false' }},
            mode: '{{ $mode }}',
            layout: '{{ $layout }}'
        };

        let currentIndex = 0;
        let timeRemaining = 0;
        let interval = null;
        let progressInterval = null;

        function updateHeader(competitionData) {
            document.getElementById('currentIndex').textContent = currentIndex + 1;
            
            const liveIndicator = document.getElementById('liveIndicator');
            if (competitionData.has_live) {
                liveIndicator.classList.remove('hidden');
            } else {
                liveIndicator.classList.add('hidden');
            }
        }

        function showCompetition(index) {
            const views = document.querySelectorAll('.competition-view');
            
            // Hide all views
            views.forEach(view => {
                view.classList.remove('active');
            });
            
            // Show current view after transition
            setTimeout(() => {
                views[index].classList.add('active');
            }, config.transitionSpeed / 2);

            // Update header
            updateHeader(rotationData[index]);

            // Start timer
            const duration = rotationData[index].duration;
            timeRemaining = duration;
            document.getElementById('timeRemaining').textContent = duration;

            // Update timer every second
            if (interval) clearInterval(interval);
            interval = setInterval(() => {
                timeRemaining--;
                document.getElementById('timeRemaining').textContent = timeRemaining;

                if (timeRemaining <= 0) {
                    clearInterval(interval);
                    nextCompetition();
                }
            }, 1000);
        }

        function nextCompetition() {
            currentIndex = (currentIndex + 1) % rotationData.length;
            showCompetition(currentIndex);
        }

        function previousCompetition() {
            currentIndex = (currentIndex - 1 + rotationData.length) % rotationData.length;
            showCompetition(currentIndex);
        }

        // Keyboard controls
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight' || e.key === 'n') {
                clearInterval(interval);
                nextCompetition();
            } else if (e.key === 'ArrowLeft' || e.key === 'p') {
                clearInterval(interval);
                previousCompetition();
            } else if (e.key === ' ') {
                e.preventDefault();
                if (interval) {
                    clearInterval(interval);
                    interval = null;
                } else {
                    showCompetition(currentIndex);
                }
            } else if (e.key === 'f') {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else {
                    document.exitFullscreen();
                }
            }
        });

        // Initialize first competition
        if (rotationData.length > 0) {
            showCompetition(0);
        }

        // Auto-refresh every 30 seconds to get latest match data
        setInterval(() => {
            // Refresh current view
            const currentView = document.querySelector('.competition-view.active');
            if (currentView) {
                const competitionId = currentView.dataset.competitionId;
                const phase = currentView.dataset.phase || 'auto';
                fetch(`/projector/competition/${competitionId}?mode=${config.mode}&layout=${config.layout}&phase=${phase}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.querySelector('body').innerHTML;
                        currentView.innerHTML = newContent;
                    })
                    .catch(err => console.error('Failed to refresh:', err));
            }
        }, 30000); // 30 seconds
    </script>

    <!-- Footer Controls (Hidden, shown on hover) -->
    <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 opacity-0 hover:opacity-100 transition-opacity duration-300 z-50">
        <div class="bg-gray-900/90 backdrop-blur-lg rounded-full px-6 py-3 shadow-2xl border border-gray-700/50 flex items-center gap-4">
            <button onclick="previousCompetition()" class="text-white hover:text-blue-400 transition-colors">
                ⬅️ Prethodno
            </button>
            <div class="w-px h-6 bg-gray-700"></div>
            <button onclick="nextCompetition()" class="text-white hover:text-blue-400 transition-colors">
                Sljedeće ➡️
            </button>
            <div class="w-px h-6 bg-gray-700"></div>
            <button onclick="document.documentElement.requestFullscreen()" class="text-white hover:text-blue-400 transition-colors">
                🖥️ Full Screen (F)
            </button>
        </div>
    </div>
</body>
</html>
