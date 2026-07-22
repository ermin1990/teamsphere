<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ isset($organization) ? $organization->name . ' - MojTurnir' : (isset($city) ? 'Lige u gradu ' . $city->name . ' - MojTurnir' : 'Takmičenja - MojTurnir') }}</title>

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
    body { background-color: #0b0e14; color: #e1e2eb; overflow-x: hidden; }
    .custom-scrollbar::-webkit-scrollbar { display: none; }
    .custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #10131a; }
    ::-webkit-scrollbar-thumb { background: #32353c; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #3c4a46; }
</style>
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen pb-24 lg:pb-8">

@php
    $sportIcon = function ($sportName) {
        $n = mb_strtolower($sportName ?? '');
        if (str_contains($n, 'fudbal')) return 'sports_soccer';
        if (str_contains($n, 'košark') || str_contains($n, 'kosark')) return 'sports_basketball';
        if (str_contains($n, 'odbojk')) return 'sports_volleyball';
        return 'sports_tennis';
    };
@endphp

<!-- Persistent SideNavBar (desktop) -->
<nav class="hidden lg:flex w-sidebar-width h-screen fixed left-0 top-0 border-r border-outline-variant bg-surface-container-low flex-col py-2 z-50">
    <div class="px-6 py-8">
        <a href="{{ route('home') }}" class="font-display text-primary tracking-tighter text-2xl uppercase block">MojTurnir</a>
        <p class="font-label-bold text-on-surface-variant opacity-70 text-xs">Organizuj. Igraj. Pobijedi.</p>
    </div>
    <div class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar">
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors duration-200 rounded-lg" href="{{ route('home') }}">
            <span class="material-symbols-outlined">dashboard</span><span>Početna</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-primary border-l-4 border-primary bg-primary/5 font-label-bold" href="{{ route('competitions.index') }}">
            <span class="material-symbols-outlined">emoji_events</span><span>Takmičenja</span>
        </a>
        @isset($organization)
            <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:text-on-surface font-body-md hover:bg-surface-variant/50 transition-colors duration-200 rounded-lg" href="{{ route('competitions.organization', $organization) }}">
                <span class="material-symbols-outlined">corporate_fare</span><span class="truncate">{{ \Illuminate\Support\Str::limit($organization->name, 18) }}</span>
            </a>
        @endisset
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
        <span class="font-headline-md text-on-surface truncate">{{ isset($organization) ? $organization->name : (isset($city) ? $city->name : 'Takmičenja') }}</span>
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
        <a class="text-primary font-bold border-b-2 border-primary pb-1" href="{{ route('competitions.index') }}">Takmičenja</a>
    </nav>
</header>

<!-- Main Content Canvas -->
<main class="lg:ml-[260px] lg:mt-16 mt-16 p-margin-mobile lg:p-gutter min-h-screen">

    <!-- Hero -->
    <section class="-mx-margin-mobile lg:mx-0 mb-6 lg:mb-8 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl overflow-hidden">
        @if(isset($organization))
            <div class="px-margin-mobile py-5 lg:p-8 flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-6">
                @if($organization->logo_url || $organization->logo)
                    <img src="{{ $organization->logo_url ?? asset('storage/' . $organization->logo) }}"
                         alt="{{ $organization->name }}"
                         class="w-16 h-16 lg:w-20 lg:h-20 rounded-xl object-contain bg-surface-container-lowest border border-outline-variant shrink-0">
                @else
                    <div class="w-16 h-16 lg:w-20 lg:h-20 rounded-xl bg-surface-container-highest border border-outline-variant flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-3xl text-on-surface-variant">corporate_fare</span>
                    </div>
                @endif
                <div class="min-w-0">
                    <h1 class="font-display text-2xl lg:text-display text-on-surface truncate">{{ $organization->name }}</h1>
                    <p class="text-on-surface-variant text-sm lg:text-base">Aktivna takmičenja i rezultati uživo</p>
                    @if($organization->description)
                        <p class="mt-2 text-sm text-on-surface-variant">{{ Str::limit($organization->description, 160) }}</p>
                    @endif
                </div>
            </div>
        @elseif(isset($city))
            <div class="px-margin-mobile py-5 lg:p-8 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-3xl lg:text-4xl">location_on</span>
                <div>
                    <h1 class="font-display text-2xl lg:text-display text-on-surface">Lige u gradu {{ $city->name }}</h1>
                    <p class="text-on-surface-variant text-sm lg:text-base">Sva javna takmičenja u ovom gradu, iz svih organizacija</p>
                </div>
            </div>
        @else
            <div class="px-margin-mobile py-5 lg:p-8 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-3xl lg:text-4xl">emoji_events</span>
                <div>
                    <h1 class="font-display text-2xl lg:text-display text-on-surface">Sva takmičenja</h1>
                    <p class="text-on-surface-variant text-sm lg:text-base">Izaberite takmičenje za pregled tabele i mečeva</p>
                </div>
            </div>
        @endif
    </section>

    @isset($organization)
        <div class="flex items-center gap-1 mb-6 lg:mb-8 border-b border-outline-variant">
            <a href="{{ route('competitions.organization', $organization) }}"
               class="px-4 py-3 font-label-bold text-sm border-b-2 border-primary text-primary">
                Pregled
            </a>
            <a href="{{ route('competitions.organization.announcements', $organization) }}"
               class="px-4 py-3 font-label-bold text-sm border-b-2 border-transparent text-on-surface-variant hover:text-on-surface transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">campaign</span> Obavijesti
            </a>
        </div>

        @php
            $featuredAnnouncement = $organization->featuredAnnouncement();
        @endphp
        @if($featuredAnnouncement)
        <section class="-mx-margin-mobile lg:mx-0 mb-6 lg:mb-8 bg-primary/10 border-y lg:border border-primary/30 lg:rounded-xl px-margin-mobile py-4 lg:p-5">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-primary text-[18px]">push_pin</span>
                <span class="text-xs text-on-surface-variant">{{ $featuredAnnouncement->created_at->format('d.m.Y.') }}</span>
            </div>
            <h3 class="font-headline-md">{{ $featuredAnnouncement->title }}</h3>
            <p class="text-sm text-on-surface-variant mt-1 whitespace-pre-line">{{ $featuredAnnouncement->body }}</p>
        </section>
        @endif
    @endisset

    @if(isset($organization) && $organization->links->count() > 0)
        <!-- Organization Links/Banners -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4 mb-6 lg:mb-8">
            @foreach($organization->links as $link)
                @php
                    $isYoutube = str_contains(strtolower($link->url), 'youtube');
                    $isFacebook = str_contains(strtolower($link->url), 'facebook');
                    $isInstagram = str_contains(strtolower($link->url), 'instagram');
                @endphp
                <a href="{{ $link->url }}" target="_blank"
                   class="relative overflow-hidden rounded-xl p-4 lg:p-5 border transition-all duration-300 hover:scale-[1.02] group"
                   style="
                        @if($isYoutube)
                            background: linear-gradient(135deg, #FF0000 0%, #8B0000 100%);
                            border-color: #FF0000;
                        @elseif($isFacebook)
                            background: linear-gradient(135deg, #1877F2 0%, #0952B8 100%);
                            border-color: #1877F2;
                        @elseif($isInstagram)
                            background: linear-gradient(135deg, #E4405F 0%, #833AB4 100%);
                            border-color: #E4405F;
                        @else
                            background: linear-gradient(135deg, #6366F1 0%, #4338CA 100%);
                            border-color: #6366F1;
                        @endif
                   ">
                    <div class="relative flex items-center gap-3 lg:gap-4">
                        <div class="w-12 h-12 lg:w-14 lg:h-14 bg-white/10 backdrop-blur-sm rounded-lg flex items-center justify-center flex-shrink-0 text-white transition-transform group-hover:scale-110">
                            @if($isYoutube)
                                <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            @elseif($isFacebook)
                                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            @elseif($isInstagram)
                                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                            @else
                                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-sm lg:text-base text-white mb-0.5 truncate">{{ $link->title }}</h4>
                            <p class="text-xs text-white/80 flex items-center gap-1">
                                Posjetite
                                <span class="material-symbols-outlined text-xs group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </section>
    @endif

    @if(isset($organization) && isset($seasons) && $seasons->count() > 0)
        <form method="GET" class="mb-4 flex items-center gap-3">
            <label for="season_id" class="text-sm text-on-surface-variant font-label-bold">Sezona:</label>
            <select name="season_id" id="season_id" onchange="this.form.submit()"
                    class="bg-surface-container-high border border-outline-variant text-on-surface text-sm rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="" {{ empty($selectedSeasonId) ? 'selected' : '' }}>Sve sezone</option>
                @foreach($seasons as $season)
                    <option value="{{ $season->id }}" {{ (string) $selectedSeasonId === (string) $season->id ? 'selected' : '' }}>
                        {{ $season->name }}{{ $season->is_active ? ' (aktivna)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>
    @endif

    @if($competitions->count() > 0)
        @php
            $competitionsBySport = $competitions->groupBy(fn ($c) => $c->sport->name);
        @endphp

        <div class="space-y-4 lg:space-y-6">
            @foreach($competitionsBySport as $sportName => $sportCompetitions)
                <section class="-mx-margin-mobile lg:mx-0 bg-surface-container-low border-y lg:border border-outline-variant lg:rounded-xl overflow-hidden">
                    <div class="bg-surface-container-high px-margin-mobile lg:px-4 py-2 border-b border-outline-variant flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-lg">{{ $sportIcon($sportName) }}</span>
                        <h4 class="font-label-bold text-sm text-on-surface uppercase tracking-wider truncate">{{ $sportName }}</h4>
                        <span class="text-xs text-on-surface-variant">({{ $sportCompetitions->count() }})</span>
                    </div>
                    <div class="divide-y divide-outline-variant/20">
                        @foreach($sportCompetitions as $competition)
                            @php
                                $cLive = $competition->status === 'in_progress';
                                $cCompleted = $competition->status === 'completed';
                                $cUpcomingOpen = !$cCompleted && !$cLive && $competition->registration_open
                                    && (!$competition->start_date || $competition->start_date->isFuture());
                            @endphp
                            <a href="{{ route('competitions.show', $competition) }}" class="flex items-center justify-between gap-3 px-margin-mobile lg:px-4 py-3 hover:bg-surface-variant/30 transition-colors group">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="material-symbols-outlined text-on-surface-variant text-lg shrink-0">{{ $competition->type === 'tournament' ? 'workspace_premium' : 'emoji_events' }}</span>
                                    <div class="min-w-0">
                                        <span class="font-body-md text-on-surface truncate block">{{ $competition->name }}</span>
                                        @if(!isset($organization))
                                            <span class="text-xs text-on-surface-variant truncate block">{{ $competition->organization->name }}</span>
                                        @endif
                                        @if($competition->registration_deadline)
                                            <span class="text-xs {{ $competition->registration_deadline->isPast() ? 'text-on-surface-variant' : 'text-primary' }}">
                                                {{ $competition->registration_deadline->isPast() ? 'Prijave zatvorene' : 'Prijave otvorene do ' . $competition->registration_deadline->format('d.m.Y.') }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase shrink-0 {{ $cCompleted ? 'bg-outline-variant/20 text-on-surface-variant' : ($cLive ? 'bg-primary/10 text-primary animate-pulse' : 'bg-secondary/10 text-secondary') }}">
                                        {{ $cCompleted ? 'Završeno' : ($cLive ? 'U toku' : ($cUpcomingOpen ? 'Prijave otvorene' : 'Aktivno')) }}
                                    </span>
                                </div>
                                <span class="text-primary opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-xs font-label-bold shrink-0">
                                    Pogledaj <span class="material-symbols-outlined text-xs">chevron_right</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-surface-container-low border border-outline-variant rounded-xl">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4 block">emoji_events</span>
            <h4 class="font-headline-md text-on-surface-variant mb-2">Nema dostupnih takmičenja</h4>
            <p class="text-on-surface-variant text-sm">Provjerite kasnije za predstojeća takmičenja.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="mt-10 text-center pb-6">
        <div class="inline-block px-4 py-2 rounded-full border text-xs border-outline-variant text-on-surface-variant">
            Želite li dodati vašu ligu? <a href="{{ route('register') }}" class="font-bold hover:text-primary transition-colors text-primary">Registrujte se ovdje</a>
        </div>
    </div>
</main>

<!-- BottomNavBar for Mobile -->
<nav class="fixed bottom-0 w-full h-16 bg-surface-container-highest/95 backdrop-blur-md border-t border-outline-variant z-50 flex lg:hidden items-center justify-around px-4 rounded-t-xl shadow-[0_-10px_20px_rgba(0,0,0,0.4)]">
    <a href="{{ route('home') }}" class="flex flex-col items-center justify-center text-on-surface-variant">
        <span class="material-symbols-outlined">home</span><span class="text-[10px] font-label-bold">Home</span>
    </a>
    <a href="{{ route('competitions.index') }}" class="flex flex-col items-center justify-center bg-primary-container text-on-primary-container rounded-full px-4 py-1">
        <span class="material-symbols-outlined">emoji_events</span><span class="text-[10px] font-label-bold">Takmičenja</span>
    </a>
    @auth
        <a href="{{ auth()->user()->isOrganizerOrStaff() ? route('dashboard') : route('player.dashboard') }}" class="flex flex-col items-center justify-center text-on-surface-variant">
            <span class="material-symbols-outlined">person</span><span class="text-[10px] font-label-bold">Nalog</span>
        </a>
    @else
        <a href="{{ route('login') }}" class="flex flex-col items-center justify-center text-on-surface-variant">
            <span class="material-symbols-outlined">login</span><span class="text-[10px] font-label-bold">Prijava</span>
        </a>
    @endauth
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
