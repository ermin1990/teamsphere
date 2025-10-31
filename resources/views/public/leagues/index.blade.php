@extends('layouts.public')

@section('title', 'Takmičenja - TeamSphere')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Collect all competitions for rotation
    const competitions = {!! $competitions->map(function($comp) {
        return [
            'id' => $comp->id,
            'name' => $comp->name,
            'type' => $comp->type,
            'organization' => $comp->organization->name,
            'sport' => $comp->sport->name,
            'url' => route('public.leagues.semafor', $comp)
        ];
    })->toJson() !!};

    let currentIndex = 0;
    let rotationInterval;
    let isPaused = false;

    const semaforContainer = document.getElementById('semafor-container');
    const pauseBtn = document.getElementById('pause-rotation');
    const playBtn = document.getElementById('play-rotation');
    const prevBtn = document.getElementById('prev-competition');
    const nextBtn = document.getElementById('next-competition');

    // Simple content for each competition
    const competitionContents = competitions.map(comp => `
        <div class="bg-gradient-to-br from-blue-500/10 to-purple-500/10 border-2 border-blue-500/30 rounded-2xl p-4 md:p-6">
            <div class="text-center">
                <div class="flex items-center justify-center space-x-4 mb-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center">
                        <span class="text-2xl">${comp.type === 'tournament' ? '�' : '�🏆'}</span>
                    </div>
                    <div class="text-left">
                        <div class="text-blue-400 font-bold text-lg md:text-xl mb-1">${comp.sport}</div>
                        <div class="text-gray-400 text-sm">Organizator: ${comp.organization}</div>
                    </div>
                </div>
                <h2 class="text-2xl md:text-3xl font-black text-white mb-4">${comp.name}</h2>
                                <p class="text-gray-400 text-sm md:text-base mb-4">
                    ${comp.type === 'tournament' ? 'Turnir' : 'Liga'} • Kliknite za semafor prikaz
                </p>
                <div class="flex items-center justify-center space-x-2 text-blue-400 font-semibold group-hover:translate-x-2 transition-transform">
                    <span>Pogledaj semafor</span>
            </div>
        </div>
    `);

    function updateSemafor(index) {
        if (competitions.length === 0) return;

        const comp = competitions[index];
        semaforContainer.innerHTML = `
            <a href="${comp.url}" class="block cursor-pointer">
                ${competitionContents[index]}
            </a>
        `;

        // Update indicator dots
        updateIndicators(index);
    }

    function updateIndicators(activeIndex) {
        const indicators = document.getElementById('rotation-indicators');
        if (!indicators) return;

        indicators.innerHTML = competitions.map((_, index) => `
            <button onclick="goToCompetition(${index})"
                    class="w-3 h-3 rounded-full transition-all ${index === activeIndex ? 'bg-blue-500 scale-125' : 'bg-gray-600 hover:bg-gray-500'}">
            </button>
        `).join('');
    }

    function startRotation() {
        if (rotationInterval) clearInterval(rotationInterval);
        rotationInterval = setInterval(() => {
            if (!isPaused) {
                currentIndex = (currentIndex + 1) % competitions.length;
                updateSemafor(currentIndex);
            }
        }, 10000); // 10 seconds
    }

    function stopRotation() {
        if (rotationInterval) {
            clearInterval(rotationInterval);
            rotationInterval = null;
        }
    }

    // Control buttons
    if (pauseBtn) {
        pauseBtn.addEventListener('click', function() {
            isPaused = true;
            pauseBtn.classList.add('hidden');
            playBtn.classList.remove('hidden');
        });
    }

    if (playBtn) {
        playBtn.addEventListener('click', function() {
            isPaused = false;
            pauseBtn.classList.remove('hidden');
            playBtn.classList.add('hidden');
            startRotation();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentIndex = currentIndex > 0 ? currentIndex - 1 : competitions.length - 1;
            updateSemafor(currentIndex);
            if (!isPaused) {
                stopRotation();
                startRotation();
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            currentIndex = (currentIndex + 1) % competitions.length;
            updateSemafor(currentIndex);
            if (!isPaused) {
                stopRotation();
                startRotation();
            }
        });
    }

    // Global function for indicator clicks
    window.goToCompetition = function(index) {
        currentIndex = index;
        updateSemafor(currentIndex);
        if (!isPaused) {
            stopRotation();
            startRotation();
        }
    };

    // Initialize
    if (competitions.length > 0) {
        updateSemafor(0);
        startRotation();
    }
});
</script>
@endpush

@section('content')
            
        

            <!-- Header -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 md:p-6 border border-gray-700/50 shadow-xl mb-6 md:mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-center bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                    🏆 Sva Takmičenja
                </h1>
                <p class="text-gray-400 text-center mt-2 text-sm md:text-base">Izaberite takmičenje za pregled tabele i mečeva</p>
            </div>

            @if($competitions->count() > 0)
                <!-- Group competitions by sport -->
                @php
                    $competitionsBySport = $competitions->groupBy(function($competition) {
                        return $competition->sport->name;
                    });
                @endphp

                <div class="space-y-6 md:space-y-8">
                    @foreach($competitionsBySport as $sportName => $sportCompetitions)
                    <!-- Sport Section -->
                    <div class="bg-gray-800/30 backdrop-blur-xl rounded-xl p-4 md:p-6 border border-gray-700/30 shadow-xl">
                        <h2 class="text-lg md:text-xl font-bold text-white mb-4 md:mb-6 flex items-center">
                            <span class="text-2xl mr-3">
                                @if(strtolower($sportName) === 'stoni tenis')
                                    🏓
                                @elseif(strtolower($sportName) === 'fudbal')
                                    ⚽
                                @elseif(strtolower($sportName) === 'košarka')
                                    🏀
                                @elseif(strtolower($sportName) === 'odbojka')
                                    🏐
                                @else
                                    🏆
                                @endif
                            </span>
                            {{ $sportName }}
                            <span class="ml-2 text-sm text-gray-400 font-normal">({{ $sportCompetitions->count() }})</span>
                        </h2>

                        <!-- Competitions Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
                            @foreach($sportCompetitions as $competition)
                            <a href="{{ route('public.leagues.show', $competition) }}"
                               class="bg-gray-700/30 hover:bg-gray-700/50 rounded-lg p-3 md:p-4 border border-gray-600/20 hover:border-gray-500/40 transition-all duration-200 hover:scale-[1.02] group">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm md:text-base font-semibold text-white group-hover:text-blue-400 transition-colors truncate">
                                            {{ $competition->name }}
                                        </h3>
                                        <p class="text-xs md:text-sm text-gray-400 truncate">
                                            {{ $competition->organization->name }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 ml-2">
                                        <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <span class="text-sm md:text-base">
                                                @if($competition->type === 'tournament')
                                                    🏅
                                                @else
                                                    🏆
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500">
                                        @if($competition->type === 'tournament')
                                            Turnir
                                        @else
                                            Liga
                                        @endif
                                    </span>
                                    <span class="text-blue-400 font-medium group-hover:text-blue-300">
                                        Pogledaj →
                                    </span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🏓</div>
                <h2 class="text-2xl font-bold text-gray-400 mb-2">Nema dostupnih takmičenja</h2>
                <p class="text-gray-500">Provjerite kasnije za predstojeća takmičenja u stonom tenisu.</p>
            </div>
            @endif

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-400 text-sm">
                <p>Powered by TeamSphere</p>
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
                <span class="mt-1">Competitions</span>
            </a>
        </div>
    </nav>
</body>
</html>
@endsection
