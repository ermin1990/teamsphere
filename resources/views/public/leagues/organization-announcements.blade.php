<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Obavijesti - {{ $organization->name }} - MojTurnir</title>

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
    body { background-color: #0b0e14; color: #e1e2eb; overflow-x: hidden; }
    .custom-scrollbar::-webkit-scrollbar { display: none; }
    .custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #10131a; }
    ::-webkit-scrollbar-thumb { background: #32353c; border-radius: 4px; }
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
        <a class="flex items-center gap-3 px-4 py-3 text-primary border-l-4 border-primary bg-primary/5 font-label-bold rounded-r-lg" href="{{ route('competitions.organization', $organization) }}">
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
            <a href="{{ route('competitions.organization', $organization) }}" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high shrink-0">
                <span class="material-symbols-outlined text-primary">arrow_back</span>
            </a>
            <h1 class="font-headline-md text-on-surface truncate">{{ $organization->name }}</h1>
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
        <!-- Hero -->
        <section class="-mx-margin-mobile lg:mx-0 mb-6 bg-surface-container-low lg:p-8 border-y lg:border border-outline-variant lg:rounded-xl overflow-hidden">
            <div class="px-margin-mobile py-5 lg:p-0 flex items-center gap-4">
                @if($organization->logo_url || $organization->logo)
                    <img src="{{ $organization->logo_url ?? asset('storage/' . $organization->logo) }}"
                         alt="{{ $organization->name }}"
                         class="w-14 h-14 lg:w-16 lg:h-16 rounded-xl object-contain bg-surface-container-lowest border border-outline-variant shrink-0">
                @else
                    <div class="w-14 h-14 lg:w-16 lg:h-16 rounded-xl bg-surface-container-highest border border-outline-variant flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-2xl text-on-surface-variant">corporate_fare</span>
                    </div>
                @endif
                <h1 class="font-display text-2xl lg:text-display truncate">{{ $organization->name }}</h1>
            </div>
        </section>

        <!-- Tabs -->
        <div class="flex items-center gap-1 mb-6 lg:mb-8 border-b border-outline-variant">
            <a href="{{ route('competitions.organization', $organization) }}"
               class="px-4 py-3 font-label-bold text-sm border-b-2 border-transparent text-on-surface-variant hover:text-on-surface transition-colors">
                Pregled
            </a>
            <a href="{{ route('competitions.organization.announcements', $organization) }}"
               class="px-4 py-3 font-label-bold text-sm border-b-2 border-primary text-primary flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">campaign</span> Obavijesti
            </a>
        </div>

        @if($announcements->isNotEmpty())
            <div class="space-y-4">
                @foreach($announcements as $announcement)
                    <div class="p-4 lg:p-6 bg-surface-container-low border border-outline-variant rounded-xl">
                        <p class="text-xs text-on-surface-variant mb-1">{{ $announcement->created_at->format('d.m.Y. H:i') }}</p>
                        <h3 class="font-headline-md">{{ $announcement->title }}</h3>
                        <p class="text-sm text-on-surface-variant mt-2 whitespace-pre-line">{{ $announcement->body }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-surface-container-low border border-outline-variant rounded-xl">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4 block">campaign</span>
                <h4 class="font-headline-md text-on-surface-variant mb-2">Još nema obavijesti</h4>
                <p class="text-on-surface-variant text-sm">Ovdje će se pojaviti obavijesti organizatora za cijelu organizaciju.</p>
            </div>
        @endif
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
