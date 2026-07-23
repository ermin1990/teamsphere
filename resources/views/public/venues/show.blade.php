<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $venue->name }} - MojTurnir</title>

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
                spacing: { gutter: "24px", "margin-mobile": "16px", "sidebar-width": "260px" },
                fontFamily: {
                    "headline-md": ["Montserrat"], "body-sm": ["Inter"], display: ["Montserrat"],
                    "headline-lg-mobile": ["Montserrat"], "body-md": ["Inter"], "body-lg": ["Inter"],
                    "label-bold": ["Inter"], "headline-lg": ["Montserrat"],
                },
                fontSize: {
                    "headline-md": ["20px", { lineHeight: "1.3", fontWeight: "700" }],
                    "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                    display: ["36px", { lineHeight: "1.1", letterSpacing: "-0.02em", fontWeight: "800" }],
                    "headline-lg-mobile": ["22px", { lineHeight: "1.2", fontWeight: "700" }],
                    "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                    "label-bold": ["12px", { lineHeight: "1.2", letterSpacing: "0.05em", fontWeight: "600" }],
                    "headline-lg": ["28px", { lineHeight: "1.2", letterSpacing: "-0.01em", fontWeight: "700" }],
                },
            },
        },
    }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    body { background-color: var(--c-surface-container-lowest); color: var(--c-on-surface); overflow-x: hidden; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: var(--c-surface-dim); }
    ::-webkit-scrollbar-thumb { background: var(--c-surface-container-highest); border-radius: 4px; }
</style>
@include('partials.theme-vars')
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen pb-24 lg:pb-8">

<!-- Persistent SideNavBar (desktop) -->
<nav class="hidden lg:flex w-sidebar-width h-screen fixed left-0 top-0 border-r border-outline-variant bg-surface-container-low flex-col py-2 z-50">
    <div class="px-6 py-8">
        <a href="{{ route('home') }}" class="font-display text-primary tracking-tighter text-2xl uppercase block">MojTurnir</a>
        <p class="font-label-bold text-on-surface-variant opacity-70 text-xs">Organizuj. Igraj. Pobijedi.</p>
    </div>
    <div class="flex-1 px-4 space-y-1 overflow-y-auto">
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors duration-200 rounded-lg" href="{{ route('home') }}">
            <span class="material-symbols-outlined">dashboard</span><span>Početna</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors duration-200 rounded-lg" href="{{ route('competitions.index') }}">
            <span class="material-symbols-outlined">emoji_events</span><span>Takmičenja</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-primary border-l-4 border-primary bg-primary/5 font-label-bold" href="{{ route('venues.public.show', $venue) }}">
            <span class="material-symbols-outlined">location_on</span><span class="truncate">{{ \Illuminate\Support\Str::limit($venue->name, 18) }}</span>
        </a>
    </div>
    <div class="p-4 space-y-1">
        @auth
            <a class="flex items-center gap-3 px-4 py-2 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors rounded-lg" href="{{ auth()->user()->isOrganizerOrStaff() ? route('dashboard') : route('player.dashboard') }}">
                <span class="material-symbols-outlined">account_circle</span><span>Moj nalog</span>
            </a>
        @else
            <a class="flex items-center gap-3 px-4 py-2 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors rounded-lg" href="{{ route('login') }}">
                <span class="material-symbols-outlined">login</span><span>Prijava</span>
            </a>
        @endauth
    </div>
</nav>

<!-- Mobile Top App Bar -->
<header class="lg:hidden fixed top-0 left-0 w-full h-16 bg-surface-container-lowest/80 backdrop-blur-md z-40 px-margin-mobile flex items-center justify-between border-b border-outline-variant/30">
    <div class="flex items-center gap-3 min-w-0">
        <a href="{{ route('competitions.index') }}" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high shrink-0">
            <span class="material-symbols-outlined text-primary">arrow_back</span>
        </a>
        <span class="font-headline-md text-on-surface truncate">{{ $venue->name }}</span>
    </div>
    <div class="flex items-center gap-2">
        @auth
            <a href="{{ auth()->user()->isOrganizerOrStaff() ? route('dashboard') : route('player.dashboard') }}" class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center border border-primary/20">
                <span class="material-symbols-outlined text-on-primary-container text-[20px]">person</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-surface-variant transition-colors">
                <span class="material-symbols-outlined text-on-surface-variant">login</span>
            </a>
        @endauth
    </div>
</header>

<!-- TopAppBar (desktop) -->
<header class="hidden lg:flex justify-between items-center px-gutter w-[calc(100%-260px)] ml-[260px] h-16 fixed top-0 z-40 bg-surface border-b border-outline-variant">
    <nav class="flex gap-6">
        <a class="text-on-surface-variant hover:text-primary transition-all font-medium" href="{{ route('home') }}">Home</a>
        <a class="text-on-surface-variant hover:text-primary transition-all font-medium" href="{{ route('competitions.index') }}">Takmičenja</a>
        <a class="text-primary font-bold border-b-2 border-primary pb-1" href="{{ route('venues.public.show', $venue) }}">Tereni</a>
    </nav>
</header>

<!-- Main Content Canvas -->
<main class="lg:ml-[260px] lg:mt-16 mt-16 p-margin-mobile lg:p-gutter min-h-screen">

    <!-- Hero -->
    <section class="-mx-margin-mobile lg:mx-0 mb-6 lg:mb-8 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl overflow-hidden">
        <div class="px-margin-mobile py-5 lg:p-8 flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-6">
            @if($venue->logoSrc())
                <img src="{{ $venue->logoSrc() }}"
                     alt="{{ $venue->name }}"
                     class="w-16 h-16 lg:w-20 lg:h-20 rounded-xl object-contain bg-surface-container-lowest border border-outline-variant shrink-0">
            @else
                <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-xl bg-surface-container-highest border border-outline-variant flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-3xl text-on-surface-variant">location_on</span>
                </div>
            @endif
            <div class="min-w-0">
                <h1 class="font-display text-2xl lg:text-display text-on-surface truncate">{{ $venue->name }}</h1>
                @if($venue->city || $venue->address)
                    <p class="text-on-surface-variant text-sm lg:text-base flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">location_on</span>
                        {{ collect([$venue->address, $venue->city?->name])->filter()->implode(', ') }}
                    </p>
                @endif
                @if($venue->description)
                    <p class="mt-2 text-sm text-on-surface-variant">{{ Str::limit($venue->description, 220) }}</p>
                @endif
                @if($venue->phone || $venue->website)
                    <div class="flex flex-wrap gap-4 mt-3 text-sm text-on-surface-variant">
                        @if($venue->phone)
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">call</span>{{ $venue->phone }}</span>
                        @endif
                        @if($venue->website)
                            <a href="{{ $venue->website }}" target="_blank" rel="noopener" class="flex items-center gap-1 text-primary hover:underline"><span class="material-symbols-outlined text-[16px]">language</span>{{ $venue->website }}</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Lige i turniri odigrani na ovom terenu -->
    <section class="mb-6 lg:mb-8">
        <h2 class="font-headline-lg-mobile lg:font-headline-lg text-headline-lg-mobile lg:text-headline-lg text-on-surface mb-4">Lige i turniri</h2>

        @if($competitions->isEmpty())
            <div class="-mx-margin-mobile lg:mx-0 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl px-margin-mobile py-8 text-center">
                <p class="text-on-surface-variant text-sm">Za sada nema javnih takmičenja odigranih na ovom terenu.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($competitions as $competition)
                    <a href="{{ route('competitions.show', $competition) }}"
                       class="-mx-margin-mobile lg:mx-0 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl px-margin-mobile py-4 lg:p-5 flex flex-col gap-2 hover:border-primary transition-colors">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-headline-md text-on-surface truncate">{{ $competition->name }}</h3>
                            <span class="shrink-0 text-[10px] font-label-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $competition->status === 'in_progress' ? 'bg-secondary/10 text-secondary border border-secondary/20' : 'bg-primary/10 text-primary border border-primary/20' }}">
                                {{ $competition->isLeague() ? 'Liga' : 'Turnir' }}
                            </span>
                        </div>
                        @if($competition->organization)
                            <p class="text-xs text-on-surface-variant truncate">{{ $competition->organization->name }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Zadnji mečevi na ovom terenu -->
    <section>
        <h2 class="font-headline-lg-mobile lg:font-headline-lg text-headline-lg-mobile lg:text-headline-lg text-on-surface mb-4">Zadnji mečevi</h2>

        @if($recentMatches->isEmpty())
            <div class="-mx-margin-mobile lg:mx-0 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl px-margin-mobile py-8 text-center">
                <p class="text-on-surface-variant text-sm">Još nema odigranih mečeva na ovom terenu.</p>
            </div>
        @else
            <div class="-mx-margin-mobile lg:mx-0 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl divide-y divide-outline-variant">
                @foreach($recentMatches as $match)
                    @php
                        $homeName = $match->homeTeam?->name ?? $match->homePlayer?->name ?? '—';
                        $awayName = $match->awayTeam?->name ?? $match->awayPlayer?->name ?? '—';
                        $homeWon = $match->status === 'completed' && $match->home_score > $match->away_score;
                        $awayWon = $match->status === 'completed' && $match->away_score > $match->home_score;
                    @endphp
                    <div class="px-margin-mobile lg:px-5 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs text-on-surface-variant truncate">{{ $match->competition?->name }}</p>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="{{ $homeWon ? 'text-primary font-bold' : 'text-on-surface' }} truncate">{{ $homeName }}</span>
                                <span class="text-on-surface-variant text-xs">vs</span>
                                <span class="{{ $awayWon ? 'text-primary font-bold' : 'text-on-surface' }} truncate">{{ $awayName }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            @if($match->status === 'completed')
                                <span class="font-display text-sm text-on-surface">{{ $match->home_score }}:{{ $match->away_score }}</span>
                            @else
                                <span class="text-[10px] font-label-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-secondary/10 text-secondary border border-secondary/20">
                                    {{ $match->scheduled_at?->format('d.m. H:i') ?? 'Zakazano' }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</main>

<!-- Mobile Bottom Navigation -->
<nav class="lg:hidden fixed bottom-0 w-full z-50 bg-surface-container-high rounded-t-xl border-t border-outline-variant flex justify-around items-center h-16 px-4 pb-[env(safe-area-inset-bottom)]">
    <a class="flex flex-col items-center justify-center text-on-surface-variant" href="{{ route('home') }}">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="text-[10px] font-label-bold">Početna</span>
    </a>
    <a class="flex flex-col items-center justify-center text-on-surface-variant" href="{{ route('competitions.index') }}">
        <span class="material-symbols-outlined">emoji_events</span>
        <span class="text-[10px] font-label-bold">Takmičenja</span>
    </a>
    <a class="flex flex-col items-center justify-center bg-primary-container text-on-primary-container rounded-full px-4 py-1" href="{{ route('venues.public.show', $venue) }}">
        <span class="material-symbols-outlined">location_on</span>
        <span class="text-[10px] font-label-bold">Teren</span>
    </a>
</nav>

</body>
</html>
