<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Upiši rezultat - MojTurnir</title>

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
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 lg:mb-8">
            <h1 class="font-headline-lg-mobile lg:font-display text-headline-lg-mobile lg:text-headline-lg text-primary tracking-tight">Upiši rezultat</h1>
            <p class="text-on-surface-variant text-sm lg:text-base mt-1">{{ $competition->name }} &middot; protiv {{ $opponent->name ?? 'TBD' }}</p>
            @if($match->status !== 'completed')
                <p class="text-sm mt-2">
                    <a href="{{ route('player.matches.live', $match) }}" class="font-semibold text-secondary hover:opacity-80">🏓 Ili unesi rezultat uživo, poen po poen →</a>
                </p>
            @endif
        </div>

        <div class="bg-surface-container-low border border-outline-variant rounded-xl p-5 lg:p-8">
            @if($errors->any())
                <div class="rounded-xl p-4 text-sm mb-6 bg-error/10 border border-error/30 text-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('player.matches.result.update', $match) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium mb-2 text-on-surface">Protivnik</label>
                    <div class="w-full px-4 py-3 rounded-xl text-sm bg-surface-container border border-outline-variant text-on-surface-variant">
                        {{ $opponent->name ?? 'TBD' }}
                    </div>
                </div>

                <div>
                    <label for="played_at" class="block text-sm font-medium mb-2 text-on-surface">Datum i vrijeme</label>
                    <input type="datetime-local" id="played_at" name="played_at"
                           value="{{ old('played_at', optional($match->played_at ?? $match->scheduled_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full px-4 py-3 rounded-xl text-sm bg-surface-container-highest border border-outline-variant text-on-surface focus:outline-none focus:border-primary transition-all">
                </div>

                <div>
                    <label for="venue_id" class="block text-sm font-medium mb-2 text-on-surface">Teren</label>
                    @if($venues->isNotEmpty())
                        <select id="venue_id" name="venue_id"
                                class="w-full px-4 py-3 rounded-xl text-sm bg-surface-container-highest border border-outline-variant text-on-surface focus:outline-none focus:border-primary transition-all">
                            <option value="">— nije odabran —</option>
                            @foreach($venues as $venue)
                                <option value="{{ $venue->id }}" {{ (string) old('venue_id', $match->venue_id) === (string) $venue->id ? 'selected' : '' }}>
                                    {{ $venue->name }}{{ $venue->address ? ' - '.$venue->address : '' }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="w-full px-4 py-3 rounded-xl text-sm bg-surface-container border border-outline-variant text-on-surface-variant">
                            Još nema unesenih terena{{ $competition->city ? ' za grad '.$competition->city->name : '' }} - zamoli organizatora da doda teren u admin panelu.
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-on-surface">Rezultat po setovima ({{ $unitLabel }})</label>
                    <p class="text-xs mb-3 text-on-surface-variant">Popuni samo onoliko setova koliko je odigrano - prazni setovi na kraju se ignorišu.</p>
                    <div class="space-y-2">
                        @for($i = 0; $i < $maxSets; $i++)
                            <div class="flex items-center gap-3">
                                <span class="text-sm w-14 text-on-surface-variant">Set {{ $i + 1 }}</span>
                                <input type="number" name="sets[{{ $i }}][mine]" min="0" placeholder="Ja"
                                       value="{{ old("sets.$i.mine", $existingSets[$i]['mine'] ?? '') }}"
                                       class="w-20 text-center px-3 py-2 rounded-xl text-sm bg-surface-container-highest border border-outline-variant text-on-surface focus:outline-none focus:border-primary transition-all">
                                <span class="text-on-surface-variant">:</span>
                                <input type="number" name="sets[{{ $i }}][theirs]" min="0" placeholder="Protivnik"
                                       value="{{ old("sets.$i.theirs", $existingSets[$i]['theirs'] ?? '') }}"
                                       class="w-20 text-center px-3 py-2 rounded-xl text-sm bg-surface-container-highest border border-outline-variant text-on-surface focus:outline-none focus:border-primary transition-all">
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('player.dashboard.matches') }}" class="flex-1 text-center px-4 py-3 rounded-xl text-sm font-semibold transition-all bg-surface-container-highest text-on-surface">
                        Odustani
                    </a>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold transition-all active:scale-95 bg-primary text-on-primary">
                        Sačuvaj rezultat
                    </button>
                </div>
            </form>

            @if($match->status !== 'scheduled')
                <div class="mt-4 pt-4 border-t border-outline-variant">
                    <form method="POST" action="{{ route('player.matches.result.reset', $match) }}"
                          onsubmit="return confirm('Resetovati ovaj meč? Uneseni rezultat, setovi, datum i teren će biti obrisani, a meč vraćen na zakazano - koristi ovo ako si unio rezultat na pogrešan meč.');">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-xs font-semibold transition-all bg-error/10 border border-error/30 text-error">
                            Resetuj meč (obriši uneseni rezultat)
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</main>

<!-- Bottom Navigation (Mobile Only) -->
<nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-2 pb-[env(safe-area-inset-bottom)] bg-surface-container-high rounded-t-xl border-t border-outline-variant shadow-lg">
    <a href="{{ route('player.dashboard') }}" class="flex flex-col items-center justify-center text-on-surface-variant active:scale-90 transition-transform">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="font-label-bold text-[10px]">Dashboard</span>
    </a>
    <a href="{{ route('player.dashboard.matches') }}" class="flex flex-col items-center justify-center bg-primary-container text-on-primary-container rounded-full px-4 py-1 active:scale-90 transition-transform">
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
