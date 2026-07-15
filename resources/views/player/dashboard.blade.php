<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl sm:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent" style="font-family: 'Unbounded', ui-sans-serif, sans-serif; letter-spacing: -0.01em;">
                Moje lige
            </h2>
            <p class="text-sm mt-1" style="color: var(--text-tertiary);">Tvoji mečevi i takmičenja na jednom mjestu</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('player.partials.nav')
        </div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
            @if(session('success'))
                <div class="rounded-xl p-4 text-sm" style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #4ade80;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-xl p-4 text-sm" style="background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3); color: #f87171;">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                {{-- Naredni mečevi --}}
                <div class="rounded-2xl p-5 sm:p-6 backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold flex items-center gap-2" style="color: var(--text-primary);">
                            <span>📅</span> Naredni mečevi
                        </h3>
                        @if($upcomingMatches->isNotEmpty())
                            <a href="{{ route('player.dashboard.matches') }}" class="text-xs font-semibold" style="color: var(--accent-blue);">Svi →</a>
                        @endif
                    </div>
                    <div class="space-y-2">
                    @forelse($upcomingMatches as $match)
                        @php
                            $isHome = $playerIds->contains($match->home_player_id);
                            $opponent = $isHome ? $match->awayPlayer : $match->homePlayer;
                            $canEnterResult = $match->competition
                                && $match->competition->isLeague()
                                && !$match->competition->is_team_based
                                && $match->competition->status === 'active';
                        @endphp
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between p-3 rounded-xl" style="background: var(--bg-hover); border: 1px solid var(--border-secondary);">
                            <div class="min-w-0">
                                <p class="font-semibold text-sm truncate" style="color: var(--text-primary);">protiv {{ $opponent->name ?? 'TBD' }}</p>
                                <p class="text-xs truncate mt-0.5" style="color: var(--text-tertiary);">
                                    {{ $match->competition->name ?? '' }}
                                    @if($match->scheduled_at) · {{ $match->scheduled_at->format('d.m.Y. H:i') }} @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if($match->status === 'in_progress')
                                    <span class="px-2.5 py-1 text-[11px] rounded-full font-bold animate-pulse" style="background: rgba(248,113,113,0.2); color: #f87171;">UŽIVO</span>
                                @else
                                    <span class="px-2.5 py-1 text-[11px] rounded-full font-semibold" style="background: rgba(234,179,8,0.15); color: #eab308;">Zakazano</span>
                                @endif
                                @if($canEnterResult)
                                    <a href="{{ route('player.matches.live', $match) }}" class="text-xs font-semibold whitespace-nowrap" style="color: #4ade80;">Uživo</a>
                                    <a href="{{ route('player.matches.result.edit', $match) }}" class="text-xs font-semibold whitespace-nowrap" style="color: #c4b5fd;">Upiši rezultat</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm py-2" style="color: var(--text-muted);">Nemaš zakazanih mečeva.</p>
                    @endforelse
                    </div>
                </div>

                {{-- Završeni mečevi --}}
                <div class="rounded-2xl p-5 sm:p-6 backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold flex items-center gap-2" style="color: var(--text-primary);">
                            <span>🏁</span> Završeni mečevi
                        </h3>
                        @if($completedMatches->isNotEmpty())
                            <a href="{{ route('player.dashboard.matches') }}" class="text-xs font-semibold" style="color: var(--accent-blue);">Svi →</a>
                        @endif
                    </div>
                    <div class="space-y-2">
                    @forelse($completedMatches as $match)
                        @php
                            $isHome = $playerIds->contains($match->home_player_id);
                            $opponent = $isHome ? $match->awayPlayer : $match->homePlayer;
                            $myScore = $isHome ? $match->home_score : $match->away_score;
                            $theirScore = $isHome ? $match->away_score : $match->home_score;
                            $win = $myScore > $theirScore; $loss = $myScore < $theirScore;
                            $canEditResult = $match->competition
                                && $match->competition->isLeague()
                                && !$match->competition->is_team_based
                                && $match->competition->status === 'active';
                        @endphp
                        <div class="flex items-center justify-between p-3 rounded-xl" style="background: var(--bg-hover); border: 1px solid var(--border-secondary);">
                            <div class="min-w-0">
                                <p class="font-semibold text-sm truncate" style="color: var(--text-primary);">protiv {{ $opponent->name ?? 'TBD' }}</p>
                                <p class="text-xs truncate mt-0.5" style="color: var(--text-tertiary);">
                                    {{ $match->competition->name ?? '' }}
                                    @if($match->played_at) · {{ $match->played_at->format('d.m.Y.') }} @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 ml-2">
                                <span class="text-sm font-black" style="color: {{ $win ? 'var(--accent-green-solid)' : ($loss ? 'var(--accent-red)' : 'var(--text-tertiary)') }};">
                                    {{ $myScore }} : {{ $theirScore }}
                                </span>
                                @if($canEditResult)
                                    <a href="{{ route('player.matches.result.edit', $match) }}" class="text-xs font-semibold whitespace-nowrap" style="color: #c4b5fd;">Uredi</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm py-2" style="color: var(--text-muted);">Još nemaš odigranih mečeva.</p>
                    @endforelse
                    </div>
                </div>
            </div>

            @if($bySeason->isNotEmpty())
                <h3 class="text-[11px] font-bold uppercase tracking-[0.18em] px-1" style="color: var(--text-muted); font-family: 'Unbounded', ui-sans-serif, sans-serif;">Moja takmičenja</h3>
            @endif
            @forelse($bySeason as $seasonName => $seasonCompetitions)
                <div class="rounded-2xl p-5 sm:p-6 backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                    <h3 class="text-base font-bold mb-4" style="color: var(--text-primary);">{{ $seasonName }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        @foreach($seasonCompetitions as $competition)
                            <div class="mt-comp-card flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between p-4 rounded-xl transition-all" style="background: var(--bg-hover); border: 1px solid var(--border-secondary);">
                                <a href="{{ route('player.leagues.show', $competition) }}" class="min-w-0 sm:flex-1">
                                    <p class="font-semibold truncate" style="color: var(--text-primary);">{{ $competition->name }}</p>
                                    <p class="text-xs mt-1 truncate" style="color: var(--text-tertiary);">
                                        {{ $competition->organization->name }}
                                        @if(isset($rankings[$competition->organization_id]))
                                            · Rang {{ $rankings[$competition->organization_id]['position'] }}/{{ $rankings[$competition->organization_id]['total'] }}
                                        @endif
                                    </p>
                                </a>
                                <div class="flex items-center gap-4 shrink-0">
                                    @if($competition->isLeague())
                                        <a href="{{ route('player.matches.create', $competition) }}" class="text-sm font-semibold whitespace-nowrap" style="color: #c4b5fd;">+ Zabilježi</a>
                                    @endif
                                    <a href="{{ route('player.leagues.show', $competition) }}" class="text-sm font-semibold whitespace-nowrap" style="color: var(--accent-blue);">Pogledaj →</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="rounded-2xl p-8 sm:p-10 text-center backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                    <p style="color: var(--text-secondary);">Još nisi dodan ni na jedno takmičenje.</p>
                    <p class="text-sm mt-2" style="color: var(--text-muted);">Kada te organizator doda ili pozove na ligu, ona će se pojaviti ovdje.</p>
                    <a href="{{ route('player.leagues.index') }}" class="inline-block mt-5 px-6 py-3 font-semibold rounded-full transition-all active:scale-95" style="background: var(--accent-blue); color: #14141F;">
                        Pronađi ligu
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    @once
    <style>
        .mt-comp-card:hover { border-color: var(--border-accent) !important; }
    </style>
    @endonce
</x-app-layout>
