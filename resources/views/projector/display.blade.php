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
            --player-font-size: 14px;
            --title-font-size: 24px;
            --zoom-scale: 1;
        }

        body {
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            overflow: hidden;
            @if($resolution === '1024x768')
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            @endif
        }

        .player-name {
            font-size: var(--player-font-size) !important;
        }

        .group-title,
        .competition-header h2 {
            font-size: var(--title-font-size) !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .projector-content {
            transform: scale(var(--zoom-scale));
            transform-origin: top center;
            transition: transform 300ms ease;
        }

        .projector-wrapper {
            @if($resolution === '1024x768')
            width: 1024px;
            height: 768px;
            position: relative;
            background: var(--bg-primary);
            box-shadow: 0 0 100px rgba(0,0,0,0.8);
            overflow: hidden;
            border: 1px solid var(--border-primary);
            @else
            width: 100%;
            height: 100vh;
            @endif
        }

        .projector-wrapper.res-1024 {
            font-size: 0.875rem;
        }

        .res-1024 h1 { font-size: 1.5rem !important; }
        .res-1024 h2 { font-size: 1.25rem !important; }
        .res-1024 h3 { font-size: 1.125rem !important; }
        .res-1024 h4 { font-size: 1rem !important; }
        .res-1024 .p-6 { padding: 1rem !important; }
        .res-1024 .p-8 { padding: 1.5rem !important; }
        .res-1024 .gap-8 { gap: 1rem !important; }
        .res-1024 .gap-6 { gap: 0.75rem !important; }
        .res-1024 .gap-4 { gap: 0.5rem !important; }
        .res-1024 .mt-8 { margin-top: 1rem !important; }
        .res-1024 .mb-8 { margin-bottom: 1rem !important; }
        .res-1024 .mb-6 { margin-bottom: 0.75rem !important; }
        .res-1024 .mb-3 { margin-bottom: 0.5rem !important; }
        .res-1024 .text-3xl { font-size: 1.25rem !important; }
        .res-1024 .text-4xl { font-size: 1.5rem !important; }
        .res-1024 .text-5xl { font-size: 1.75rem !important; }
        .res-1024 .text-base { font-size: 0.875rem !important; }
        .res-1024 .text-lg { font-size: 1rem !important; }
        .res-1024 .text-xl { font-size: 1.125rem !important; }
        .res-1024 .text-2xl { font-size: 1.25rem !important; }
        
        /* Responsive group title for 1024x768 */
        .res-1024 .group-title {
            font-size: calc(var(--title-font-size) * 0.75) !important;
            max-width: 100%;
        }
        
        .res-1024 .group-title span {
            font-size: 1.5rem !important;
        }

        /* Responsive table spacing for 1024x768 */
        .res-1024 table th,
        .res-1024 table td {
            padding-top: 0.375rem !important;
            padding-bottom: 0.375rem !important;
        }

        .res-1024 .space-y-8 > * + * {
            margin-top: 1rem !important;
        }

        /* Ensure content fits within viewport */
        .tournament-standings-container {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
        }

        .competition-view {
            display: none;
            opacity: 1;
            pointer-events: auto;
            transition: none;
        }

        .competition-view.active {
            display: block;
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
    <div class="projector-wrapper {{ $resolution === '1024x768' ? 'res-1024' : '' }}" id="zoomWrapper">

    <!-- Competition Info Header (Live Indicator) -->
    <div class="fixed top-4 left-4 z-40">
        <div class="flex items-center gap-4">
            <!-- Live Indicator -->
            <div id="liveIndicator" class="hidden bg-red-500/20 backdrop-blur-lg rounded-full px-4 py-2 border border-red-500/50">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full live-indicator"></div>
                    <span class="text-red-400 font-semibold text-sm">UŽIVO</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div id="projectorContainer" class="pt-6 pb-32 px-4 h-full overflow-y-auto custom-scrollbar">
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
                    'resolution' => $resolution,
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
        let zoomLevel = 1.0;
        let columnWidth = 0; // Offset from default
        let playerFontSize = 14; // Base font size in px
        let titleFontSize = 24; // Base title font size in px

        window.changeZoom = function(delta) {
            zoomLevel = Math.max(0.2, Math.min(2.0, zoomLevel + delta));
            applyZoom();
            saveSettings();
        }

        window.resetZoom = function() {
            zoomLevel = 1.0;
            applyZoom();
            saveSettings();
        }

        window.changeWidth = function(delta) {
            columnWidth += delta;
            applyColumnWidth();
            saveSettings();
        }

        window.resetWidth = function() {
            columnWidth = 0;
            applyColumnWidth();
            saveSettings();
        }

        window.changePlayerFont = function(delta) {
            playerFontSize = Math.max(6, Math.min(32, playerFontSize + delta));
            // Proporcionalno promijeni i title font (title je ~1.7x veći)
            titleFontSize = Math.round(playerFontSize * 1.71);
            applyPlayerFont();
            saveSettings();
        }

        window.resetPlayerFont = function() {
            playerFontSize = 14;
            titleFontSize = 24;
            applyPlayerFont();
            saveSettings();
        }

        function applyZoom() {
            const wrapper = document.querySelector('.projector-wrapper');
            if (wrapper) {
                wrapper.style.transform = `scale(${zoomLevel})`;
                wrapper.style.transformOrigin = 'top center';
            }
        }

        function applyPlayerFont() {
            document.documentElement.style.setProperty('--player-font-size', playerFontSize + 'px');
            document.documentElement.style.setProperty('--title-font-size', titleFontSize + 'px');
        }

        function applyColumnWidth() {
            const columns = document.querySelectorAll('.knockout-column');
            const containers = document.querySelectorAll('.knockout-bracket-container');
            const isSmallRes = {{ ($resolution ?? 'full') === '1024x768' ? 'true' : 'false' }};

            columns.forEach(col => {
                if (columnWidth === 0) {
                    // Fit to screen mode
                    col.style.minWidth = isSmallRes ? "140px" : "200px";
                    col.style.flex = "1 1 0%";
                } else {
                    // Manual expansion mode
                    const baseWidth = isSmallRes ? 200 : 280;
                    col.style.minWidth = (baseWidth + columnWidth) + 'px';
                    col.style.flex = "0 0 auto";
                }
            });

            containers.forEach(container => {
                if (columnWidth === 0) {
                    container.style.justifyContent = "center";
                    container.classList.remove('overflow-x-auto');
                    container.style.overflowX = 'hidden';
                } else {
                    container.style.justifyContent = "start";
                    container.classList.add('overflow-x-auto');
                    container.style.overflowX = 'auto';
                }
                
                const baseGap = 16;
                const newGap = Math.max(2, baseGap + (columnWidth / 10)); 
                container.style.gap = newGap + 'px';
            });
        }

        function saveSettings() {
            const id = getCurrentCompetitionId();
            if (id) {
                const settings = { 
                    zoom: zoomLevel, 
                    width: columnWidth, 
                    playerFont: playerFontSize,
                    titleFont: titleFontSize 
                };
                localStorage.setItem(`projector_settings_${id}`, JSON.stringify(settings));
            }
        }

        function loadSettings(id) {
            const saved = localStorage.getItem(`projector_settings_${id}`);
            if (saved) {
                const settings = JSON.parse(saved);
                zoomLevel = settings.zoom || 1.0;
                columnWidth = settings.width || 0;
                playerFontSize = settings.playerFont || 14;
                titleFontSize = settings.titleFont || 24;
            } else {
                zoomLevel = 1.0;
                columnWidth = 0;
                playerFontSize = 14;
                titleFontSize = 24;
            }
            applyZoom();
            applyColumnWidth();
            applyPlayerFont();
        }

        function showCompetition(index) {
            const views = document.querySelectorAll('.competition-view');
            const nextView = views[index];
            if (!nextView) return;

            const compId = nextView.dataset.competitionId;
            
            // Load settings for this specific competition
            loadSettings(compId);

            // Remove active from all views
            views.forEach(view => view.classList.remove('active'));
            
            // Add active to next view
            nextView.classList.add('active');
            
            // Re-apply width after content might have loaded
            setTimeout(() => {
                applyColumnWidth();
            }, 50);

            // Update live indicator
            const liveIndicator = document.getElementById('liveIndicator');
            if (liveIndicator) {
                if (rotationData[index] && rotationData[index].has_live) {
                    liveIndicator.classList.remove('hidden');
                } else {
                    liveIndicator.classList.add('hidden');
                }
            }

            // Start timer for next transition
            const duration = rotationData[index] ? Math.max(1, parseInt(rotationData[index].duration) || 20) : 20;
            timeRemaining = duration;

            if (interval) clearInterval(interval);
            interval = setInterval(() => {
                timeRemaining--;

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
                const resolution = '{{ $resolution }}';
                fetch(`/projector/competition/${competitionId}?mode=${config.mode}&layout=${config.layout}&phase=${phase}&resolution=${resolution}`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.querySelector('body').innerHTML;
                        currentView.innerHTML = newContent;
                        
                        // Re-apply settings after refresh
                        applyZoom();
                        applyColumnWidth();
                        applyPlayerFont();
                    })
                    .catch(err => console.error('Failed to refresh:', err));
            }
        }, 30000); // 30 seconds
    </script>
    </div>

</body>
</html>
