<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Moje lige - MojTurnir</title>

<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#0b0e14">
<link rel="apple-touch-icon" href="/icons/icon-192.png">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Inter:wght@400;600&display=swap">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap">

<script>
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "surface-container-lowest": "#0b0e14",
                    "surface-dim": "#10131a",
                    "surface": "#10131a",
                    "surface-container-low": "#191c22",
                    "surface-container": "#1d2026",
                    "surface-container-high": "#272a31",
                    "surface-container-highest": "#32353c",
                    "surface-variant": "#32353c",
                    "surface-bright": "#363940",
                    "on-surface": "#e1e2eb",
                    "on-surface-variant": "#bacac5",
                    "outline": "#859490",
                    "outline-variant": "#3c4a46",
                    "primary": "#57f1db",
                    "primary-container": "#2dd4bf",
                    "on-primary": "#003731",
                    "on-primary-container": "#00574d",
                    "secondary": "#ffb95f",
                    "secondary-container": "#ee9800",
                    "on-secondary-container": "#5b3800",
                    "tertiary-container": "#b3bed5",
                    "on-tertiary-container": "#424d61",
                    "error": "#ffb4ab",
                },
                borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
                spacing: { gutter: "24px", "margin-mobile": "16px", "sidebar-width": "260px", "container-max": "1280px", base: "8px" },
                fontFamily: {
                    "headline-md": ["Montserrat"], "body-sm": ["Inter"], display: ["Montserrat"],
                    "headline-lg-mobile": ["Montserrat"], "body-md": ["Inter"], "body-lg": ["Inter"],
                    "label-bold": ["Inter"], "headline-lg": ["Montserrat"],
                },
                fontSize: {
                    "headline-md": ["20px", { lineHeight: "1.3", fontWeight: "700" }],
                    "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                    display: ["48px", { lineHeight: "1.1", letterSpacing: "-0.02em", fontWeight: "800" }],
                    "headline-lg-mobile": ["24px", { lineHeight: "1.2", fontWeight: "700" }],
                    "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                    "label-bold": ["12px", { lineHeight: "1.2", letterSpacing: "0.05em", fontWeight: "600" }],
                    "headline-lg": ["32px", { lineHeight: "1.2", letterSpacing: "-0.01em", fontWeight: "700" }],
                },
            },
        },
    }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
    body { background-color: #0b0e14; color: #e1e2eb; overflow-x: hidden; -webkit-tap-highlight-color: transparent; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #10131a; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #32353c; border-radius: 10px; }
    .match-card:hover, .match-card:active { border-color: #57f1db; box-shadow: 0 0 15px rgba(45, 212, 191, 0.15); }
</style>
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen pb-24 lg:pb-8">

@php
    $initials = mb_strtoupper(collect(preg_split('/\s+/', trim(auth()->user()->name ?? '')))->filter()->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')) ?: '?';

    $newMatchCompetitions = \App\Models\Competition::whereHas('players', function ($q) {
            $q->where('players.user_id', auth()->id());
        })
        ->where('is_team_based', false)
        ->where('status', 'active')
        ->orderBy('name')
        ->get();
@endphp

<!-- Persistent SideNavBar (desktop) -->
<nav class="hidden lg:flex w-sidebar-width h-screen fixed left-0 top-0 border-r border-outline-variant bg-surface-container-low flex-col py-2 z-50">
    <div class="px-6 py-8">
        <a href="{{ route('player.dashboard') }}" class="font-display text-primary tracking-tighter text-2xl uppercase block">MojTurnir</a>
        <p class="font-label-bold text-on-surface-variant opacity-70 text-xs">Moje lige</p>
    </div>
    <div class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar">
        <a class="flex items-center gap-3 px-4 py-3 text-primary border-l-4 border-primary bg-primary/5 font-label-bold" href="{{ route('player.dashboard') }}">
            <span class="material-symbols-outlined">dashboard</span><span>Dashboard</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors duration-200 rounded-lg" href="{{ route('player.dashboard.matches') }}">
            <span class="material-symbols-outlined">sports_tennis</span><span>Moji mečevi</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors duration-200 rounded-lg" href="{{ route('player.leagues.index') }}">
            <span class="material-symbols-outlined">emoji_events</span><span>Takmičenja</span>
        </a>
    </div>
    <div class="px-4 py-6 border-t border-outline-variant space-y-1">
        <a class="flex items-center gap-3 px-4 py-2 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors rounded-lg" href="{{ route('profile.edit') }}">
            <span class="material-symbols-outlined">account_circle</span><span>Nalog</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors rounded-lg text-left">
                <span class="material-symbols-outlined">logout</span><span>Odjava</span>
            </button>
        </form>
    </div>
</nav>

<!-- Mobile Top App Bar -->
<header class="lg:hidden sticky top-0 z-40 bg-surface border-b border-outline-variant h-16 flex items-center justify-between px-margin-mobile">
    <span class="font-display text-headline-md text-primary">MojTurnir</span>
    <div class="flex items-center gap-2">
        <a href="{{ route('profile.edit') }}" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container-high transition-colors">
            <span class="material-symbols-outlined text-on-surface-variant">settings</span>
        </a>
        <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-label-bold text-xs">{{ $initials }}</div>
    </div>
</header>

<!-- Desktop Top App Bar -->
<header class="hidden lg:flex justify-end items-center px-gutter w-[calc(100%-260px)] ml-[260px] h-16 fixed top-0 z-40 bg-surface border-b border-outline-variant">
    <div class="flex items-center gap-4">
        <a href="{{ route('profile.edit') }}" class="text-on-surface-variant hover:text-primary transition-colors">
            <span class="material-symbols-outlined">settings</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-label-bold text-xs border border-outline-variant">{{ $initials }}</a>
    </div>
</header>

<!-- Main Content Canvas -->
<main class="lg:ml-[260px] lg:mt-16 p-margin-mobile lg:p-gutter min-h-screen">

    @if(session('success'))
        <div class="mb-6 rounded-xl p-4 text-sm bg-primary/10 border border-primary/30 text-primary">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-xl p-4 text-sm bg-error/10 border border-error/30 text-error">{{ session('error') }}</div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 lg:gap-6 mb-8 lg:mb-12">
        <div>
            <h1 class="font-headline-lg-mobile lg:font-display text-headline-lg-mobile lg:text-headline-lg text-primary tracking-tight">Moje lige</h1>
            <p class="text-on-surface-variant text-sm lg:text-base mt-1">Tvoji mečevi i takmičenja na jednom mjestu</p>
        </div>

        @if($newMatchCompetitions->count() === 1)
            <a href="{{ route('player.matches.create', $newMatchCompetitions->first()) }}"
               class="inline-flex items-center gap-1.5 self-start bg-primary-container text-on-primary-container font-label-bold px-4 py-2.5 lg:px-6 rounded-full hover:opacity-90 transition-all active:scale-95">
                <span class="material-symbols-outlined text-lg">add</span> Novi meč
            </a>
        @elseif($newMatchCompetitions->count() > 1)
            <select onchange="if(this.value) window.location = this.value;"
                    class="self-start bg-primary-container text-on-primary-container font-label-bold px-4 py-2.5 lg:px-6 rounded-full border-0 focus:ring-2 focus:ring-primary transition-all">
                <option value="">+ Novi meč...</option>
                @foreach($newMatchCompetitions as $newMatchCompetition)
                    <option value="{{ route('player.matches.create', $newMatchCompetition) }}">{{ $newMatchCompetition->name }}</option>
                @endforeach
            </select>
        @endif
    </div>

    <!-- Matches Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-gutter mb-10 lg:mb-12">
        {{-- Naredni mečevi --}}
        <section class="bg-surface-container-low border border-outline-variant rounded-xl p-4 lg:p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 lg:gap-3">
                    <span class="material-symbols-outlined text-primary lg:bg-primary/10 lg:p-2 lg:rounded-lg text-lg lg:text-2xl">calendar_month</span>
                    <h2 class="font-label-bold lg:font-headline-md text-label-bold lg:text-headline-md uppercase lg:normal-case tracking-wider lg:tracking-normal text-on-surface">Naredni mečevi</h2>
                </div>
                @if($upcomingMatches->isNotEmpty())
                    <a href="{{ route('player.dashboard.matches') }}" class="text-primary font-label-bold text-label-bold flex items-center gap-1 hover:underline shrink-0">
                        Svi <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                @endif
            </div>
            <div class="space-y-3 lg:max-h-[400px] lg:overflow-y-auto custom-scrollbar lg:pr-2">
                @forelse($upcomingMatches as $match)
                    @php
                        $isHome = $playerIds->contains($match->home_player_id);
                        $opponent = $isHome ? $match->awayPlayer : $match->homePlayer;
                        $canEnterResult = $match->competition
                            && $match->competition->isLeague()
                            && !$match->competition->is_team_based
                            && $match->competition->status === 'active';
                    @endphp
                    <div class="match-card bg-surface-container border border-outline-variant rounded-lg p-3 space-y-2 transition-all">
                        <div class="flex justify-between items-start gap-2">
                            <div class="min-w-0">
                                <h3 class="text-on-surface font-semibold text-sm truncate">protiv {{ $opponent->name ?? 'TBD' }}</h3>
                                <p class="font-label-bold text-[10px] text-on-surface-variant uppercase truncate">
                                    {{ $match->competition->name ?? '' }}
                                    @if($match->scheduled_at) · {{ $match->scheduled_at->format('d.m.Y. H:i') }} @endif
                                </p>
                            </div>
                            @if($match->status === 'in_progress')
                                <span class="bg-secondary/15 text-secondary text-[10px] px-2 py-1 rounded-full font-bold border border-secondary/20 animate-pulse shrink-0">UŽIVO</span>
                            @else
                                <span class="bg-surface-container-highest text-secondary text-[10px] px-2 py-0.5 rounded border border-outline-variant shrink-0">Zakazano</span>
                            @endif
                        </div>
                        @if($canEnterResult)
                            <div class="flex justify-end gap-4 pt-1">
                                @if($match->status !== 'completed')
                                    <a href="{{ route('player.matches.live', $match) }}" class="text-secondary font-label-bold text-[11px] uppercase tracking-widest hover:opacity-80">Uživo</a>
                                @endif
                                <a href="{{ route('player.matches.result.edit', $match) }}" class="text-primary font-label-bold text-[11px] uppercase tracking-widest hover:opacity-80">Upiši rezultat</a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="border border-dashed border-outline-variant/40 rounded-lg p-6 flex flex-col items-center justify-center text-center opacity-60">
                        <span class="material-symbols-outlined text-3xl mb-2 text-on-surface-variant">event_available</span>
                        <p class="text-sm font-label-bold text-on-surface-variant">Nemaš zakazanih mečeva.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Završeni mečevi --}}
        <section class="bg-surface-container-low border border-outline-variant rounded-xl p-4 lg:p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 lg:gap-3">
                    <span class="material-symbols-outlined text-primary lg:bg-primary/10 lg:p-2 lg:rounded-lg text-lg lg:text-2xl">sports_score</span>
                    <h2 class="font-label-bold lg:font-headline-md text-label-bold lg:text-headline-md uppercase lg:normal-case tracking-wider lg:tracking-normal text-on-surface">Završeni mečevi</h2>
                </div>
                @if($completedMatches->isNotEmpty())
                    <a href="{{ route('player.dashboard.matches') }}" class="text-primary font-label-bold text-label-bold flex items-center gap-1 hover:underline shrink-0">
                        Svi <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                @endif
            </div>
            <div class="space-y-3">
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
                    <div class="match-card bg-surface-container border border-outline-variant rounded-lg p-3 flex items-center justify-between gap-2 transition-all">
                        <div class="min-w-0">
                            <h3 class="text-on-surface font-semibold text-sm truncate">protiv {{ $opponent->name ?? 'TBD' }}</h3>
                            <p class="font-label-bold text-[10px] text-on-surface-variant uppercase truncate">
                                {{ $match->competition->name ?? '' }}
                                @if($match->played_at) · {{ $match->played_at->format('d.m.Y.') }} @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-4 shrink-0">
                            <span class="font-headline-md text-lg lg:text-xl font-bold {{ $win ? 'text-primary' : ($loss ? 'text-error' : 'text-on-surface-variant') }}">
                                {{ $myScore }} : {{ $theirScore }}
                            </span>
                            @if($canEditResult)
                                <a href="{{ route('player.matches.result.edit', $match) }}" class="text-on-surface-variant hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="border border-dashed border-outline-variant/40 rounded-lg p-6 flex flex-col items-center justify-center text-center opacity-60">
                        <span class="material-symbols-outlined text-3xl mb-2 text-on-surface-variant">history</span>
                        <p class="text-sm font-label-bold text-on-surface-variant">Još nemaš odigranih mečeva.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Moja takmičenja -->
    <section>
        @if($bySeason->isNotEmpty())
            <h3 class="font-label-bold text-label-bold uppercase tracking-[0.2em] text-on-surface-variant opacity-70 mb-6">Moja takmičenja</h3>
        @endif
        @forelse($bySeason as $seasonName => $seasonCompetitions)
            <div class="bg-surface-container-low border border-outline-variant rounded-xl overflow-hidden mb-4 lg:mb-6">
                <div class="bg-surface-container-high px-4 lg:px-8 py-3 lg:py-4 border-b border-outline-variant">
                    <span class="font-display text-headline-md text-on-surface">{{ $seasonName }}</span>
                </div>
                <div class="p-4 lg:p-6 space-y-4">
                    @foreach($seasonCompetitions as $competition)
                        <div class="bg-surface-container-highest border border-outline-variant rounded-lg p-4 lg:p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div class="min-w-0">
                                <h4 class="font-display text-lg lg:text-2xl font-extrabold text-primary mb-1 truncate">{{ $competition->name }}</h4>
                                <div class="flex flex-wrap items-center gap-2 text-on-surface-variant text-xs lg:text-sm">
                                    <span>{{ $competition->organization->name }}@if($competition->sport) &middot; {{ $competition->sport->name }} @endif</span>
                                    @if(isset($rankings[$competition->organization_id]))
                                        <span class="w-1 h-1 bg-outline-variant rounded-full hidden lg:inline-block"></span>
                                        <span class="font-semibold text-on-surface">Rang {{ $rankings[$competition->organization_id]['position'] }}/{{ $rankings[$competition->organization_id]['total'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 lg:gap-4 lg:border-t-0 border-t border-outline-variant pt-3 lg:pt-0">
                                @if($competition->isLeague())
                                    <a href="{{ route('player.matches.create', $competition) }}" class="flex-1 lg:flex-none justify-center px-5 py-2 rounded-lg border border-primary text-primary font-bold text-xs lg:text-[13px] hover:bg-primary/5 transition-colors flex items-center gap-2">
                                        <span class="material-symbols-outlined text-lg">note_add</span> Zabilježi
                                    </a>
                                @endif
                                <a href="{{ route('player.leagues.show', $competition) }}" class="flex-1 lg:flex-none justify-center px-5 py-2 rounded-lg bg-primary text-on-primary font-bold text-xs lg:text-[13px] hover:opacity-90 transition-opacity flex items-center gap-2">
                                    Pogledaj <span class="material-symbols-outlined text-lg">arrow_right_alt</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-surface-container-low border border-outline-variant rounded-xl p-8 lg:p-10 text-center">
                <p class="text-on-surface-variant">Još nisi dodan ni na jedno takmičenje.</p>
                <p class="text-sm mt-2 text-on-surface-variant opacity-70">Kada te organizator doda ili pozove na ligu, ona će se pojaviti ovdje.</p>
                <a href="{{ route('player.leagues.index') }}" class="inline-block mt-5 px-6 py-3 font-semibold rounded-full transition-all active:scale-95 bg-primary text-on-primary">
                    Pronađi ligu
                </a>
            </div>
        @endforelse
    </section>
</main>

<!-- Bottom Navigation (Mobile Only) -->
<nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-2 pb-[env(safe-area-inset-bottom)] bg-surface-container-high rounded-t-xl border-t border-outline-variant shadow-lg">
    <a href="{{ route('player.dashboard') }}" class="flex flex-col items-center justify-center bg-primary-container text-on-primary-container rounded-full px-4 py-1 active:scale-90 transition-transform">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="font-label-bold text-[10px]">Dashboard</span>
    </a>
    <a href="{{ route('player.dashboard.matches') }}" class="flex flex-col items-center justify-center text-on-surface-variant active:scale-90 transition-transform">
        <span class="material-symbols-outlined">sports_tennis</span>
        <span class="font-label-bold text-[10px]">Mečevi</span>
    </a>
    <a href="{{ route('player.leagues.index') }}" class="flex flex-col items-center justify-center text-on-surface-variant active:scale-90 transition-transform">
        <span class="material-symbols-outlined">emoji_events</span>
        <span class="font-label-bold text-[10px]">Takmičenja</span>
    </a>
    <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center text-on-surface-variant active:scale-90 transition-transform">
        <span class="material-symbols-outlined">person</span>
        <span class="font-label-bold text-[10px]">Nalog</span>
    </a>
</nav>

<x-pwa-install-prompt />

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }
</script>
</body>
</html>
