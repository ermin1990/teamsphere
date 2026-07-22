<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Moji mečevi - MojTurnir</title>

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
                    "surface-container-lowest": "var(--c-surface-container-lowest)",
                    "surface-dim": "var(--c-surface-dim)",
                    "surface": "var(--c-surface)",
                    "surface-container-low": "var(--c-surface-container-low)",
                    "surface-container": "var(--c-surface-container)",
                    "surface-container-high": "var(--c-surface-container-high)",
                    "surface-container-highest": "var(--c-surface-container-highest)",
                    "surface-variant": "var(--c-surface-variant)",
                    "surface-bright": "var(--c-surface-bright)",
                    "on-surface": "var(--c-on-surface)",
                    "on-surface-variant": "var(--c-on-surface-variant)",
                    "outline": "var(--c-outline)",
                    "outline-variant": "var(--c-outline-variant)",
                    "primary": "var(--c-primary)",
                    "primary-container": "var(--c-primary-container)",
                    "on-primary": "var(--c-on-primary)",
                    "on-primary-container": "var(--c-on-primary-container)",
                    "secondary": "var(--c-secondary)",
                    "secondary-container": "var(--c-secondary-container)",
                    "on-secondary-container": "var(--c-on-secondary-container)",
                    "tertiary-container": "var(--c-tertiary-container)",
                    "on-tertiary-container": "var(--c-on-tertiary-container)",
                    "error": "var(--c-error)", "primary-soft": "var(--c-primary-soft)", "error-soft": "var(--c-error-soft)", "secondary-soft": "var(--c-secondary-soft)",
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
    body { background-color: var(--c-surface-container-lowest); color: var(--c-on-surface); overflow-x: hidden; -webkit-tap-highlight-color: transparent; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: var(--c-surface-dim); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--c-surface-container-highest); border-radius: 10px; }
    .match-card:hover, .match-card:active { border-color: var(--c-primary); box-shadow: 0 0 15px rgba(45, 212, 191, 0.15); }
</style>
@include('partials.theme-vars')
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen pb-24 lg:pb-8">

@php
    $initials = mb_strtoupper(collect(preg_split('/\s+/', trim(auth()->user()->name ?? '')))->filter()->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')) ?: '?';
@endphp

<!-- Persistent SideNavBar (desktop) -->
<nav class="hidden lg:flex w-sidebar-width h-screen fixed left-0 top-0 border-r border-outline-variant bg-surface-container-low flex-col py-2 z-50">
    <div class="px-6 py-8">
        <a href="{{ route('player.dashboard') }}" class="font-display text-primary tracking-tighter text-2xl uppercase block">MojTurnir</a>
        <p class="font-label-bold text-on-surface-variant opacity-70 text-xs">Moje lige</p>
    </div>
    <div class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar">
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors duration-200 rounded-lg" href="{{ route('player.dashboard') }}">
            <span class="material-symbols-outlined">dashboard</span><span>Dashboard</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-primary border-l-4 border-primary bg-primary/5 font-label-bold" href="{{ route('player.dashboard.matches') }}">
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

    @php
        $singleCompetition = request()->filled('competition_id')
            ? $competitions->firstWhere('id', (int) request('competition_id'))
            : ($competitions->count() === 1 ? $competitions->first() : null);

        $newMatchCompetitions = \App\Models\Competition::whereHas('players', function ($q) {
                $q->where('players.user_id', auth()->id());
            })
            ->where('is_team_based', false)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    @endphp

    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 lg:gap-6 mb-6 lg:mb-8">
        <div>
            <h1 class="font-headline-lg-mobile lg:font-display text-headline-lg-mobile lg:text-headline-lg text-primary tracking-tight">Moji mečevi</h1>
            <p class="text-on-surface-variant text-sm lg:text-base mt-1">Svi tvoji mečevi u svim takmičenjima</p>
            @if($singleCompetition)
                <a href="{{ route('player.leagues.show', $singleCompetition) }}" class="inline-flex items-center gap-1 text-primary text-sm font-label-bold mt-2 hover:underline">
                    Pogledaj ligu <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            @endif
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

    <!-- Filters -->
    @if($seasons->count() > 1 || $competitions->count() > 1 || $rounds->count() > 1)
        <form method="GET" action="{{ route('player.dashboard.matches') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6 lg:mb-8">
            @if($seasons->count() > 1)
                <select name="season_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-surface-container-low border border-outline-variant text-on-surface focus:outline-none focus:border-primary transition-all">
                    <option value="">Sve sezone</option>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}" {{ (string) request('season_id') === (string) $season->id ? 'selected' : '' }}>{{ $season->name }}</option>
                    @endforeach
                </select>
            @endif
            @if($competitions->count() > 1)
                <select name="competition_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-surface-container-low border border-outline-variant text-on-surface focus:outline-none focus:border-primary transition-all">
                    <option value="">Sve lige</option>
                    @foreach($competitions as $competition)
                        <option value="{{ $competition->id }}" {{ (string) request('competition_id') === (string) $competition->id ? 'selected' : '' }}>{{ $competition->name }}</option>
                    @endforeach
                </select>
            @endif
            @if($rounds->count() > 1)
                <select name="round" onchange="this.form.submit()"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-surface-container-low border border-outline-variant text-on-surface focus:outline-none focus:border-primary transition-all">
                    <option value="">Sva kola</option>
                    @foreach($rounds as $round)
                        <option value="{{ $round }}" {{ (string) request('round') === (string) $round ? 'selected' : '' }}>Kolo {{ $round }}</option>
                    @endforeach
                </select>
            @endif
        </form>
    @endif

    <!-- Match List -->
    <div class="space-y-3">
        @forelse($matches as $match)
            @php
                $competition = $match->competition;
                $canEnterResult = $competition
                    && $competition->isLeague()
                    && !$competition->is_team_based
                    && $competition->status === 'active';

                $isTeamMatch = $competition && $competition->is_team_based && $competition->type === 'league';
                $homeName = $isTeamMatch
                    ? ($match->homeTeam?->name ?? 'Domaći')
                    : (($match->position_code === 'Dubl') ? 'Dubl' : ($match->homePlayer?->name ?? 'TBD'));
                $awayName = $isTeamMatch
                    ? ($match->awayTeam?->name ?? 'Gosti')
                    : (($match->position_code === 'Dubl') ? 'Dubl' : ($match->awayPlayer?->name ?? 'TBD'));

                $hs = $match->home_score ?? 0;
                $as = $match->away_score ?? 0;
                $completed = in_array($match->status, ['completed', 'forfeited']);
                $live = $match->status === 'in_progress';
                $scheduled = $match->status === 'scheduled';
                $homeWin = $completed && $hs > $as;
                $awayWin = $completed && $as > $hs;

                $sets = (!$isTeamMatch)
                    ? collect($match->sets ?? [])->map(fn ($s) => [
                        'h' => $s['home'] ?? $s['home_score'] ?? $s['p1'] ?? null,
                        'a' => $s['away'] ?? $s['away_score'] ?? $s['p2'] ?? null,
                    ])->filter(fn ($s) => !(($s['h'] === null || $s['h'] === '') && ($s['a'] === null || $s['a'] === '')))->values()
                    : collect();
                $maxPossibleSets = $competition ? max(1, (2 * ($competition->sets_to_win ?: 1)) - 1) : 1;
                $cellCount = max($maxPossibleSets, $sets->count());
            @endphp
            <div class="match-card relative overflow-hidden bg-surface-container-low border border-outline-variant rounded-xl transition-all">
                @if($completed)
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>
                @elseif($live)
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-secondary animate-pulse"></div>
                @endif
                @if($live)
                    <div class="px-4 pt-3">
                        <span class="inline-block bg-secondary-soft text-secondary text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wide animate-pulse">Uživo</span>
                    </div>
                @endif
                <div class="p-4">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <p class="font-label-bold text-[11px] text-on-surface-variant uppercase truncate">
                            {{ $competition->name ?? '' }}
                            @if($match->scheduled_at || $match->played_at) &middot; {{ optional($match->played_at ?? $match->scheduled_at)->format('d.m.Y. H:i') }} @endif
                        </p>
                        @if($scheduled)
                            <span class="bg-surface-container-highest text-secondary text-[10px] px-2 py-0.5 rounded border border-outline-variant shrink-0">Zakazano</span>
                        @endif
                    </div>
                    <div class="flex items-stretch gap-3">
                        <div class="flex-1 flex flex-col justify-between gap-2.5 min-w-0">
                            <div class="flex items-center justify-between gap-2 min-w-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full {{ $homeWin ? 'bg-primary' : 'bg-transparent' }}"></div>
                                    <div class="text-sm font-bold truncate {{ $homeWin ? 'text-on-surface' : ($awayWin ? 'text-on-surface-variant/50' : 'text-on-surface-variant') }}">{{ $homeName }}</div>
                                </div>
                                <div class="flex items-center gap-1 ml-auto">
                                    @for($i = 0; $i < $cellCount; $i++)
                                        @php $c = $sets[$i] ?? null; $winCell = $c && (int) ($c['h'] ?? 0) >= (int) ($c['a'] ?? 0); @endphp
                                        <span class="w-[16px] text-center text-[10px] font-black {{ $c && $c['h'] !== null && $c['h'] !== '' ? ($winCell ? 'text-primary' : 'text-on-surface-variant/50') : 'text-on-surface-variant/30' }}">{{ $c && $c['h'] !== null && $c['h'] !== '' ? $c['h'] : '-' }}</span>
                                    @endfor
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-2 min-w-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="flex-shrink-0 w-1.5 h-1.5 rounded-full {{ $awayWin ? 'bg-primary' : 'bg-transparent' }}"></div>
                                    <div class="text-sm font-bold truncate {{ $awayWin ? 'text-on-surface' : ($homeWin ? 'text-on-surface-variant/50' : 'text-on-surface-variant') }}">{{ $awayName }}</div>
                                </div>
                                <div class="flex items-center gap-1 ml-auto">
                                    @for($i = 0; $i < $cellCount; $i++)
                                        @php $c = $sets[$i] ?? null; $winCell = $c && (int) ($c['a'] ?? 0) >= (int) ($c['h'] ?? 0); @endphp
                                        <span class="w-[16px] text-center text-[10px] font-black {{ $c && $c['a'] !== null && $c['a'] !== '' ? ($winCell ? 'text-primary' : 'text-on-surface-variant/50') : 'text-on-surface-variant/30' }}">{{ $c && $c['a'] !== null && $c['a'] !== '' ? $c['a'] : '-' }}</span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-center justify-center gap-1.5 pl-3 border-l border-outline-variant min-w-[36px]">
                            <div class="w-7 h-7 rounded-md flex items-center justify-center {{ $homeWin ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface-variant' }}">
                                <span class="text-[13px] font-black italic">{{ $scheduled ? 0 : $hs }}</span>
                            </div>
                            <div class="w-7 h-7 rounded-md flex items-center justify-center {{ $awayWin ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface-variant' }}">
                                <span class="text-[13px] font-black italic">{{ $scheduled ? 0 : $as }}</span>
                            </div>
                        </div>
                    </div>
                    @if($match->venue)
                        <div class="flex items-center gap-1 mt-3 pt-3 border-t border-outline-variant text-[10px] text-on-surface-variant">
                            <span class="material-symbols-outlined text-xs">location_on</span> {{ $match->venue->name }}
                        </div>
                    @endif
                    @if($canEnterResult)
                        <div class="flex justify-end gap-4 mt-3 pt-3 border-t border-outline-variant">
                            @if($match->status !== 'completed')
                                <a href="{{ route('player.matches.live', $match) }}" class="text-secondary font-label-bold text-[11px] uppercase tracking-widest hover:opacity-80">Uživo</a>
                            @endif
                            <a href="{{ route('player.matches.result.edit', $match) }}" class="text-primary font-label-bold text-[11px] uppercase tracking-widest hover:opacity-80">{{ $match->status === 'completed' ? 'Uredi rezultat' : 'Upiši rezultat' }}</a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-surface-container-low border border-outline-variant rounded-xl p-8 lg:p-10 text-center">
                <p class="text-on-surface-variant">Još nemaš odigranih ili zakazanih mečeva.</p>
            </div>
        @endforelse
    </div>

    <div class="pt-6">{{ $matches->links() }}</div>
</main>

<!-- Bottom Navigation (Mobile Only) -->
@include('player.partials.bottom-nav')

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
