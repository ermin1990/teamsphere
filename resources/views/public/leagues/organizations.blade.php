<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Takmičenja - MojTurnir</title>

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
                    display: ["44px", { lineHeight: "1.1", letterSpacing: "-0.02em", fontWeight: "800" }],
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
    .card-glow:hover { box-shadow: 0 0 15px rgba(87, 241, 219, 0.2); border-color: var(--c-primary); }
    .custom-scrollbar::-webkit-scrollbar { display: none; }
    .custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: var(--c-surface-dim); }
    ::-webkit-scrollbar-thumb { background: var(--c-surface-container-highest); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--c-outline-variant); }
</style>
@include('partials.theme-vars')
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen pb-24 lg:pb-0">

@php
    $sportIcon = function ($sport) {
        $n = mb_strtolower($sport?->name ?? '');
        if (str_contains($n, 'fudbal')) return 'sports_soccer';
        if (str_contains($n, 'košark') || str_contains($n, 'kosark')) return 'sports_basketball';
        if (str_contains($n, 'odbojk')) return 'sports_volleyball';
        if (str_contains($n, 'padel') || str_contains($n, 'padel')) return 'sports_tennis';
        return 'sports_tennis';
    };
    $activeCity = $cities->firstWhere('id', (int) request('city_id'));
    $activeSport = $sports->firstWhere('id', (int) request('sport_id'));
    $activeType = request('type');
    $filterUrl = fn ($overrides) => route('competitions.index', array_filter(array_merge([
        'city_id' => request('city_id'), 'sport_id' => request('sport_id'), 'type' => request('type'), 'q' => request('q'), 'status' => $statusFilter,
    ], $overrides)));
@endphp

<!-- Persistent SideNavBar (desktop) -->
<nav class="hidden lg:flex w-sidebar-width h-screen fixed left-0 top-0 border-r border-outline-variant bg-surface-container-low flex-col py-2 z-50">
    <div class="px-6 py-8">
        <a href="{{ route('home') }}" class="font-display text-primary tracking-tighter text-2xl uppercase block">MojTurnir</a>
        <p class="font-label-bold text-on-surface-variant opacity-70 text-xs">Organizuj. Igraj. Pobijedi.</p>
    </div>
    <div class="flex-1 px-4 space-y-6 overflow-y-auto custom-scrollbar">
        <div class="space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors duration-200" href="{{ route('home') }}">
                <span class="material-symbols-outlined">dashboard</span><span>Početna</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-primary border-l-4 border-primary bg-primary/5 font-label-bold" href="{{ route('competitions.index') }}">
                <span class="material-symbols-outlined">emoji_events</span><span>Takmičenja</span>
            </a>
        </div>

        @if($cities->isNotEmpty())
        <div class="pt-4 border-t border-outline-variant/30">
            <span class="px-4 font-label-bold text-primary uppercase tracking-widest block mb-4">Gradovi</span>
            <div class="space-y-1 px-2">
                <a href="{{ $filterUrl(['city_id' => null]) }}" class="w-full text-left px-4 py-2 {{ !$activeCity ? 'text-primary' : 'text-on-surface-variant' }} hover:text-primary transition-colors text-sm flex items-center justify-between rounded-lg">
                    Svi gradovi @if(!$activeCity)<span class="w-2 h-2 rounded-full bg-primary"></span>@endif
                </a>
                @foreach($cities as $city)
                    <a href="{{ $filterUrl(['city_id' => $city->id]) }}" class="w-full text-left px-4 py-2 {{ $activeCity?->id === $city->id ? 'text-primary' : 'text-on-surface-variant' }} hover:text-primary transition-colors text-sm flex items-center justify-between rounded-lg">
                        {{ $city->name }} @if($activeCity?->id === $city->id)<span class="w-2 h-2 rounded-full bg-primary"></span>@endif
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($sports->isNotEmpty())
        <div class="pt-4 border-t border-outline-variant/30">
            <span class="px-4 font-label-bold text-primary uppercase tracking-widest block mb-4">Sportovi</span>
            <div class="grid grid-cols-2 gap-2 px-2">
                @foreach($sports as $sport)
                    <a href="{{ $filterUrl(['sport_id' => $activeSport?->id === $sport->id ? null : $sport->id]) }}"
                       class="flex flex-col items-center justify-center p-3 rounded-lg {{ $activeSport?->id === $sport->id ? 'bg-primary/10 border-primary/50' : 'bg-surface-container border-outline-variant/30' }} hover:bg-surface-variant transition-all border group">
                        <span class="material-symbols-outlined {{ $activeSport?->id === $sport->id ? 'text-primary' : 'text-on-surface-variant group-hover:text-primary' }} transition-colors">{{ $sportIcon($sport) }}</span>
                        <span class="text-[10px] mt-1 uppercase font-bold text-on-surface-variant truncate w-full text-center">{{ $sport->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="pt-4 border-t border-outline-variant/30">
            <span class="px-4 font-label-bold text-primary uppercase tracking-widest block mb-4">Tip</span>
            <div class="space-y-1 px-2">
                <a href="{{ $filterUrl(['type' => null]) }}" class="w-full text-left px-4 py-2 {{ !$activeType ? 'text-primary' : 'text-on-surface-variant' }} hover:text-primary transition-colors text-sm flex items-center justify-between rounded-lg">
                    Sve @if(!$activeType)<span class="w-2 h-2 rounded-full bg-primary"></span>@endif
                </a>
                <a href="{{ $filterUrl(['type' => 'league']) }}" class="w-full text-left px-4 py-2 {{ $activeType === 'league' ? 'text-primary' : 'text-on-surface-variant' }} hover:text-primary transition-colors text-sm flex items-center justify-between rounded-lg">
                    Lige @if($activeType === 'league')<span class="w-2 h-2 rounded-full bg-primary"></span>@endif
                </a>
                <a href="{{ $filterUrl(['type' => 'tournament']) }}" class="w-full text-left px-4 py-2 {{ $activeType === 'tournament' ? 'text-primary' : 'text-on-surface-variant' }} hover:text-primary transition-colors text-sm flex items-center justify-between rounded-lg">
                    Turniri @if($activeType === 'tournament')<span class="w-2 h-2 rounded-full bg-primary"></span>@endif
                </a>
            </div>
        </div>
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
    <div class="flex items-center gap-3">
        <button class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-surface-variant transition-colors" id="filter-trigger">
            <span class="material-symbols-outlined text-primary">menu</span>
        </button>
        <a href="{{ route('home') }}" class="font-headline-md text-on-surface tracking-tight">MojTurnir</a>
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
    <div class="flex items-center gap-6">
        <h2 class="font-headline-md text-on-surface font-bold">Takmičenja</h2>
        <nav class="hidden md:flex gap-6">
            <a class="text-primary font-bold border-b-2 border-primary pb-1" href="{{ route('competitions.index') }}">Sve</a>
        </nav>
    </div>
    <form method="GET" action="{{ route('competitions.index') }}" class="flex items-center gap-4">
        @if(request('city_id'))<input type="hidden" name="city_id" value="{{ request('city_id') }}">@endif
        @if(request('sport_id'))<input type="hidden" name="sport_id" value="{{ request('sport_id') }}">@endif
        <div class="relative hidden lg:block">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
            <input name="q" value="{{ request('q') }}" class="bg-surface-container-lowest border border-outline-variant rounded-full pl-10 pr-4 py-1.5 text-sm w-64 focus:border-primary focus:ring-0 focus:outline-none transition-all" placeholder="Pretraži igrače, timove, lige, organizacije..." type="text">
        </div>
    </form>
</header>

<!-- Main Content Canvas -->
<main class="lg:ml-[260px] lg:mt-16 mt-16 lg:mt-16 p-margin-mobile lg:p-gutter min-h-screen">
    <!-- Welcome Hero -->
    <section class="relative rounded-xl overflow-hidden mb-6 lg:mb-8 lg:h-40 flex items-center bg-surface-container-low border border-outline-variant p-6 lg:p-8">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="material-symbols-outlined text-primary text-3xl lg:text-4xl">emoji_events</span>
                <h2 class="font-display text-2xl lg:text-4xl font-extrabold text-primary tracking-tight">Takmičenja</h2>
            </div>
            <p class="text-on-surface-variant font-body-md lg:font-body-lg max-w-lg text-sm lg:text-base">Pregledaj aktivne lige i turnire, prati rezultate uživo i pronađi takmičenje u svom gradu.</p>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
    <div class="xl:col-span-9 min-w-0">

    <!-- Mobile: sport chips + search -->
    <section class="lg:hidden mb-6 space-y-4">
        <div class="relative">
            <form method="GET" action="{{ route('competitions.index') }}">
                @if(request('city_id'))<input type="hidden" name="city_id" value="{{ request('city_id') }}">@endif
                @if(request('sport_id'))<input type="hidden" name="sport_id" value="{{ request('sport_id') }}">@endif
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                <input name="q" value="{{ request('q') }}" class="w-full bg-surface-container-lowest border border-outline-variant rounded-full pl-10 pr-4 py-2.5 text-sm focus:border-primary focus:ring-0 focus:outline-none transition-all" placeholder="Pretraži igrače, timove, lige, organizacije..." type="text">
            </form>
        </div>
        @if($sports->isNotEmpty())
        <div class="overflow-x-auto custom-scrollbar -mx-margin-mobile px-margin-mobile flex gap-2">
            <a href="{{ $filterUrl(['sport_id' => null]) }}" class="whitespace-nowrap px-4 py-2 {{ !$activeSport ? 'bg-primary text-surface-container-lowest' : 'bg-surface-container-high text-on-surface border border-outline-variant' }} font-label-bold rounded-lg flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-[18px]">category</span> Sve
            </a>
            @foreach($sports as $sport)
                <a href="{{ $filterUrl(['sport_id' => $activeSport?->id === $sport->id ? null : $sport->id]) }}" class="whitespace-nowrap px-4 py-2 {{ $activeSport?->id === $sport->id ? 'bg-primary text-surface-container-lowest' : 'bg-surface-container-high text-on-surface border border-outline-variant' }} font-label-bold rounded-lg flex items-center gap-2 transition-all">
                    <span class="material-symbols-outlined text-[18px]">{{ $sportIcon($sport) }}</span> {{ $sport->name }}
                </a>
            @endforeach
        </div>
        @endif

        @if($cities->isNotEmpty())
        <div class="p-5 rounded-xl bg-surface-container border border-outline-variant space-y-4">
            <div class="flex items-center gap-2 text-secondary">
                <span class="material-symbols-outlined text-[20px]">location_on</span>
                <h2 class="font-label-bold uppercase tracking-widest">Pronađi ligu u svom gradu</h2>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ $filterUrl(['city_id' => null]) }}" class="px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-label-bold {{ !$activeCity ? 'text-primary' : 'text-on-surface-variant opacity-60' }}">Svi</a>
                @foreach($cities as $city)
                    <a href="{{ $filterUrl(['city_id' => $city->id]) }}" class="px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg font-label-bold {{ $activeCity?->id === $city->id ? 'text-primary' : 'text-on-surface-variant opacity-60' }}">{{ $city->name }}</a>
                @endforeach
            </div>
        </div>
        @endif
    </section>

    <!-- Desktop filter chips row -->
    <div class="hidden lg:flex items-center gap-4 mb-8 overflow-x-auto pb-2">
        <span class="material-symbols-outlined text-primary">location_on</span>
        <div class="flex gap-2">
            <a href="{{ $filterUrl(['city_id' => null]) }}" class="px-4 py-1.5 rounded-full {{ !$activeCity ? 'bg-primary-container text-on-primary-container' : 'border border-outline-variant text-on-surface-variant hover:border-primary' }} font-label-bold text-sm transition-all">Svi gradovi</a>
            @foreach($cities as $city)
                <a href="{{ $filterUrl(['city_id' => $city->id]) }}" class="px-4 py-1.5 rounded-full {{ $activeCity?->id === $city->id ? 'bg-primary-container text-on-primary-container' : 'border border-outline-variant text-on-surface-variant hover:border-primary' }} font-label-bold text-sm transition-all">{{ $city->name }}</a>
            @endforeach
        </div>
    </div>

    <!-- Direct player match(es) - jump straight to their profile -->
    @if($matchedPlayers->isNotEmpty())
        <section class="mb-8 lg:mb-10">
            <h2 class="font-headline-md mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">person</span> Igrači
            </h2>
            <div class="space-y-3">
                @foreach($matchedPlayers as $matchedPlayer)
                    <a href="{{ route('competitions.player.show', $matchedPlayer) }}" class="flex items-center gap-4 bg-surface-container-low border border-primary/30 rounded-xl p-4 card-glow transition-all group">
                        <div class="w-12 h-12 rounded-full bg-primary-soft border border-primary/40 flex items-center justify-center shrink-0">
                            <span class="font-display text-primary text-sm uppercase">{{ collect(explode(' ', $matchedPlayer->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate group-hover:text-primary transition-colors">{{ $matchedPlayer->name }}</p>
                            <p class="text-xs text-on-surface-variant truncate">
                                @if($matchedPlayer->organization)
                                    {{ $matchedPlayer->organization->name }}
                                    @if($matchedPlayer->leagues->isNotEmpty()) · @endif
                                @endif
                                {{ $matchedPlayer->leagues->pluck('name')->implode(', ') }}
                            </p>
                        </div>
                        <span class="text-primary flex items-center gap-1 text-xs font-label-bold shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            Profil <span class="material-symbols-outlined text-xs">chevron_right</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Takmičenja - compact list -->
    <section class="mb-10 lg:mb-12">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div class="flex bg-surface-container-low p-1 rounded-lg border border-outline-variant">
                <a href="{{ $filterUrl(['status' => 'active']) }}" class="px-4 py-1.5 rounded-md text-sm font-label-bold transition-all {{ $statusFilter === 'active' ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:text-on-surface' }}">Aktivne</a>
                <a href="{{ $filterUrl(['status' => 'uskoro']) }}" class="px-4 py-1.5 rounded-md text-sm font-label-bold transition-all {{ $statusFilter === 'uskoro' ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:text-on-surface' }}">Uskoro</a>
                <a href="{{ $filterUrl(['status' => 'zavrsene']) }}" class="px-4 py-1.5 rounded-md text-sm font-label-bold transition-all {{ $statusFilter === 'zavrsene' ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:text-on-surface' }}">Završene</a>
                <a href="{{ $filterUrl(['status' => 'sve']) }}" class="px-4 py-1.5 rounded-md text-sm font-label-bold transition-all {{ $statusFilter === 'sve' ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:text-on-surface' }}">Sve</a>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-on-surface-variant text-body-sm">{{ $competitionsCount }} {{ $competitionsCount === 1 ? 'takmičenje' : 'takmičenja' }}</span>
                @if($activeCity || $activeSport || request('q'))
                    <a href="{{ $filterUrl(['city_id' => null, 'sport_id' => null, 'q' => null]) }}" class="text-primary flex items-center gap-1 font-label-bold hover:gap-2 transition-all text-sm">
                        Ukloni filtere <span class="material-symbols-outlined text-sm">close</span>
                    </a>
                @endif
            </div>
        </div>

        @if($competitions->isNotEmpty())
        <div class="space-y-4">
            @foreach($competitions as $organizationName => $orgCompetitions)
                <div class="bg-surface-container-low rounded-xl border border-outline-variant overflow-hidden">
                    <a href="{{ route('competitions.organization', $orgCompetitions->first()->organization) }}" class="bg-surface-container-high px-4 py-2 border-b border-outline-variant flex items-center gap-3 hover:bg-surface-variant/50 transition-colors">
                        <span class="material-symbols-outlined text-primary text-lg">corporate_fare</span>
                        <h4 class="font-label-bold text-sm text-on-surface uppercase tracking-wider truncate">{{ $organizationName }}</h4>
                    </a>
                    <div class="divide-y divide-outline-variant/20">
                        @foreach($orgCompetitions as $competition)
                            @php
                                $cLive = $competition->status === 'in_progress';
                                $cCompleted = $competition->status === 'completed';
                                $cUpcomingOpen = !$cCompleted && !$cLive && $competition->registration_open
                                    && (!$competition->start_date || $competition->start_date->copy()->startOfDay()->gte(now()->startOfDay()))
                                    && (!$competition->registration_deadline || $competition->registration_deadline->isFuture());
                                $cCanApply = $cUpcomingOpen && !$competition->is_team_based;
                                $badge = $cCompleted ? 'Završeno' : ($cLive ? 'U toku' : ($cUpcomingOpen ? 'Prijave otvorene' : 'Aktivno'));
                            @endphp
                            <div class="flex items-center justify-between gap-3 px-4 py-3 hover:bg-surface-variant/30 transition-colors group">
                                <a href="{{ route('competitions.show', $competition) }}" class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1">
                                    <span class="material-symbols-outlined text-on-surface-variant text-lg shrink-0">{{ $sportIcon($competition->sport) }}</span>
                                    <span class="font-body-md text-sm lg:text-base text-on-surface truncate">{{ $competition->name }}</span>
                                    <span class="hidden md:inline text-xs text-on-surface-variant truncate shrink-0">{{ $competition->effectiveCity()?->name ?? '' }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase shrink-0 {{ $cCompleted ? 'bg-outline-variant/20 text-on-surface-variant' : ($cLive ? 'bg-primary/10 text-primary animate-pulse' : 'bg-secondary/10 text-secondary') }}">
                                        {{ $badge }}
                                    </span>
                                </a>
                                @if($cCanApply)
                                    <a href="{{ route('competitions.show', $competition) }}" class="shrink-0 px-3 py-1.5 rounded-full bg-primary text-on-primary text-xs font-label-bold hover:bg-primary-container transition-colors">
                                        Prijavi se
                                    </a>
                                @else
                                    <a href="{{ route('competitions.show', $competition) }}" class="text-primary opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-xs font-label-bold shrink-0">
                                        Pogledaj <span class="material-symbols-outlined text-xs">chevron_right</span>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16 bg-surface-container-low border border-outline-variant rounded-xl">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4 block">emoji_events</span>
            <h4 class="font-headline-md text-on-surface-variant mb-2">Nema takmičenja za odabrane filtere</h4>
            <p class="text-on-surface-variant text-sm">Pokušaj ukloniti filter grada, sporta ili pretrage.</p>
        </div>
        @endif
    </section>

    </div>

    <aside class="xl:col-span-3 space-y-6">
        @include('public.leagues._banners', ['placement' => \App\Models\Banner::PLACEMENT_TAKMICENJA])
    </aside>
    </div>

    <!-- Footer -->
    <div class="mt-10 text-center pb-6">
        <div class="inline-block px-4 py-2 rounded-full border text-xs border-outline-variant text-on-surface-variant">
            Želite li dodati vašu ligu? <a href="{{ route('register') }}" class="font-bold hover:text-primary transition-colors text-primary">Registrujte se ovdje</a>
        </div>
    </div>
</main>

@include('public.leagues._bottom-nav')

<!-- Mobile Filter Drawer -->
<div class="fixed inset-0 bg-black/60 z-[60] hidden backdrop-blur-sm transition-opacity opacity-0" id="drawer-overlay"></div>
<aside class="fixed top-0 left-0 h-full w-[280px] bg-surface-container-low z-[70] transition-transform -translate-x-full border-r border-outline-variant flex flex-col p-6" id="filter-drawer">
    <div class="flex items-center justify-between mb-8">
        <span class="font-headline-md text-primary">Filteri</span>
        <button class="p-2 hover:bg-surface-variant rounded-full transition-colors" id="drawer-close">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <form method="GET" action="{{ route('competitions.index') }}" class="space-y-6 flex-1 overflow-y-auto custom-scrollbar">
        @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
        <div class="space-y-3">
            <h4 class="font-label-bold text-on-surface-variant uppercase">Grad</h4>
            <select name="city_id" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface p-3 focus:ring-1 focus:ring-primary outline-none transition-all">
                <option value="">Svi gradovi</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ $activeCity?->id === $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-3">
            <h4 class="font-label-bold text-on-surface-variant uppercase">Sport</h4>
            <select name="sport_id" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface p-3 focus:ring-1 focus:ring-primary outline-none transition-all">
                <option value="">Svi sportovi</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}" {{ $activeSport?->id === $sport->id ? 'selected' : '' }}>{{ $sport->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-3">
            <h4 class="font-label-bold text-on-surface-variant uppercase">Tip</h4>
            <select name="type" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface p-3 focus:ring-1 focus:ring-primary outline-none transition-all">
                <option value="">Sve</option>
                <option value="league" {{ $activeType === 'league' ? 'selected' : '' }}>Lige</option>
                <option value="tournament" {{ $activeType === 'tournament' ? 'selected' : '' }}>Turniri</option>
            </select>
        </div>
        <button type="submit" class="w-full py-4 bg-primary text-surface-container-lowest font-headline-md rounded-xl mt-auto shadow-lg active:scale-95 transition-transform">
            Primijeni Filter
        </button>
    </form>
</aside>

<x-pwa-install-prompt />

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Filter drawer
        const drawer = document.getElementById('filter-drawer');
        const overlay = document.getElementById('drawer-overlay');
        const openDrawer = () => {
            drawer.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.add('opacity-100'), 10);
            document.body.style.overflow = 'hidden';
        };
        const closeDrawer = () => {
            drawer.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            setTimeout(() => overlay.classList.add('hidden'), 300);
            document.body.style.overflow = '';
        };
        document.getElementById('filter-trigger')?.addEventListener('click', openDrawer);
        document.getElementById('drawer-close')?.addEventListener('click', closeDrawer);
        overlay?.addEventListener('click', closeDrawer);

        // Schedule tabs
        const tabPast = document.getElementById('tab-past');
        const tabFuture = document.getElementById('tab-future');
        const panelPast = document.getElementById('panel-past');
        const panelFuture = document.getElementById('panel-future');

        tabPast?.addEventListener('click', () => {
            tabPast.classList.add('bg-primary', 'text-on-primary');
            tabPast.classList.remove('text-on-surface-variant');
            tabFuture.classList.remove('bg-primary', 'text-on-primary');
            tabFuture.classList.add('text-on-surface-variant');
            panelPast.classList.remove('hidden');
            panelFuture.classList.add('hidden');
        });
        tabFuture?.addEventListener('click', () => {
            tabFuture.classList.add('bg-primary', 'text-on-primary');
            tabFuture.classList.remove('text-on-surface-variant');
            tabPast.classList.remove('bg-primary', 'text-on-primary');
            tabPast.classList.add('text-on-surface-variant');
            panelFuture.classList.remove('hidden');
            panelPast.classList.add('hidden');
        });
    });
</script>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }
</script>
</body>
</html>
