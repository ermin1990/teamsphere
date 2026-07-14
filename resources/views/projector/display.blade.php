<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Projektor - MojTurnir</title>

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
            --score-font-size: 18px;
            --base-font-size: 14px;
            --zoom-scale: 1;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body {
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
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

        /* Score and result styling */
        .match-score,
        .score,
        .result-score,
        .text-xl,
        .text-2xl {
            font-size: var(--score-font-size) !important;
        }

        /* Table cell text */
        table td,
        table th {
            font-size: var(--base-font-size) !important;
        }

        /* Goals, points, stats */
        .goals,
        .points,
        .stats-number,
        .text-lg {
            font-size: calc(var(--base-font-size) * 1.1) !important;
        }

        .projector-content {
            transform: scale(var(--zoom-scale));
            transform-origin: top center;
            transition: transform 300ms ease;
        }

        /* Badge scaling tied to player font size */
        .badge-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: calc(var(--player-font-size) * 1.8);
            height: calc(var(--player-font-size) * 1.8);
        }

        .badge-number {
            font-size: calc(var(--player-font-size) * 0.9) !important;
            line-height: 1;
        }

        /* Transition helpers for switching competitions */
        .transition-fade-enter { opacity: 0; }
        .transition-fade-enter-active { transition: opacity var(--transition-duration, 1000ms) ease; opacity: 1; }
        .transition-fade-exit { opacity: 1; }
        .transition-fade-exit-active { transition: opacity var(--transition-duration, 1000ms) ease; opacity: 0; }

        .transition-slide-left-enter { transform: translateX(30px); opacity: 0; }
        .transition-slide-left-enter-active { transition: transform var(--transition-duration, 1000ms) ease, opacity var(--transition-duration, 1000ms) ease; transform: translateX(0); opacity: 1; }
        .transition-slide-left-exit { transform: translateX(0); opacity: 1; }
        .transition-slide-left-exit-active { transition: transform var(--transition-duration, 1000ms) ease, opacity var(--transition-duration, 1000ms) ease; transform: translateX(-30px); opacity: 0; }

        .transition-slide-up-enter { transform: translateY(20px); opacity: 0; }
        .transition-slide-up-enter-active { transition: transform var(--transition-duration, 1000ms) ease, opacity var(--transition-duration, 1000ms) ease; transform: translateY(0); opacity: 1; }
        .transition-slide-up-exit { transform: translateY(0); opacity: 1; }
        .transition-slide-up-exit-active { transition: transform var(--transition-duration, 1000ms) ease, opacity var(--transition-duration, 1000ms) ease; transform: translateY(-20px); opacity: 0; }

        .transition-zoom-enter { transform: scale(0.95); opacity: 0; }
        .transition-zoom-enter-active { transition: transform var(--transition-duration, 1000ms) ease, opacity var(--transition-duration, 1000ms) ease; transform: scale(1); opacity: 1; }
        .transition-zoom-exit { transform: scale(1); opacity: 1; }
        .transition-zoom-exit-active { transition: transform var(--transition-duration, 1000ms) ease, opacity var(--transition-duration, 1000ms) ease; transform: scale(0.98); opacity: 0; }

        .projector-wrapper {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
        }

        /* Main content area that takes full screen */
        #projectorContainer {
            flex: 1;
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        .competition-view {
            display: none;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        .competition-view.active {
            display: block;
        }

        .scale-wrapper {
            width: 100%;
            height: 100%;
            transform-origin: top left;
            transition: transform 0.3s ease;
        }

        /* Ensure tables and content fit */
        .tournament-standings-container {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
        }

        table {
            width: 100%;
            table-layout: auto;
        }

        /* Progress bar */
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

        /* Custom scrollbar */
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
    <div class="projector-wrapper" id="zoomWrapper">

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
    <div id="projectorContainer">
        @foreach($rotationConfig as $index => $config)
            <div 
                class="competition-view {{ $index === 0 ? 'active' : '' }}" 
                id="competition-{{ $config['id'] }}"
                data-competition-id="{{ $config['id'] }}"
                data-phase="{{ $config['phase'] ?? 'auto' }}"
                data-duration="{{ $config['duration'] }}"
                data-has-live="{{ $config['has_live'] ? 'true' : 'false' }}"
            >
                <div class="scale-wrapper">
                    @if(isset($config['type']) && $config['type'] === 'qr')
                        @php
                            $qrUrl = $config['qr_url'] ?? '';
                            $caption = $config['caption'] ?? ($config['text'] ?? '');
                            $qrImg = 'https://api.qrserver.com/v1/create-qr-code/?size=1000x1000&data=' . urlencode($qrUrl);
                        @endphp
                        <div class="h-screen w-full flex flex-col items-center justify-between py-16 px-12 relative overflow-hidden">
                            <!-- Background abstract shapes for premium look -->
                            <div class="absolute -top-[10%] -right-[10%] w-[50%] h-[50%] bg-blue-600/10 rounded-full blur-[150px]"></div>
                            <div class="absolute -bottom-[10%] -left-[10%] w-[50%] h-[50%] bg-green-600/5 rounded-full blur-[150px]"></div>

                            @if($caption)
                                <div class="flex-shrink-0 w-full mb-4">
                                    <h2 class="text-[min(7vw,9vh)] font-black text-center leading-none uppercase tracking-tighter" style="color: #fff; text-shadow: 0 10px 40px rgba(0,0,0,0.6);">
                                        {{ $caption }}
                                    </h2>
                                </div>
                            @endif

                            <div class="flex-1 flex items-center justify-center w-full min-h-0 py-4">
                                <div class="h-full aspect-square bg-white p-4 rounded-[8%] shadow-[0_0_120px_rgba(0,0,0,0.5)] transform hover:scale-[1.03] transition-transform duration-700">
                                    <img src="{{ $qrImg }}" alt="QR" class="w-full h-full object-contain" />
                                </div>
                            </div>
                            
                            <div class="flex-shrink-0 mt-6 flex items-center gap-10 opacity-30">
                                <span class="h-[2px] w-48 bg-gradient-to-r from-transparent to-white"></span>
                                <span class="text-3xl font-black uppercase tracking-[1em] text-white whitespace-nowrap">SKENIRAJ</span>
                                <span class="h-[2px] w-48 bg-gradient-to-l from-transparent to-white"></span>
                            </div>
                        </div>
                    @else
                        @include('projector.partials.competition-view', [
                            'competition' => $config['competition'],
                            'selectedGroup' => $config['group'] ?? null,
                            'mode' => $mode,
                            'layout' => $layout,
                            'resolution' => $resolution,
                            'phase' => $config['phase'] ?? 'auto'
                        ])
                    @endif
                </div>
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
        // Transition type passed from builder (fade, slide-left, slide-up, zoom, none)
        config.transitionType = '{{ $transitionType ?? 'fade' }}';

        let currentIndex = 0;
        let interval = null;
        let timeRemaining = 0;
        let zoomLevel = 1.0;
        let columnWidth = 0;
        let playerFontSize = 14;
        let titleFontSize = 24;
        let baseFontSize = 14;

        // Global functions
        window.changeZoom = function(delta) {
            zoomLevel = Math.max(0.5, Math.min(2, zoomLevel + delta));
            applyZoom();
            saveSettings();
        };

        window.resetZoom = function() {
            zoomLevel = 1.0;
            applyZoom();
            saveSettings();
        };

        window.changePlayerFont = function(delta) {
            playerFontSize = Math.max(8, Math.min(32, playerFontSize + delta));
            titleFontSize = Math.max(16, Math.min(48, titleFontSize + (delta * 1.5)));
            applyPlayerFont();
            saveSettings();
        };

        window.resetPlayerFont = function() {
            playerFontSize = 14;
            titleFontSize = 24;
            applyPlayerFont();
            saveSettings();
        };

        window.changeColumnWidth = function(delta) {
            if (delta === 0) {
                columnWidth = 0;
            } else {
                columnWidth = Math.max(-50, Math.min(200, columnWidth + delta));
            }
            applyColumnWidth();
            saveSettings();
        };

        function getCurrentCompetitionId() {
            const activeView = document.querySelector('.competition-view.active');
            return activeView ? activeView.dataset.competitionId : null;
        }

        function applyZoom() {
            document.documentElement.style.setProperty('--zoom-scale', zoomLevel);
        }

        // Auto-fit content to available space
        function autoFitContent() {
            const activeView = document.querySelector('.competition-view.active');
            if (!activeView) return;

            const scaleWrapper = activeView.querySelector('.scale-wrapper');
            if (!scaleWrapper) return;

            // Reset transform first
            scaleWrapper.style.transform = 'scale(1)';

            // Wait for DOM to update
            requestAnimationFrame(() => {
                const container = document.getElementById('projectorContainer');
                const containerWidth = container.clientWidth;
                const containerHeight = container.clientHeight;
                
                const contentWidth = scaleWrapper.scrollWidth;
                const contentHeight = scaleWrapper.scrollHeight;

                if (contentWidth > 0 && contentHeight > 0) {
                    // Prioritize width - always use full width
                    const scaleX = containerWidth / contentWidth;
                    const scaleY = containerHeight / contentHeight;
                    
                    // Use the smaller scale to ensure everything fits
                    const scale = Math.min(scaleX, scaleY);

                    scaleWrapper.style.transform = `scale(${scale})`;
                    scaleWrapper.style.width = `${100 / scale}%`;
                }
            });
        }

        function applyPlayerFont() {
            document.documentElement.style.setProperty('--player-font-size', playerFontSize + 'px');
            document.documentElement.style.setProperty('--title-font-size', titleFontSize + 'px');
            document.documentElement.style.setProperty('--score-font-size', (playerFontSize * 1.3) + 'px');
            document.documentElement.style.setProperty('--base-font-size', playerFontSize + 'px');
            
            // Re-fit content after font change
            setTimeout(() => autoFitContent(), 100);
        }

        function applyColumnWidth() {
            const columns = document.querySelectorAll('.knockout-column');
            const containers = document.querySelectorAll('.knockout-bracket-container');

            columns.forEach(col => {
                if (columnWidth === 0) {
                    col.style.minWidth = "200px";
                    col.style.flex = "1 1 0%";
                } else {
                    const baseWidth = 280;
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

            const prevView = document.querySelector('.competition-view.active');
                const transition = config.transitionType || 'fade';
            const speed = parseInt(config.transitionSpeed) || 400;
            // Set CSS var for transition duration so classes use correct timing
            document.documentElement.style.setProperty('--transition-duration', speed + 'ms');

            // If there's no previous view, just show the next one
            if (!prevView || prevView === nextView || transition === 'none') {
                views.forEach(view => view.classList.remove('active'));
                nextView.classList.add('active');
                setTimeout(() => {
                    applyColumnWidth();
                    autoFitContent();
                }, 100);
            } else {
                // Apply exit class to previous and enter class to next
                const enterClass = `transition-${transition}-enter`;
                const enterActive = `transition-${transition}-enter-active`;
                const exitClass = `transition-${transition}-exit`;
                const exitActive = `transition-${transition}-exit-active`;

                // Prepare next view
                nextView.classList.add('active', enterClass);

                // Force reflow then start enter animation
                // eslint-disable-next-line @typescript-eslint/no-unused-expressions
                nextView.offsetWidth;
                nextView.classList.add(enterActive);

                // Start exit animation on prev
                prevView.classList.add(exitClass);
                // force reflow
                prevView.offsetWidth;
                prevView.classList.add(exitActive);

                // After animation ends, cleanup
                setTimeout(() => {
                    prevView.classList.remove('active', exitClass, exitActive);
                    nextView.classList.remove(enterClass, enterActive);
                    applyColumnWidth();
                    autoFitContent();
                }, Math.max(50, speed));
            }

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

        // Font size change function (+ and - keys)
        function changeFontSize(delta) {
            playerFontSize = Math.max(8, Math.min(40, playerFontSize + delta));
            titleFontSize = Math.max(16, Math.min(60, titleFontSize + (delta * 1.7)));
            applyPlayerFont();
            saveSettings();
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
            } else if (e.key === '+' || e.key === '=') {
                // Povećaj font
                changeFontSize(1);
            } else if (e.key === '-' || e.key === '_') {
                // Smanji font
                changeFontSize(-1);
            } else if (e.key === '0') {
                // Reset font na default
                playerFontSize = 14;
                titleFontSize = 24;
                applyPlayerFont();
                saveSettings();
            }
        });

        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                autoFitContent();
            }, 100);
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
                        
                        const scaleWrapper = currentView.querySelector('.scale-wrapper');
                        if (scaleWrapper) {
                            scaleWrapper.innerHTML = newContent;
                        }
                        
                        // Re-apply settings after refresh
                        applyZoom();
                        applyColumnWidth();
                        applyPlayerFont();
                        
                        // Re-fit content
                        setTimeout(() => autoFitContent(), 200);
                    })
                    .catch(err => console.error('Failed to refresh:', err));
            }
        }, 30000); // 30 seconds
    </script>
    </div>

</body>
</html>