<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $player->name }} - {{ $player->organization->name ?? 'MojTurnir' }}</title>

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
                    "surface-container-lowest": "#0b0e14", "surface-dim": "#10131a", "surface": "#10131a",
                    "surface-container-low": "#191c22", "surface-container": "#1d2026", "surface-container-high": "#272a31",
                    "surface-container-highest": "#32353c", "surface-variant": "#32353c", "surface-bright": "#363940",
                    "on-surface": "#e1e2eb", "on-surface-variant": "#bacac5", "outline": "#859490", "outline-variant": "#3c4a46",
                    "primary": "#57f1db", "primary-container": "#2dd4bf", "on-primary": "#003731", "on-primary-container": "#00574d",
                    "secondary": "#ffb95f", "secondary-container": "#ee9800", "on-secondary-container": "#5b3800",
                    "tertiary-container": "#b3bed5", "on-tertiary-container": "#424d61", "error": "#ffb4ab",
                },
                borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
                spacing: { gutter: "24px", "margin-mobile": "16px", "sidebar-width": "260px", "container-max": "1280px" },
                fontFamily: {
                    display: ["Montserrat"], "headline-md": ["Montserrat"], "headline-lg-mobile": ["Montserrat"], "headline-lg": ["Montserrat"],
                    "body-md": ["Inter"], "body-sm": ["Inter"], "body-lg": ["Inter"], "label-bold": ["Inter"],
                },
                fontSize: {
                    display: ["40px", { lineHeight: "1.1", letterSpacing: "-0.02em", fontWeight: "800" }],
                    "headline-lg": ["28px", { lineHeight: "1.2", letterSpacing: "-0.01em", fontWeight: "700" }],
                    "headline-md": ["20px", { lineHeight: "1.3", fontWeight: "700" }],
                    "headline-lg-mobile": ["22px", { lineHeight: "1.2", fontWeight: "700" }],
                    "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                    "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                    "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                    "label-bold": ["12px", { lineHeight: "1.2", letterSpacing: "0.05em", fontWeight: "600" }],
                },
            },
        },
    }
</script>
<style>
    html { scroll-behavior: smooth; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
    body { background-color: #0b0e14; color: #e1e2eb; overflow-x: hidden; }
    .custom-scrollbar::-webkit-scrollbar { display: none; }
    .custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #10131a; }
    ::-webkit-scrollbar-thumb { background: #32353c; border-radius: 4px; }
    .hero-banner {
        background:
            radial-gradient(circle at 15% 20%, rgba(87, 241, 219, 0.16) 0%, transparent 45%),
            radial-gradient(circle at 85% 0%, rgba(255, 185, 95, 0.10) 0%, transparent 40%),
            linear-gradient(135deg, #191c22 0%, #10131a 100%);
    }
    .card-glow:hover { box-shadow: 0 0 18px rgba(87, 241, 219, 0.15); border-color: rgba(87, 241, 219, 0.4); }
</style>
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen pb-24 lg:pb-8">

<!-- Persistent Left Sidebar (desktop) -->
<aside class="hidden lg:flex w-sidebar-width h-screen fixed left-0 top-0 border-r border-outline-variant bg-surface-container-low flex-col py-2 z-50">
    <div class="px-6 py-8">
        <a href="{{ route('home') }}" class="font-display text-primary tracking-tighter text-2xl uppercase block">MojTurnir</a>
        <p class="font-label-bold text-on-surface-variant opacity-70 text-xs">Organizuj. Igraj. Pobijedi.</p>
    </div>
    <nav class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar">
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-variant/50 transition-colors duration-200 font-body-md rounded-lg" href="{{ route('home') }}">
            <span class="material-symbols-outlined">dashboard</span> Početna
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-variant/50 transition-colors duration-200 font-body-md rounded-lg" href="{{ route('competitions.index') }}">
            <span class="material-symbols-outlined">emoji_events</span> Takmičenja
        </a>
        @if($player->organization)
            <a class="flex items-center gap-3 px-4 py-3 text-primary border-l-4 border-primary bg-primary/5 font-label-bold rounded-r-lg" href="{{ route('competitions.organization', $player->organization) }}">
                <span class="material-symbols-outlined">corporate_fare</span> {{ \Illuminate\Support\Str::limit($player->organization->name, 18) }}
            </a>
        @endif
    </nav>
    <div class="px-4 py-6 border-t border-outline-variant space-y-1">
        @auth
            <a class="flex items-center gap-3 px-4 py-2 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors rounded-lg" href="{{ auth()->user()->isOrganizerOrStaff() ? route('dashboard') : route('player.dashboard') }}">
                <span class="material-symbols-outlined">account_circle</span> Moj nalog
            </a>
        @else
            <a class="flex items-center gap-3 px-4 py-2 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors rounded-lg" href="{{ route('login') }}">
                <span class="material-symbols-outlined">login</span> Prijava
            </a>
        @endauth
    </div>
</aside>

<!-- Mobile sticky header -->
<header class="lg:hidden sticky top-0 z-40 bg-surface/90 backdrop-blur-md border-b border-outline-variant px-4 py-3">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 min-w-0">
            <a href="javascript:history.back()" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high shrink-0">
                <span class="material-symbols-outlined text-primary">arrow_back</span>
            </a>
            <div class="min-w-0">
                <h1 class="font-headline-md text-on-surface truncate">{{ $player->name }}</h1>
                @if($player->organization)
                    <p class="text-xs text-primary uppercase tracking-wider truncate">{{ $player->organization->name }}</p>
                @endif
            </div>
        </div>
    </div>
</header>

<!-- Desktop Top App Bar -->
<header class="hidden lg:flex justify-between items-center px-gutter w-[calc(100%-260px)] ml-[260px] h-16 fixed top-0 z-40 bg-surface border-b border-outline-variant">
    <nav class="flex gap-6">
        <a class="text-on-surface-variant hover:text-primary transition-all font-medium" href="{{ route('home') }}">Home</a>
        <a class="text-on-surface-variant hover:text-primary transition-all font-medium" href="{{ route('competitions.index') }}">Takmičenja</a>
    </nav>
</header>

<!-- Main Content Canvas -->
<main class="lg:ml-[260px] lg:mt-16 mt-0 p-margin-mobile lg:p-gutter min-h-screen">
    <div class="max-w-container-max mx-auto">

        @php
            $initials = collect(explode(' ', $player->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
            $setDominance = ($stats['setsWon'] + $stats['setsLost']) > 0
                ? round($stats['setsWon'] / ($stats['setsWon'] + $stats['setsLost']) * 100)
                : null;
        @endphp

        <!-- Hero banner -->
        <section class="-mx-margin-mobile lg:mx-0 mb-6 lg:mb-10 hero-banner border-y lg:border border-outline-variant lg:rounded-2xl relative overflow-hidden">
            <div class="relative z-10 px-margin-mobile lg:px-8 py-8 lg:py-10 flex flex-col sm:flex-row items-center sm:items-end gap-5">
                <div class="w-24 h-24 lg:w-28 lg:h-28 rounded-2xl bg-primary/15 border-2 border-primary/40 flex items-center justify-center shrink-0">
                    <span class="font-display text-primary text-2xl lg:text-3xl uppercase">{{ $initials }}</span>
                </div>
                <div class="min-w-0 text-center sm:text-left">
                    <h1 class="font-display text-2xl lg:text-headline-lg mb-2 truncate">{{ $player->name }}</h1>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        @if($player->organization)
                            <a href="{{ route('competitions.organization', $player->organization) }}" class="bg-surface-container-highest text-on-surface-variant hover:text-primary transition-colors px-3 py-1 rounded-full text-label-bold uppercase flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">group</span> {{ $player->organization->name }}
                            </a>
                        @endif
                        @if($stats['played'] > 0)
                            <span class="bg-primary/15 text-primary px-3 py-1 rounded-full text-label-bold uppercase">{{ $stats['winRate'] }}% pobjeda</span>
                        @endif
                        @if($stats['streakType'] && $stats['streakCount'] >= 2)
                            <span class="bg-secondary/15 text-secondary px-3 py-1 rounded-full text-label-bold uppercase flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">local_fire_department</span>
                                {{ $stats['streakType'] === 'W' ? 'Niz pobjeda' : 'Niz poraza' }}: {{ $stats['streakCount'] }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats bento grid -->
        <section class="grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-4 mb-6 lg:mb-10">
            <div class="bg-surface-container-low border border-outline-variant rounded-xl p-5 card-glow transition-all">
                <p class="text-on-surface-variant text-xs uppercase tracking-wide mb-2">Odigrano</p>
                <p class="font-display text-3xl lg:text-4xl text-on-surface">{{ $stats['played'] }}</p>
            </div>
            <div class="bg-surface-container-low border border-outline-variant rounded-xl p-5 card-glow transition-all">
                <p class="text-on-surface-variant text-xs uppercase tracking-wide mb-2">Pobjede</p>
                <p class="font-display text-3xl lg:text-4xl text-primary">{{ $stats['won'] }}</p>
            </div>
            <div class="bg-surface-container-low border border-outline-variant rounded-xl p-5 card-glow transition-all">
                <p class="text-on-surface-variant text-xs uppercase tracking-wide mb-2">Porazi</p>
                <p class="font-display text-3xl lg:text-4xl text-error">{{ $stats['lost'] }}</p>
            </div>
            <div class="bg-surface-container-low border border-outline-variant rounded-xl p-5 card-glow transition-all relative overflow-hidden">
                <p class="text-on-surface-variant text-xs uppercase tracking-wide mb-2">Win Rate</p>
                <p class="font-display text-3xl lg:text-4xl text-primary">{{ $stats['played'] > 0 ? $stats['winRate'] . '%' : '–' }}</p>
                @if($stats['played'] > 0)
                    <div class="w-full bg-surface-container-highest h-1 rounded-full mt-3">
                        <div class="bg-primary h-full rounded-full" style="width: {{ $stats['winRate'] }}%"></div>
                    </div>
                @endif
            </div>
            <div class="bg-surface-container-low border border-outline-variant rounded-xl p-5 card-glow transition-all col-span-2 lg:col-span-1">
                <p class="text-on-surface-variant text-xs uppercase tracking-wide mb-2">Setovi (O:I)</p>
                <p class="font-display text-3xl lg:text-4xl text-on-surface tabular-nums">{{ $stats['setsWon'] }}:{{ $stats['setsLost'] }}</p>
                @if($setDominance !== null)
                    <div class="w-full bg-surface-container-highest h-1 rounded-full mt-3">
                        <div class="bg-secondary h-full rounded-full" style="width: {{ $setDominance }}%"></div>
                    </div>
                @endif
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <!-- Leagues -->
            <section>
                <h2 class="font-headline-md mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">emoji_events</span> Lige
                </h2>
                @if($leagues->isEmpty())
                    <div class="bg-surface-container-low border border-outline-variant rounded-xl p-6 text-center text-on-surface-variant text-sm">
                        Igrač trenutno ne učestvuje u javnim ligama.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($leagues as $league)
                            @php $standing = $standings->get($league->id); @endphp
                            <a href="{{ route('competitions.show', $league) }}" class="flex items-center gap-4 bg-surface-container-low border border-outline-variant rounded-xl p-4 card-glow transition-all group">
                                <div class="w-14 h-14 rounded-lg bg-surface-container-highest border border-outline-variant flex items-center justify-center shrink-0 group-hover:border-primary/50 transition-colors">
                                    <span class="material-symbols-outlined text-primary text-2xl">emoji_events</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold truncate group-hover:text-primary transition-colors">{{ $league->name }}</p>
                                    <p class="text-xs text-on-surface-variant truncate">{{ $league->season->name ?? '' }}</p>
                                </div>
                                @if($standing)
                                    <div class="text-right shrink-0">
                                        <p class="font-display text-primary text-lg">#{{ $standing->position }}</p>
                                        <p class="text-[10px] text-on-surface-variant uppercase tracking-wide">{{ $standing->points }} bod.</p>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- Recent matches -->
            <section>
                <h2 class="font-headline-md mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">history</span> Zadnji mečevi
                </h2>
                @if($recentMatches->isEmpty())
                    <div class="bg-surface-container-low border border-outline-variant rounded-xl p-6 text-center text-on-surface-variant text-sm">
                        Nema odigranih mečeva.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentMatches as $match)
                            @php
                                $isHome = $match->home_player_id === $player->id;
                                $opponent = $isHome ? $match->awayPlayer : $match->homePlayer;
                                $playerScore = $isHome ? $match->home_score : $match->away_score;
                                $opponentScore = $isHome ? $match->away_score : $match->home_score;
                                $forfeitedByPlayer = $match->forfeited_by === ($isHome ? 'home' : 'away');
                                $isWin = !$forfeitedByPlayer && $playerScore > $opponentScore;
                                $opponentInitials = $opponent ? collect(explode(' ', $opponent->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') : '?';
                            @endphp
                            @unless($forfeitedByPlayer)
                                <div class="bg-surface-container-low border border-outline-variant rounded-xl p-4 flex items-center gap-4 card-glow transition-all group">
                                    <div class="w-10 h-10 rounded-full bg-surface-container-highest border border-outline-variant flex items-center justify-center shrink-0">
                                        <span class="font-label-bold text-on-surface-variant text-xs uppercase">{{ $opponentInitials }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold truncate">
                                            @if($opponent)
                                                <a href="{{ route('competitions.player.show', $opponent) }}" class="hover:text-primary transition-colors">{{ $opponent->name }}</a>
                                            @else
                                                Nepoznat igrač
                                            @endif
                                        </p>
                                        <p class="text-xs text-on-surface-variant truncate">{{ $match->competition->name ?? '' }}{{ $match->round ? ' · Kolo ' . $match->round : '' }}</p>
                                    </div>
                                    <div class="text-center shrink-0 hidden sm:block">
                                        <p class="font-display text-lg tabular-nums">{{ $playerScore }}:{{ $opponentScore }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0 {{ $isWin ? 'bg-primary/15 text-primary' : 'bg-error/15 text-error' }}">
                                        {{ $isWin ? 'Pobjeda' : 'Poraz' }}
                                    </span>
                                    @if($match->forfeited_by)
                                        <span class="px-1.5 py-0.5 rounded bg-secondary/20 text-secondary text-[10px] font-bold uppercase shrink-0">WO</span>
                                    @endif
                                </div>
                            @endunless
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

    </div>
</main>

</body>
</html>
