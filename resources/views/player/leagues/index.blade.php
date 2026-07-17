<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Takmičenja - MojTurnir</title>

<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#0b0e14">
<link rel="apple-touch-icon" href="/icons/icon-192.svg">
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
    .comp-card:hover { border-color: #57f1db; box-shadow: 0 0 15px rgba(45, 212, 191, 0.15); }
</style>
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
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors duration-200 rounded-lg" href="{{ route('player.dashboard.matches') }}">
            <span class="material-symbols-outlined">sports_tennis</span><span>Moji mečevi</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-primary border-l-4 border-primary bg-primary/5 font-label-bold" href="{{ route('player.leagues.index') }}">
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
    <div class="mb-6 lg:mb-8">
        <h1 class="font-headline-lg-mobile lg:font-display text-headline-lg-mobile lg:text-headline-lg text-primary tracking-tight">Takmičenja</h1>
        <p class="text-on-surface-variant text-sm lg:text-base mt-1">Pretraži otvorene lige i prijavi se za učešće</p>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('player.leagues.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6 lg:mb-8">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Pretraži po nazivu..."
               class="md:col-span-2 w-full px-4 py-3 rounded-xl text-sm bg-surface-container-low border border-outline-variant text-on-surface placeholder:text-on-surface-variant/60 focus:outline-none focus:border-primary transition-all">
        <select name="sport_id" onchange="this.form.submit()"
                class="w-full px-4 py-3 rounded-xl text-sm bg-surface-container-low border border-outline-variant text-on-surface focus:outline-none focus:border-primary transition-all">
            <option value="">Svi sportovi</option>
            @foreach($sports as $sport)
                <option value="{{ $sport->id }}" {{ (string) request('sport_id') === (string) $sport->id ? 'selected' : '' }}>{{ $sport->name }}</option>
            @endforeach
        </select>
        <select name="city_id" onchange="this.form.submit()"
                class="w-full px-4 py-3 rounded-xl text-sm bg-surface-container-low border border-outline-variant text-on-surface focus:outline-none focus:border-primary transition-all">
            <option value="">Svi gradovi</option>
            @foreach($cities as $city)
                <option value="{{ $city->id }}" {{ (string) request('city_id') === (string) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="md:hidden px-4 py-2.5 text-sm font-semibold rounded-xl transition-all active:scale-95 bg-primary text-on-primary">
            Pretraži
        </button>
    </form>

    <!-- Competitions List -->
    <div class="space-y-4">
        @forelse($competitions as $competition)
            <div class="comp-card bg-surface-container-low border border-outline-variant rounded-xl p-5 space-y-3 transition-all">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="font-bold text-lg text-on-surface truncate">{{ $competition->name }}</p>
                        <p class="text-xs mt-1 text-on-surface-variant truncate">
                            {{ $competition->organization->name }}
                            @if($competition->sport) &middot; {{ $competition->sport->name }} @endif
                            @if($competition->city) &middot; {{ $competition->city->name }} @endif
                            @if($competition->season) &middot; {{ $competition->season->name }} @endif
                        </p>
                    </div>
                    <div class="shrink-0">
                        @if($memberCompetitionIds->contains($competition->id))
                            <span class="px-3 py-1.5 text-xs rounded-full font-semibold bg-primary/15 text-primary">Već si član</span>
                        @elseif($pendingCompetitionIds->contains($competition->id))
                            <span class="px-3 py-1.5 text-xs rounded-full font-semibold bg-secondary/15 text-secondary">Zahtjev poslan</span>
                        @elseif($competition->is_team_based)
                            <span class="px-3 py-1.5 text-xs rounded-full font-semibold bg-surface-container-highest text-on-surface-variant">Timsko takmičenje</span>
                        @else
                            <form method="POST" action="{{ route('player.leagues.apply', $competition) }}" class="apply-form">
                                @csrf
                                <button type="submit" class="apply-button w-full sm:w-auto px-5 py-2.5 text-sm font-semibold rounded-full transition-all active:scale-95 whitespace-nowrap bg-primary text-on-primary inline-flex items-center justify-center gap-2 disabled:opacity-70">
                                    <span class="apply-button-text">Prijavi se</span>
                                    <svg class="apply-button-spinner hidden animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($competition->description)
                    <p class="text-sm text-on-surface-variant">{{ $competition->description }}</p>
                @endif

                @if($competition->location || $competition->organizer_contact || $competition->entry_fee)
                    <div class="flex flex-wrap gap-x-6 gap-y-1 text-xs pt-2 border-t border-outline-variant text-on-surface-variant">
                        @if($competition->location)<span>📍 {{ $competition->location }}</span>@endif
                        @if($competition->entry_fee)<span>💳 {{ $competition->entry_fee }}</span>@endif
                        @if($competition->organizer_contact)<span>☎️ {{ $competition->organizer_contact }}</span>@endif
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-surface-container-low border border-outline-variant rounded-xl p-8 lg:p-10 text-center">
                <p class="text-on-surface-variant">Trenutno nema otvorenih takmičenja koja odgovaraju pretrazi.</p>
            </div>
        @endforelse
    </div>

    <div class="pt-6">{{ $competitions->links() }}</div>
</main>

<!-- Bottom Navigation (Mobile Only) -->
<nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-2 pb-[env(safe-area-inset-bottom)] bg-surface-container-high rounded-t-xl border-t border-outline-variant shadow-lg">
    <a href="{{ route('player.dashboard') }}" class="flex flex-col items-center justify-center text-on-surface-variant active:scale-90 transition-transform">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="font-label-bold text-[10px]">Dashboard</span>
    </a>
    <a href="{{ route('player.dashboard.matches') }}" class="flex flex-col items-center justify-center text-on-surface-variant active:scale-90 transition-transform">
        <span class="material-symbols-outlined">sports_tennis</span>
        <span class="font-label-bold text-[10px]">Mečevi</span>
    </a>
    <a href="{{ route('player.leagues.index') }}" class="flex flex-col items-center justify-center bg-primary-container text-on-primary-container rounded-full px-4 py-1 active:scale-90 transition-transform">
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

    document.querySelectorAll('.apply-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const button = form.querySelector('.apply-button');
            button.disabled = true;
            button.querySelector('.apply-button-text').textContent = 'Šaljem zahtjev...';
            button.querySelector('.apply-button-spinner').classList.remove('hidden');
        });
    });
</script>
</body>
</html>
