<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('app.name', 'MojTurnir') }}</title>
<meta name="description" content="MojTurnir — organizuj lige i turnire za stoni tenis, tenis i padel. Rasporedi, rezultati i tabele na jednom mjestu.">

<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<meta name="theme-color" content="#0b0e14">
<link rel="apple-touch-icon" href="/icons/icon-192.png">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Inter:wght@400;600&display=swap">
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
                borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", "2xl": "1rem", full: "9999px" },
                spacing: { gutter: "24px", "margin-mobile": "16px", "container-max": "1200px" },
                fontFamily: {
                    "headline-md": ["Montserrat"], "body-sm": ["Inter"], display: ["Montserrat"],
                    "headline-lg": ["Montserrat"], "body-md": ["Inter"], "body-lg": ["Inter"],
                    "label-bold": ["Inter"],
                },
                fontSize: {
                    "headline-md": ["22px", { lineHeight: "1.3", fontWeight: "700" }],
                    "headline-lg": ["36px", { lineHeight: "1.2", letterSpacing: "-0.01em", fontWeight: "800" }],
                    "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                    display: ["56px", { lineHeight: "1.1", letterSpacing: "-0.02em", fontWeight: "900" }],
                    "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                    "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                    "label-bold": ["12px", { lineHeight: "1.2", letterSpacing: "0.05em", fontWeight: "600" }],
                },
                animation: {
                    float: 'float 6s ease-in-out infinite',
                },
                keyframes: {
                    float: {
                        '0%, 100%': { transform: 'translateY(0)' },
                        '50%': { transform: 'translateY(-16px)' },
                    },
                },
            },
        },
    }
</script>
<style>
    html { scroll-behavior: smooth; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
    body { background-color: #0b0e14; color: #e1e2eb; overflow-x: hidden; }
    .hero-pattern {
        background-color: #0b0e14;
        background-image:
            radial-gradient(circle at 15% 40%, hsla(170, 70%, 15%, 0.35) 0%, transparent 50%),
            radial-gradient(circle at 85% 20%, hsla(189, 100%, 15%, 0.3) 0%, transparent 50%);
    }
    .grid-mesh {
        background-size: 48px 48px;
        background-image: linear-gradient(to right, rgba(87, 241, 219, 0.06) 1px, transparent 1px),
                          linear-gradient(to bottom, rgba(87, 241, 219, 0.06) 1px, transparent 1px);
        mask-image: radial-gradient(circle at center, black 40%, transparent 80%);
        -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 80%);
    }
    .card-hover { transition: all 0.25s ease; }
    .card-hover:hover {
        box-shadow: 0 10px 30px -10px rgba(87, 241, 219, 0.25);
        border-color: #57f1db;
        transform: translateY(-4px);
    }
    .gradient-text {
        background: linear-gradient(to right, #62fae3, #2dd4bf);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    @media (prefers-reduced-motion: reduce) {
        html { scroll-behavior: auto; }
    }
</style>
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen">

<!-- Top Nav -->
<nav class="bg-surface-container-lowest/85 backdrop-blur-md w-full sticky top-0 border-b border-outline-variant z-50">
    <div class="flex justify-between items-center px-margin-mobile lg:px-gutter py-4 max-w-container-max mx-auto">
        <a href="{{ route('home') }}" class="font-display text-2xl text-primary tracking-tighter uppercase">MojTurnir</a>
        <div class="hidden md:flex gap-8 items-center">
            <a href="#sportovi" class="text-on-surface-variant font-semibold hover:text-primary transition-colors duration-200">Sportovi</a>
            <a href="#kako-radi" class="text-on-surface-variant font-semibold hover:text-primary transition-colors duration-200">Kako radi</a>
            <a href="{{ route('competitions.index') }}" class="text-on-surface-variant font-semibold hover:text-primary transition-colors duration-200">Takmičenja</a>
            <a href="#o-aplikaciji" class="text-on-surface-variant font-semibold hover:text-primary transition-colors duration-200">O aplikaciji</a>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ auth()->user()->isOrganizerOrStaff() ? route('dashboard') : route('player.dashboard') }}" class="bg-primary-container text-on-primary-container rounded px-5 py-2.5 font-label-bold hover:bg-primary transition-all">
                    Moj nalog
                </a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="text-on-surface-variant font-semibold hover:text-primary transition-colors text-sm sm:text-base">Prijava</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="bg-primary-container text-on-primary-container rounded px-5 py-2.5 font-label-bold hover:bg-primary transition-all">
                        Počni besplatno
                    </a>
                @endif
            @endauth
        </div>
    </div>
</nav>

<main>
    <!-- Hero -->
    <section class="hero-pattern relative overflow-hidden py-24 md:py-36 px-margin-mobile lg:px-gutter border-b border-outline-variant">
        <div class="absolute inset-0 grid-mesh z-0"></div>
        <div class="max-w-container-max mx-auto relative z-10 flex flex-col items-center text-center">
            <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-on-surface-variant border border-outline-variant rounded-full px-4 py-2 mb-8">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                Stoni tenis · Tenis · Padel
            </div>

            <h1 class="font-display text-4xl sm:text-5xl md:text-display leading-[1.05] mb-8 tracking-tighter text-on-surface">
                TVOJA LIGA.<br>
                TVOJ TURNIR.<br>
                <span class="gradient-text">REZULTAT ZA REZULTATOM.</span>
            </h1>

            <p class="font-body-lg text-lg md:text-xl text-on-surface-variant max-w-2xl mb-10 leading-relaxed">
                MojTurnir organizuje rasporede, prati rezultate uživo i vodi tabele za tvoj klub ili ligu —
                bez tabela u Excelu i grupa na Viberu.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                @auth
                    <a href="{{ auth()->user()->isOrganizerOrStaff() ? route('dashboard') : route('player.dashboard') }}" class="bg-primary-container text-on-primary-container rounded-lg px-8 py-4 font-label-bold text-base hover:bg-primary transition-all shadow-[0_0_20px_rgba(87,241,219,0.25)] hover:shadow-[0_0_30px_rgba(87,241,219,0.4)] transform hover:-translate-y-1 text-center">
                        Moj nalog
                    </a>
                @else
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-primary-container text-on-primary-container rounded-lg px-8 py-4 font-label-bold text-base hover:bg-primary transition-all shadow-[0_0_20px_rgba(87,241,219,0.25)] hover:shadow-[0_0_30px_rgba(87,241,219,0.4)] transform hover:-translate-y-1 text-center">
                            Kreiraj organizaciju
                        </a>
                    @endif
                @endauth
                <a href="{{ route('competitions.index') }}" class="inline-flex items-center justify-center gap-2 border-2 border-outline-variant text-on-surface rounded-lg px-8 py-4 font-label-bold text-base hover:border-primary hover:text-primary transition-all transform hover:-translate-y-1">
                    Pregledaj takmičenja
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Real stats strip -->
    <section class="py-10 border-b border-outline-variant bg-surface-container-lowest">
        <div class="max-w-container-max mx-auto px-margin-mobile lg:px-gutter grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <div class="font-display text-2xl md:text-headline-lg text-primary">{{ $organizationsCount }}</div>
                <div class="text-xs md:text-sm text-on-surface-variant font-label-bold uppercase tracking-wider mt-1">Organizacija</div>
            </div>
            <div>
                <div class="font-display text-2xl md:text-headline-lg text-primary">{{ $activeCompetitionsCount }}</div>
                <div class="text-xs md:text-sm text-on-surface-variant font-label-bold uppercase tracking-wider mt-1">Aktivnih takmičenja</div>
            </div>
            <div>
                <div class="font-display text-2xl md:text-headline-lg text-primary">{{ $playersCount }}</div>
                <div class="text-xs md:text-sm text-on-surface-variant font-label-bold uppercase tracking-wider mt-1">Registrovanih igrača</div>
            </div>
            <div>
                <div class="font-display text-2xl md:text-headline-lg text-primary">{{ $matchesPlayedCount }}</div>
                <div class="text-xs md:text-sm text-on-surface-variant font-label-bold uppercase tracking-wider mt-1">Odigranih mečeva</div>
            </div>
        </div>
    </section>

    <!-- Sportovi -->
    <section id="sportovi" class="py-24 md:py-32 px-margin-mobile lg:px-gutter bg-surface relative">
        <div class="max-w-container-max mx-auto">
            <h2 class="font-headline-lg text-headline-lg text-center mb-4 tracking-tight">Jedan sport po organizaciji</h2>
            <p class="text-center text-on-surface-variant font-body-lg mb-16 max-w-2xl mx-auto">Biraš sport pri kreiranju organizacije — specijalizovani alati i pravila skrojeni tačno za tu disciplinu.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-surface-container-low border border-outline-variant rounded-2xl p-8 card-hover relative overflow-hidden">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-primary text-4xl">sports_tennis</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md mb-3">Stoni Tenis</h3>
                    <p class="text-body-sm text-on-surface-variant mb-6">Pojedinačno i ekipno, grupna faza i knockout, uživo praćenje seta po seta.</p>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm text-on-surface"><span class="material-symbols-outlined text-primary text-base mr-2">check_circle</span> Live set tracking</li>
                        <li class="flex items-center text-sm text-on-surface"><span class="material-symbols-outlined text-primary text-base mr-2">check_circle</span> Round-robin generisanje</li>
                        <li class="flex items-center text-sm text-on-surface"><span class="material-symbols-outlined text-primary text-base mr-2">check_circle</span> Timski i pojedinačni formati</li>
                    </ul>
                </div>
                <div class="bg-surface-container-low border border-outline-variant rounded-2xl p-8 card-hover relative overflow-hidden">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-primary text-4xl">sports_tennis</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md mb-3">Tenis</h3>
                    <p class="text-body-sm text-on-surface-variant mb-6">Lige i turniri, pojedinačno ili ekipno, sa punom istorijom mečeva.</p>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm text-on-surface"><span class="material-symbols-outlined text-primary text-base mr-2">check_circle</span> Rang liste igrača</li>
                        <li class="flex items-center text-sm text-on-surface"><span class="material-symbols-outlined text-primary text-base mr-2">check_circle</span> Grupna faza i eliminacija</li>
                        <li class="flex items-center text-sm text-on-surface"><span class="material-symbols-outlined text-primary text-base mr-2">check_circle</span> Praćenje setova uživo</li>
                    </ul>
                </div>
                <div class="bg-surface-container-low border border-outline-variant rounded-2xl p-8 card-hover relative overflow-hidden">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-primary text-4xl">sports_volleyball</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md mb-3">Padel</h3>
                    <p class="text-body-sm text-on-surface-variant mb-6">Klub protiv kluba ili par protiv para — sastav se bira za svaki meč.</p>
                    <ul class="space-y-2">
                        <li class="flex items-center text-sm text-on-surface"><span class="material-symbols-outlined text-primary text-base mr-2">check_circle</span> Upravljanje parovima</li>
                        <li class="flex items-center text-sm text-on-surface"><span class="material-symbols-outlined text-primary text-base mr-2">check_circle</span> Grupne faze</li>
                        <li class="flex items-center text-sm text-on-surface"><span class="material-symbols-outlined text-primary text-base mr-2">check_circle</span> Brze izmjene rasporeda</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Kako radi -->
    <section id="kako-radi" class="py-24 md:py-32 px-margin-mobile lg:px-gutter bg-surface-container-lowest border-y border-outline-variant">
        <div class="max-w-container-max mx-auto">
            <h2 class="font-headline-lg text-headline-lg text-center mb-16 tracking-tight">Od registracije do prve utakmice</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 relative">
                <div class="hidden md:block absolute top-8 left-0 right-0 h-0.5 bg-gradient-to-r from-primary/0 via-primary/30 to-primary/0 z-0"></div>
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 mx-auto bg-surface-container-low border-2 border-primary rounded-full flex items-center justify-center mb-5 shadow-[0_0_20px_rgba(87,241,219,0.15)]">
                        <span class="font-display text-2xl text-primary">1</span>
                    </div>
                    <h3 class="font-headline-md text-lg mb-2">Kreiraj organizaciju</h3>
                    <p class="text-sm text-on-surface-variant">Izaberi sport — stoni tenis, tenis ili padel. Ostaje isti kroz cijelu organizaciju.</p>
                </div>
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 mx-auto bg-surface-container-low border-2 border-outline-variant rounded-full flex items-center justify-center mb-5">
                        <span class="font-display text-2xl text-on-surface-variant">2</span>
                    </div>
                    <h3 class="font-headline-md text-lg mb-2">Dodaj igrače i timove</h3>
                    <p class="text-sm text-on-surface-variant">Prijavi igrače jednom, koristi ih u svim ligama i turnirima te organizacije.</p>
                </div>
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 mx-auto bg-surface-container-low border-2 border-outline-variant rounded-full flex items-center justify-center mb-5">
                        <span class="font-display text-2xl text-on-surface-variant">3</span>
                    </div>
                    <h3 class="font-headline-md text-lg mb-2">Pokreni ligu ili turnir</h3>
                    <p class="text-sm text-on-surface-variant">Automatski raspored, grupna faza i knockout — ili klasična liga sa tabelom.</p>
                </div>
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 mx-auto bg-surface-container-low border-2 border-outline-variant rounded-full flex items-center justify-center mb-5">
                        <span class="font-display text-2xl text-on-surface-variant">4</span>
                    </div>
                    <h3 class="font-headline-md text-lg mb-2">Prati rezultate uživo</h3>
                    <p class="text-sm text-on-surface-variant">Tabele se ažuriraju automatski, igrači vide raspored i rezultate javno.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Za koga je / Zašto MojTurnir -->
    <section class="py-24 md:py-32 px-margin-mobile lg:px-gutter bg-surface">
        <div class="max-w-container-max mx-auto grid grid-cols-1 md:grid-cols-2 gap-16">
            <div>
                <h2 class="font-headline-lg text-headline-lg mb-8 tracking-tight">Za koga je MojTurnir</h2>
                <ul class="space-y-6">
                    <li class="flex gap-4">
                        <span class="material-symbols-outlined text-primary text-2xl shrink-0">groups</span>
                        <div>
                            <h3 class="font-semibold mb-1">Lokalne klubove</h3>
                            <p class="text-on-surface-variant text-sm">Interne lige, članstvo i raspored treninga na jednom mjestu.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="material-symbols-outlined text-primary text-2xl shrink-0">emoji_events</span>
                        <div>
                            <h3 class="font-semibold mb-1">Organizatore turnira</h3>
                            <p class="text-on-surface-variant text-sm">Grupna faza, knockout bracket i tabele bez ručnog računanja.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="material-symbols-outlined text-primary text-2xl shrink-0">workspace_premium</span>
                        <div>
                            <h3 class="font-semibold mb-1">Saveze i federacije</h3>
                            <p class="text-on-surface-variant text-sm">Centralizovan pregled više organizacija i njihovih takmičenja.</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div>
                <h2 class="font-headline-lg text-headline-lg mb-8 tracking-tight">Zašto MojTurnir</h2>
                <ul class="space-y-6">
                    <li class="flex gap-4">
                        <span class="material-symbols-outlined text-primary text-2xl shrink-0">check_circle</span>
                        <div>
                            <h3 class="font-semibold mb-1">Bez ručnog vođenja tabela</h3>
                            <p class="text-on-surface-variant text-sm">Poredak i statistike se računaju automatski poslije svakog meča.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="material-symbols-outlined text-primary text-2xl shrink-0">check_circle</span>
                        <div>
                            <h3 class="font-semibold mb-1">Javan pregled za igrače</h3>
                            <p class="text-on-surface-variant text-sm">Rasporedi i rezultati dostupni svima, bez prijave.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="material-symbols-outlined text-primary text-2xl shrink-0">check_circle</span>
                        <div>
                            <h3 class="font-semibold mb-1">Besplatno za osnovne potrebe</h3>
                            <p class="text-on-surface-variant text-sm">Bez ograničenja ako ne prelazite osnovni paket takmičenja i igrača.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- O aplikaciji / Tim -->
    <section id="o-aplikaciji" class="py-24 md:py-32 px-margin-mobile lg:px-gutter bg-surface-container-lowest border-y border-outline-variant">
        <div class="max-w-container-max mx-auto">
            <h2 class="font-headline-lg text-headline-lg mb-6 tracking-tight">O aplikaciji</h2>
            <p class="text-on-surface-variant text-lg max-w-2xl leading-relaxed mb-6">
                MojTurnir je platforma za organizaciju sportskih takmičenja — stoni tenis, tenis i padel.
                Cilj je da klubovi, lige i federacije jednostavno kreiraju takmičenja, prate rezultate uživo
                i imaju detaljnu analitiku, bez potrebe za tabelama i grupama za dopisivanje.
            </p>
            <div class="inline-flex items-start gap-3 border-l-2 border-primary pl-5 py-1 mb-16">
                <p class="text-sm text-on-surface-variant max-w-lg">
                    <span class="text-primary font-semibold">Besplatno</span> za osnovne potrebe — ako ne prelazite
                    osnovni paket takmičenja i igrača, aplikacija je potpuno besplatna.
                </p>
            </div>

            <h3 class="font-headline-md text-xl mb-8">Tim</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-surface-container-low border border-outline-variant rounded-2xl p-8">
                    <h4 class="font-headline-md text-lg mb-1">Ermin Selimović</h4>
                    <p class="text-primary text-sm font-medium mb-4">Dizajn &amp; Razvoj</p>
                    <p class="text-on-surface-variant text-sm leading-relaxed mb-6">
                        Full-stack developer i dizajner odgovoran za kompletnu izradu aplikacije,
                        od korisničkog interfejsa do serverske logike.
                    </p>
                    <div class="flex gap-3">
                        <a href="https://github.com/ermin1990" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-xs font-medium text-on-surface-variant hover:text-primary border border-outline-variant rounded-full px-3 py-1.5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                            GitHub
                        </a>
                        <a href="https://instagram.com/infinitycreative.agency" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-xs font-medium text-on-surface-variant hover:text-primary border border-outline-variant rounded-full px-3 py-1.5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            Instagram
                        </a>
                    </div>
                </div>
                <div class="bg-surface-container-low border border-outline-variant rounded-2xl p-8">
                    <h4 class="font-headline-md text-lg mb-1">Sanel Moranjkić</h4>
                    <p class="text-primary text-sm font-medium mb-4">Analiza &amp; Takmičarska Logika</p>
                    <p class="text-on-surface-variant text-sm leading-relaxed">
                        Tehnička podrška u razvoju aplikacije.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA -->
    <section class="py-24 md:py-36 px-margin-mobile lg:px-gutter bg-surface-dim border-t border-outline-variant text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-primary/10 via-surface-dim to-surface-dim z-0 pointer-events-none"></div>
        <div class="max-w-container-max mx-auto relative z-10">
            <h2 class="font-display text-3xl md:text-headline-lg mb-10 tracking-tighter">Spremni ste da podignete takmičenje na sljedeći nivo?</h2>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ auth()->user()->isOrganizerOrStaff() ? route('dashboard') : route('player.dashboard') }}" class="bg-primary-container text-on-primary-container rounded-xl px-10 py-5 font-label-bold text-lg hover:bg-primary transition-all shadow-[0_0_30px_rgba(87,241,219,0.25)] hover:shadow-[0_0_45px_rgba(87,241,219,0.4)] transform hover:-translate-y-1">
                        Moj nalog
                    </a>
                @else
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-primary-container text-on-primary-container rounded-xl px-10 py-5 font-label-bold text-lg hover:bg-primary transition-all shadow-[0_0_30px_rgba(87,241,219,0.25)] hover:shadow-[0_0_45px_rgba(87,241,219,0.4)] transform hover:-translate-y-1">
                            Započni besplatno
                        </a>
                    @endif
                @endauth
                <a href="{{ route('competitions.index') }}" class="inline-flex items-center justify-center gap-2 border-2 border-outline-variant text-on-surface rounded-xl px-10 py-5 font-label-bold text-lg hover:border-primary hover:text-primary transition-all transform hover:-translate-y-1">
                    Pregledaj takmičenja
                </a>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="bg-surface-container-lowest w-full py-16 border-t border-outline-variant">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 px-margin-mobile lg:px-gutter max-w-container-max mx-auto">
        <div>
            <div class="font-display text-headline-md text-primary mb-4 tracking-tighter uppercase">MojTurnir</div>
            <p class="text-on-surface-variant text-body-sm leading-relaxed">© {{ date('Y') }} MojTurnir.<br>Sva prava zadržana.</p>
        </div>
        <div>
            <h4 class="font-label-bold text-primary mb-5 uppercase tracking-wider">Takmičenja</h4>
            <a class="block text-on-surface-variant text-body-sm hover:text-primary transition-colors mb-3" href="{{ route('competitions.index') }}">Sva takmičenja</a>
            <a class="block text-on-surface-variant text-body-sm hover:text-primary transition-colors mb-3" href="{{ route('login') }}">Prijava</a>
            @if (Route::has('register'))
                <a class="block text-on-surface-variant text-body-sm hover:text-primary transition-colors mb-3" href="{{ route('register') }}">Registracija</a>
            @endif
        </div>
        <div>
            <h4 class="font-label-bold text-primary mb-5 uppercase tracking-wider">Podrška</h4>
            <a class="block text-on-surface-variant text-body-sm hover:text-primary transition-colors mb-3" href="{{ route('feedback.create') }}">Kontaktiraj nas</a>
        </div>
        <div>
            <h4 class="font-label-bold text-primary mb-5 uppercase tracking-wider">Verzija</h4>
            <span class="block text-on-surface-variant text-body-sm">v2</span>
        </div>
    </div>
</footer>

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
