@extends('layouts.public')

@section('title', isset($organization) ? $organization->name . ' - MojTurnir' : 'Takmičenja - MojTurnir')

@section('content')
    <!-- Header -->
    <div class="backdrop-blur-xl rounded-2xl p-6 md:p-8 shadow-xl mb-6 md:mb-8 border" style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 25px var(--shadow-primary);">
        @if(isset($organization))
            <div class="space-y-6">
                @if($organization->logo_url || $organization->logo)
                    <div class="w-full">
                        <img src="{{ $organization->logo_url ?? asset('storage/' . $organization->logo) }}" 
                             alt="{{ $organization->name }}" 
                             class="w-full max-w-2xl rounded-2xl object-contain" 
                             style="max-height: 200px;">
                    </div>
                @endif
                <div>
                    <h1 class="text-3xl md:text-5xl font-bold mb-2" style="color: var(--text-primary);">
                        {{ $organization->name }}
                    </h1>
                    <p class="text-base md:text-lg font-medium" style="color: var(--text-secondary);">
                        Aktivna takmičenja i rezultati uživo
                    </p>
                    @if($organization->description)
                        <p class="mt-2 text-sm md:text-base" style="color: var(--text-tertiary);">
                            {{ Str::limit($organization->description, 120) }}
                        </p>
                    @endif
                </div>
            </div>
        @else
            <div>
                <h1 class="text-3xl md:text-5xl font-bold mb-3" style="color: var(--text-primary);">
                    🏆 Sva Takmičenja
                </h1>
                <p class="text-base md:text-lg font-medium" style="color: var(--text-tertiary);">
                    Izaberite takmičenje za pregled tabele i mečeva
                </p>
            </div>
        @endif
    </div>

    @if(isset($organization) && $organization->links->count() > 0)
        <!-- Organization Links/Banners -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8">
            @foreach($organization->links as $link)
                @php
                    $isYoutube = str_contains(strtolower($link->url), 'youtube');
                    $isFacebook = str_contains(strtolower($link->url), 'facebook');
                    $isInstagram = str_contains(strtolower($link->url), 'instagram');
                @endphp
                <a href="{{ $link->url }}" target="_blank" 
                   class="relative overflow-hidden rounded-2xl p-6 border-2 transition-all duration-300 hover:scale-[1.05] hover:shadow-2xl group"
                   style="
                        @if($isYoutube)
                            background: linear-gradient(135deg, #FF0000 0%, #CC0000 50%, #8B0000 100%);
                            border-color: #FF0000;
                            box-shadow: 0 10px 30px rgba(255, 0, 0, 0.3);
                        @elseif($isFacebook)
                            background: linear-gradient(135deg, #1877F2 0%, #0C63D4 50%, #0952B8 100%);
                            border-color: #1877F2;
                            box-shadow: 0 10px 30px rgba(24, 119, 242, 0.3);
                        @elseif($isInstagram)
                            background: linear-gradient(135deg, #E4405F 0%, #C13584 50%, #833AB4 100%);
                            border-color: #E4405F;
                            box-shadow: 0 10px 30px rgba(228, 64, 95, 0.3);
                        @else
                            background: linear-gradient(135deg, #6366F1 0%, #4F46E5 50%, #4338CA 100%);
                            border-color: #6366F1;
                            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
                        @endif
                   ">
                    <!-- Animated background effect -->
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-20 transition-opacity duration-300" 
                         style="background: radial-gradient(circle at center, white 0%, transparent 70%);"></div>
                    
                    <div class="relative flex items-center space-x-4">
                        <div class="w-16 h-16 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center flex-shrink-0 text-white transition-transform group-hover:scale-110 group-hover:rotate-6" 
                             style="box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
                            @if($isYoutube)
                                <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            @elseif($isFacebook)
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            @elseif($isInstagram)
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-base md:text-lg text-white mb-1 group-hover:translate-x-1 transition-transform">
                                {{ $link->title }}
                            </h4>
                            <p class="text-xs md:text-sm text-white/80 flex items-center">
                                <span>Posjetite</span>
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

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
                                @if(!isset($organization))
                                    <p class="text-xs md:text-sm" style="color: var(--text-tertiary); word-break: break-word;">
                                        {{ $competition->organization->name }}
                                    </p>
                                @endif
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
        <p>Powered by MojTurnir</p>
    </div>
@endsection
