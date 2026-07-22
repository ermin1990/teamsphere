<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Profil - MojTurnir</title>

<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#0b0e14">
<link rel="apple-touch-icon" href="/icons/icon-192.png">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Inter:wght@400;600;700&display=swap">
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
                    "tertiary-container": "var(--c-tertiary-container)", "on-tertiary-container": "var(--c-on-tertiary-container)", "error": "var(--c-error)", "primary-soft": "var(--c-primary-soft)", "error-soft": "var(--c-error-soft)", "secondary-soft": "var(--c-secondary-soft)", "error-container": "var(--c-error-container)", "on-error-container": "var(--c-on-error-container)",
                },
                borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
                spacing: { gutter: "24px", "margin-mobile": "16px", "sidebar-width": "260px" },
                fontFamily: {
                    display: ["Montserrat"], "headline-md": ["Montserrat"], "headline-lg-mobile": ["Montserrat"],
                    "body-md": ["Inter"], "body-sm": ["Inter"], "label-bold": ["Inter"],
                },
                fontSize: {
                    "headline-md": ["20px", { lineHeight: "1.3", fontWeight: "700" }],
                    "headline-lg-mobile": ["24px", { lineHeight: "1.2", fontWeight: "700" }],
                    "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                    "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                    "label-bold": ["12px", { lineHeight: "1.2", letterSpacing: "0.05em", fontWeight: "600" }],
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
    .input-focus-ring:focus { border-color: var(--c-primary); box-shadow: inset 0 0 8px rgba(87, 241, 219, 0.1); outline: none; }

    {{-- The shared profile.partials.* forms and x-text-input/x-primary-button/etc. components
         read these custom properties - defining them here recolors those shared components
         to this page's teal palette without touching the components themselves. --}}
    :root {
        --bg-primary: #0b0e14; --bg-secondary: #191c22; --bg-tertiary: #272a31;
        --bg-card: rgba(25, 28, 34, 0.75); --bg-card-solid: #191c22; --bg-accent: rgba(11, 14, 20, 0.95); --bg-hover: rgba(39, 42, 49, 0.6);
        --text-primary: #e1e2eb; --text-secondary: #bacac5; --text-tertiary: #8b9a96; --text-muted: #667572;
        --border-primary: rgba(87, 241, 219, 0.16); --border-secondary: rgba(255, 255, 255, 0.07); --border-accent: rgba(87, 241, 219, 0.3);
        --accent-blue: #57f1db; --shadow-primary: rgba(0, 0, 0, 0.65);
    }
</style>
@include('partials.theme-vars')
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen pb-24 lg:pb-8">

@php
    $isOrganizer = auth()->user()->isOrganizerOrStaff();
    $initials = mb_strtoupper(collect(preg_split('/\s+/', trim(auth()->user()->name ?? '')))->filter()->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')) ?: '?';
@endphp

@unless($isOrganizer)
<!-- Persistent SideNavBar (desktop, players only) -->
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
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors duration-200 rounded-lg" href="{{ route('player.leagues.index') }}">
            <span class="material-symbols-outlined">emoji_events</span><span>Takmičenja</span>
        </a>
    </div>
    <div class="px-4 py-6 border-t border-outline-variant space-y-1">
        <a class="flex items-center gap-3 px-4 py-2 text-primary border-l-4 border-primary bg-primary/5 font-label-bold" href="{{ route('profile.edit') }}">
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
@endunless

<!-- Top App Bar -->
<header class="{{ $isOrganizer ? '' : 'lg:ml-[260px]' }} bg-surface sticky top-0 z-40 flex justify-between items-center px-margin-mobile h-16 w-full {{ $isOrganizer ? '' : 'lg:w-[calc(100%-260px)]' }} border-b border-outline-variant">
    <div class="flex items-center gap-3">
        @if($isOrganizer)
            <a href="{{ route('dashboard') }}" class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-primary">arrow_back</span>
            </a>
        @endif
        <span class="font-headline-md text-headline-md font-bold text-primary">MojTurnir</span>
    </div>
    @if($isOrganizer)
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-on-surface-variant hover:bg-surface-container-high transition-colors p-2 rounded-full active:scale-95">
                <span class="material-symbols-outlined">logout</span>
            </button>
        </form>
    @endif
</header>

<main class="{{ $isOrganizer ? '' : 'lg:ml-[260px]' }} pb-32 px-margin-mobile pt-6 max-w-lg mx-auto">
    <!-- Hero Profile Section -->
    <div class="relative mb-8 text-center">
        <div class="w-24 h-24 rounded-full mx-auto border-2 border-primary p-1">
            <div class="w-full h-full rounded-full bg-primary-container flex items-center justify-center text-on-primary-container font-display text-2xl">
                {{ $initials }}
            </div>
        </div>
        <h1 class="mt-4 font-headline-lg-mobile text-headline-lg-mobile text-on-surface">{{ auth()->user()->name }}</h1>
        <p class="text-on-surface-variant font-body-sm uppercase tracking-wider">{{ $isOrganizer ? 'Organizator' : 'Igrač' }}</p>
    </div>

    <!-- Account Info Card -->
    <section class="mb-6">
        <div class="bg-surface-container border border-outline-variant rounded-xl overflow-hidden">
            <div class="p-4 border-b border-outline-variant flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">person</span>
                <h2 class="font-headline-md text-[18px]">Podaci o profilu</h2>
            </div>
            <div class="p-4">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </section>

    <!-- Appearance Card -->
    <section class="mb-6">
        <div class="bg-surface-container border border-outline-variant rounded-xl overflow-hidden">
            <div class="p-4 border-b border-outline-variant flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">palette</span>
                <h2 class="font-headline-md text-[18px]">Izgled</h2>
            </div>
            <div class="p-4">
                @php $currentTheme = auth()->user()->theme === 'light' ? 'light' : 'dark'; @endphp
                <p class="text-on-surface-variant font-body-sm mb-4">Bira temu za javne stranice i "Moje lige".</p>
                <div class="grid grid-cols-2 gap-3">
                    <form method="POST" action="{{ route('profile.theme') }}">
                        @csrf
                        <input type="hidden" name="theme" value="dark">
                        <button type="submit"
                                class="w-full flex flex-col items-center gap-2 px-4 py-3 rounded-lg border-2 transition-colors {{ $currentTheme === 'dark' ? 'border-primary bg-primary/5' : 'border-outline-variant' }}">
                            <span class="material-symbols-outlined {{ $currentTheme === 'dark' ? 'text-primary' : 'text-on-surface-variant' }}">dark_mode</span>
                            <span class="font-label-bold text-[13px] {{ $currentTheme === 'dark' ? 'text-primary' : 'text-on-surface-variant' }}">Tamna</span>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('profile.theme') }}">
                        @csrf
                        <input type="hidden" name="theme" value="light">
                        <button type="submit"
                                class="w-full flex flex-col items-center gap-2 px-4 py-3 rounded-lg border-2 transition-colors {{ $currentTheme === 'light' ? 'border-primary bg-primary/5' : 'border-outline-variant' }}">
                            <span class="material-symbols-outlined {{ $currentTheme === 'light' ? 'text-primary' : 'text-on-surface-variant' }}">light_mode</span>
                            <span class="font-label-bold text-[13px] {{ $currentTheme === 'light' ? 'text-primary' : 'text-on-surface-variant' }}">Svijetla</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Card -->
    <section class="mb-6">
        <div class="bg-surface-container border border-outline-variant rounded-xl overflow-hidden">
            <div class="p-4 border-b border-outline-variant flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">lock</span>
                <h2 class="font-headline-md text-[18px]">Sigurnost</h2>
            </div>
            <div class="p-4">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </section>

    <!-- Danger Zone -->
    <section class="mb-6">
        <div class="bg-surface-container border border-error/30 rounded-xl overflow-hidden">
            <div class="p-4 border-b border-error/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-error">warning</span>
                <h2 class="font-headline-md text-[18px] text-error">Opasna zona</h2>
            </div>
            <div class="p-4">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </section>

    @unless($isOrganizer)
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full bg-surface-container-highest text-on-surface-variant py-3 rounded-lg font-label-bold text-[14px] uppercase tracking-widest active:scale-[0.98] transition-all">
                Odjava
            </button>
        </form>
    @endunless
</main>

@unless($isOrganizer)
<!-- Bottom Navigation Bar (Mobile Only) -->
<nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-2 pb-[env(safe-area-inset-bottom)] bg-surface-container-high border-t border-outline-variant shadow-lg rounded-t-xl">
    <a href="{{ route('player.dashboard') }}" class="flex flex-col items-center justify-center text-on-surface-variant active:scale-90 transition-transform">
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
    <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center bg-primary-container text-on-primary-container rounded-full px-4 py-1 active:scale-90 transition-transform">
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">person</span>
        <span class="font-label-bold text-[10px]">Nalog</span>
    </a>
</nav>
@endunless

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
