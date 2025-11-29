@extends('layouts.public')

@section('title', 'Takmičenja - TeamSphere')

@section('content')
    <!-- Header -->
    <div class="backdrop-blur-xl rounded-2xl p-4 md:p-6 shadow-xl mb-6 md:mb-8 border" style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 25px var(--shadow-primary);">
        <h1 class="text-2xl md:text-3xl font-bold text-center bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
            🏆 Sva Takmičenja
        </h1>
        <p class="text-center mt-2 text-sm md:text-base" style="color: var(--text-tertiary);">Izaberite takmičenje za pregled tabele i mečeva</p>
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
            <div class="backdrop-blur-xl rounded-xl p-4 md:p-6 shadow-xl border" style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 25px var(--shadow-primary);">
                <h2 class="text-lg md:text-xl font-bold mb-4 md:mb-6 flex items-center" style="color: var(--text-primary);">
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
                    <span class="ml-2 text-sm font-normal" style="color: var(--text-tertiary);">({{ $sportCompetitions->count() }})</span>
                </h2>

                <!-- Competitions Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
                    @foreach($sportCompetitions as $competition)
                    <a href="{{ route('public.leagues.show', $competition) }}"
                       class="rounded-lg p-3 md:p-4 border transition-all duration-200 hover:scale-[1.02] group" style="background: var(--bg-tertiary); border-color: var(--border-secondary);">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm md:text-base font-semibold group-hover:text-blue-400 transition-colors" style="color: var(--text-primary); word-break: break-word;">
                                    {{ $competition->name }}
                                </h3>
                                <p class="text-xs md:text-sm" style="color: var(--text-tertiary); word-break: break-word;">
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
                            <span style="color: var(--text-muted);">
                                @if($competition->type === 'tournament')
                                    Turnir
                                @else
                                    Liga
                                @endif
                            </span>
                            <span class="font-medium group-hover:text-blue-300 transition-colors" style="color: var(--accent-blue);">
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
        <h2 class="text-2xl font-bold mb-2" style="color: var(--text-tertiary);">Nema dostupnih takmičenja</h2>
        <p style="color: var(--text-muted);">Provjerite kasnije za predstojeća takmičenja u stonom tenisu.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="text-center mt-8 text-sm" style="color: var(--text-tertiary);">
        <p>Powered by TeamSphere</p>
    </div>
@endsection
