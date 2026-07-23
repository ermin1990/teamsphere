<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $match->homeTeam?->name ?? $match->homePlayer?->name ?? 'Domaći' }} vs {{ $match->awayTeam?->name ?? $match->awayPlayer?->name ?? 'Gost' }} - {{ $organization->name }}</title>

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
                    "surface-container-lowest": "var(--c-surface-container-lowest)", "surface-dim": "var(--c-surface-dim)", "surface": "var(--c-surface)",
                    "surface-container-low": "var(--c-surface-container-low)", "surface-container": "var(--c-surface-container)", "surface-container-high": "var(--c-surface-container-high)",
                    "surface-container-highest": "var(--c-surface-container-highest)", "surface-variant": "var(--c-surface-variant)", "surface-bright": "var(--c-surface-bright)",
                    "on-surface": "var(--c-on-surface)", "on-surface-variant": "var(--c-on-surface-variant)", "outline": "var(--c-outline)", "outline-variant": "var(--c-outline-variant)",
                    "primary": "var(--c-primary)", "primary-container": "var(--c-primary-container)", "on-primary": "var(--c-on-primary)", "on-primary-container": "var(--c-on-primary-container)",
                    "secondary": "var(--c-secondary)", "secondary-container": "var(--c-secondary-container)", "on-secondary-container": "var(--c-on-secondary-container)",
                    "tertiary-container": "var(--c-tertiary-container)", "on-tertiary-container": "var(--c-on-tertiary-container)", "error": "var(--c-error)", "primary-soft": "var(--c-primary-soft)", "error-soft": "var(--c-error-soft)", "secondary-soft": "var(--c-secondary-soft)",
                },
                borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
                spacing: { gutter: "24px", "margin-mobile": "16px", "sidebar-width": "260px", "container-max": "1280px" },
                fontFamily: {
                    display: ["Montserrat"], "headline-md": ["Montserrat"], "headline-lg-mobile": ["Montserrat"], "headline-lg": ["Montserrat"],
                    "body-md": ["Inter"], "body-sm": ["Inter"], "body-lg": ["Inter"], "label-bold": ["Inter"],
                },
                fontSize: {
                    display: ["40px", { lineHeight: "1.1", letterSpacing: "-0.02em", fontWeight: "800" }],
                    "headline-md": ["20px", { lineHeight: "1.3", fontWeight: "700" }],
                    "headline-lg-mobile": ["22px", { lineHeight: "1.2", fontWeight: "700" }],
                    "headline-lg": ["28px", { lineHeight: "1.2", letterSpacing: "-0.01em", fontWeight: "700" }],
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
    body { background-color: var(--c-surface-container-lowest); color: var(--c-on-surface); overflow-x: hidden; }
    .custom-scrollbar::-webkit-scrollbar { display: none; }
    .custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: var(--c-surface-dim); }
    ::-webkit-scrollbar-thumb { background: var(--c-surface-container-highest); border-radius: 4px; }
    .score-card { transition: border-color 0.3s ease, box-shadow 0.3s ease; }
    .score-card:hover { border-color: var(--c-primary); box-shadow: 0 0 15px rgba(87, 241, 219, 0.15); }
    @keyframes score-pop {
        0% { transform: scale(1.4); }
        60% { transform: scale(0.94); }
        100% { transform: scale(1); }
    }
    .score-pop { animation: score-pop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); }
</style>
@include('partials.theme-vars')
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
        <a class="flex items-center gap-3 px-4 py-3 text-primary border-l-4 border-primary bg-primary/5 font-label-bold rounded-r-lg" href="{{ route('competitions.index') }}">
            <span class="material-symbols-outlined">emoji_events</span> Takmičenja
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-variant/50 transition-colors duration-200 font-body-md rounded-lg" href="{{ route('venues.public.index') }}">
            <span class="material-symbols-outlined">location_on</span> Tereni
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface hover:bg-surface-variant/50 transition-colors duration-200 font-body-md rounded-lg" href="{{ route('competitions.organization', $organization) }}">
            <span class="material-symbols-outlined">corporate_fare</span> {{ \Illuminate\Support\Str::limit($organization->name, 18) }}
        </a>
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
            <a href="{{ route('competitions.show', $competition) }}" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high shrink-0">
                <span class="material-symbols-outlined text-primary">arrow_back</span>
            </a>
            <div class="min-w-0">
                <h1 class="font-headline-md text-on-surface truncate">Detalji meča</h1>
                <p class="text-xs text-primary uppercase tracking-wider truncate">{{ $competition->name }}</p>
            </div>
        </div>
    </div>
</header>

<!-- Desktop Top App Bar -->
<header class="hidden lg:flex justify-between items-center px-gutter w-[calc(100%-260px)] ml-[260px] h-16 fixed top-0 z-40 bg-surface border-b border-outline-variant">
    <nav class="flex gap-6">
        <a class="text-on-surface-variant hover:text-primary transition-all font-medium" href="{{ route('home') }}">Home</a>
        <a class="text-primary font-bold border-b-2 border-primary pb-1" href="{{ route('competitions.index') }}">Takmičenja</a>
    </nav>
</header>

<!-- Main Content Canvas -->
<main class="lg:ml-[260px] lg:mt-16 mt-0 p-margin-mobile lg:p-gutter min-h-screen">
    <div class="max-w-container-max mx-auto">

        <!-- Breadcrumbs (desktop) -->
        <nav class="hidden lg:flex items-center gap-2 text-on-surface-variant text-body-sm mb-6">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center"><span class="material-symbols-outlined text-sm">home</span></a>
            <span>/</span>
            <a href="{{ route('competitions.show', $competition) }}" class="hover:text-primary transition-colors">{{ $competition->name }}</a>
            <span>/</span>
            <span class="text-primary font-bold">Detalji meča</span>
        </nav>

        <!-- Match Status Banner (static meta - competition/round/table/referee) -->
        <div class="bg-surface-container border border-outline-variant rounded-xl p-6 lg:p-8 mb-4 lg:mb-6 flex flex-col items-center text-center">
            <p class="font-label-bold text-label-bold text-outline uppercase tracking-widest mb-2">{{ $competition->sport->name }} &bull; {{ $competition->name }}</p>
            <h1 class="font-headline-md lg:font-headline-lg text-headline-md lg:text-headline-lg text-on-surface mb-1">Detalji meča</h1>
            <p class="text-primary font-bold mb-2">Kolo {{ $match->round_number ?? $match->round }}</p>
            <div class="flex flex-wrap justify-center gap-2 mt-2">
                @if($match->table)
                    <div class="flex items-center gap-1.5 text-xs text-on-surface bg-surface-container-high px-3 py-1.5 rounded-lg">
                        <span class="material-symbols-outlined text-[16px] text-primary">table_bar</span>
                        <span>Sto: {{ $match->table->name }}</span>
                    </div>
                @endif
                @if($match->referee)
                    <div class="flex items-center gap-1.5 text-xs text-on-surface bg-surface-container-high px-3 py-1.5 rounded-lg">
                        <span class="material-symbols-outlined text-[16px] text-primary">sports</span>
                        <span>Sudija: {{ $match->referee->name }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Live Score Component (status, scores, sets - polls every 5s) -->
        @livewire('public-live-score', ['match' => $match])

        <!-- Venue & Time (static) -->
        @if($match->venue || $match->scheduled_at || $match->played_at)
        <div class="bg-surface-container border border-outline-variant rounded-xl p-6 grid grid-cols-1 md:grid-cols-3 gap-6 mt-4 lg:mt-6">
            @php $mDate = $match->played_at ?? $match->scheduled_at; @endphp
            @if($mDate)
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary">calendar_today</span>
                    <div>
                        <p class="text-label-bold text-outline">DATUM</p>
                        <p class="text-on-surface font-semibold">{{ $mDate->format('d.m.Y.') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary">schedule</span>
                    <div>
                        <p class="text-label-bold text-outline">VRIJEME</p>
                        <p class="text-on-surface font-semibold">{{ $mDate->format('H:i') }}</p>
                    </div>
                </div>
            @endif
            @if($match->venue)
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary">location_on</span>
                    <div>
                        <p class="text-label-bold text-outline">LOKACIJA</p>
                        <p class="text-on-surface font-semibold">{{ $match->venue->name }}</p>
                    </div>
                </div>
            @endif
        </div>
        @endif

        <!-- Actions -->
        <div class="mt-4 lg:mt-6 space-y-3 max-w-lg mx-auto lg:max-w-none lg:flex lg:space-y-0 lg:gap-4">
            <a href="{{ route('competitions.show', $competition) }}#standings-section" class="w-full lg:flex-1 bg-primary text-on-primary font-bold py-4 rounded-xl transition-transform active:scale-95 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">emoji_events</span> Poredak u ligi
            </a>
            <a href="{{ route('competitions.show', $competition) }}#schedule-section" class="w-full lg:flex-1 border border-primary text-primary font-bold py-4 rounded-xl transition-all active:bg-primary/10 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">history</span> Svi mečevi
            </a>
        </div>

    </div>
</main>

@include('public.leagues._bottom-nav')

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
