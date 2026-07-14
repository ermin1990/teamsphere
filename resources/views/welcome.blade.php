<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <title>{{ config('app.name', 'MojTurnir') }}</title>
        <meta name="description" content="MojTurnir — organizuj lige i turnire za stoni tenis, tenis i padel. Rasporedi, rezultati i tabele na jednom mjestu.">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=unbounded:500,600,700,800|manrope:400,500,600,700,800" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>

        <style>
            :root {
                --bg: #121309;
                --bg-raised: #1A1C10;
                --ink: #F4F2E6;
                --ink-dim: #A7AB90;
                --accent: #D7FF3F;
                --accent-ink: #171A05;
                --line: #2C2F1D;
            }
            html { scroll-behavior: smooth; }
            body {
                background: var(--bg);
                color: var(--ink);
                font-family: 'Manrope', ui-sans-serif, sans-serif;
            }
            .f-display {
                font-family: 'Unbounded', ui-sans-serif, sans-serif;
                text-wrap: balance;
                letter-spacing: -0.01em;
            }
            .text-ink-dim { color: var(--ink-dim); }
            .bg-raised { background: var(--bg-raised); }
            .border-line { border-color: var(--line); }
            .text-accent { color: var(--accent); }
            .bg-accent { background: var(--accent); }
            .text-accent-ink { color: var(--accent-ink); }

            ::selection { background: var(--accent); color: var(--accent-ink); }

            .mark {
                background-image: linear-gradient(var(--accent), var(--accent));
                background-repeat: no-repeat;
                background-size: 100% 0.28em;
                background-position: 0 88%;
                padding: 0 0.05em;
            }

            #courtCanvas { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0.5; }

            .btn-solid {
                background: var(--accent);
                color: var(--accent-ink);
            }
            .btn-solid:hover { filter: brightness(1.08); }

            .num-badge {
                font-family: 'Unbounded', ui-sans-serif, sans-serif;
                border: 1px solid var(--line);
            }

            @media (prefers-reduced-motion: reduce) {
                html { scroll-behavior: auto; }
            }
        </style>
    </head>
    <body class="antialiased">

        <!-- Nav -->
        <header class="sticky top-0 z-50 bg-[#121309]/85 backdrop-blur-md border-b border-line">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="/" class="f-display text-lg font-bold tracking-tight">
                    Moj<span class="text-accent">Turnir</span>
                </a>
                <nav class="hidden sm:flex items-center gap-8 text-sm text-ink-dim">
                    <a href="#sportovi" class="hover:text-ink transition-colors">Sportovi</a>
                    <a href="#kako-radi" class="hover:text-ink transition-colors">Kako radi</a>
                    <a href="{{ route('public.leagues.index') }}" class="hover:text-ink transition-colors">Takmičenja</a>
                    <a href="#o-aplikaciji" class="hover:text-ink transition-colors">O aplikaciji</a>
                </nav>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-solid text-sm font-semibold px-4 py-2 rounded-full transition-all">Moj Dashboard</a>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="hidden sm:inline text-sm text-ink-dim hover:text-ink transition-colors">Prijava</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-solid text-sm font-semibold px-4 py-2 rounded-full transition-all">Počni besplatno</a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero -->
        <section class="relative overflow-hidden border-b border-line">
            <canvas id="courtCanvas"></canvas>
            <div class="relative max-w-6xl mx-auto px-6 pt-20 pb-24 md:pt-28 md:pb-32">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-ink-dim border border-line rounded-full px-3 py-1.5 mb-8">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                        Stoni tenis · Tenis · Padel
                    </div>

                    <h1 class="f-display text-4xl sm:text-5xl md:text-6xl font-bold leading-[1.05] mb-6">
                        Tvoja liga.<br>
                        Tvoj <span class="mark text-accent-ink" style="color: var(--ink)">turnir</span>.<br>
                        Rezultat za rezultatom.
                    </h1>

                    <p class="text-lg md:text-xl text-ink-dim max-w-xl mb-10">
                        MojTurnir organizuje rasporede, prati rezultate uživo i vodi tabele za tvoj klub ili ligu —
                        bez tabela u Excelu i grupa na Viberu.
                    </p>

                    <div class="flex flex-wrap items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-solid font-semibold px-7 py-3.5 rounded-full transition-all">
                                Moj Dashboard
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-solid font-semibold px-7 py-3.5 rounded-full transition-all">
                                    Kreiraj organizaciju
                                </a>
                            @endif
                        @endauth
                        <a href="{{ route('public.leagues.index') }}" class="inline-flex items-center gap-2 font-semibold px-7 py-3.5 rounded-full border border-line hover:border-ink-dim transition-all">
                            Pregledaj takmičenja
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sportovi -->
        <section id="sportovi" class="max-w-6xl mx-auto px-6 py-20 md:py-28">
            <div class="flex items-end justify-between mb-10 gap-6">
                <h2 class="f-display text-2xl md:text-3xl font-semibold">Jedan sport po organizaciji.<br class="hidden sm:block"> Biraš pri kreiranju.</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-px bg-line rounded-2xl overflow-hidden border border-line">
                <div class="bg-[#121309] p-8 hover:bg-raised transition-colors">
                    <div class="text-4xl mb-5">🏓</div>
                    <h3 class="f-display text-lg font-semibold mb-2">Stoni Tenis</h3>
                    <p class="text-ink-dim text-sm leading-relaxed">Pojedinačno i ekipno, grupna faza i knockout, uživo praćenje seta po seta.</p>
                </div>
                <div class="bg-[#121309] p-8 hover:bg-raised transition-colors">
                    <div class="text-4xl mb-5">🎾</div>
                    <h3 class="f-display text-lg font-semibold mb-2">Tenis</h3>
                    <p class="text-ink-dim text-sm leading-relaxed">Lige i turniri, pojedinačno ili ekipno, sa punom istorijom mečeva.</p>
                </div>
                <div class="bg-[#121309] p-8 hover:bg-raised transition-colors">
                    <div class="text-4xl mb-5">🏸</div>
                    <h3 class="f-display text-lg font-semibold mb-2">Padel</h3>
                    <p class="text-ink-dim text-sm leading-relaxed">Klub protiv kluba ili par protiv para — sastav se bira za svaki meč.</p>
                </div>
            </div>
        </section>

        <!-- Kako radi -->
        <section id="kako-radi" class="border-y border-line bg-raised">
            <div class="max-w-6xl mx-auto px-6 py-20 md:py-28">
                <h2 class="f-display text-2xl md:text-3xl font-semibold mb-14">Od registracije do prve utakmice</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    <div>
                        <div class="num-badge w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold mb-4">01</div>
                        <h3 class="font-semibold mb-2">Kreiraj organizaciju</h3>
                        <p class="text-ink-dim text-sm leading-relaxed">Izaberi sport — stoni tenis, tenis ili padel. Ostaje isti kroz cijelu organizaciju.</p>
                    </div>
                    <div>
                        <div class="num-badge w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold mb-4">02</div>
                        <h3 class="font-semibold mb-2">Dodaj igrače i timove</h3>
                        <p class="text-ink-dim text-sm leading-relaxed">Prijavi igrače jednom, koristi ih u svim ligama i turnirima te organizacije.</p>
                    </div>
                    <div>
                        <div class="num-badge w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold mb-4">03</div>
                        <h3 class="font-semibold mb-2">Pokreni ligu ili turnir</h3>
                        <p class="text-ink-dim text-sm leading-relaxed">Automatski raspored, grupna faza i knockout - ili klasična liga sa tabelom.</p>
                    </div>
                    <div>
                        <div class="num-badge w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold mb-4">04</div>
                        <h3 class="font-semibold mb-2">Prati rezultate uživo</h3>
                        <p class="text-ink-dim text-sm leading-relaxed">Tabele se ažuriraju automatski, igrači vide raspored i rezultate javno.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Za koga je -->
        <section class="max-w-6xl mx-auto px-6 py-20 md:py-28">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                <div>
                    <h2 class="f-display text-2xl md:text-3xl font-semibold mb-8">Za koga je MojTurnir</h2>
                    <ul class="space-y-6">
                        <li class="flex gap-4">
                            <span class="text-2xl">🏓</span>
                            <div>
                                <h3 class="font-semibold mb-1">Lokalne klubove</h3>
                                <p class="text-ink-dim text-sm">Interne lige, članstvo i raspored treninga na jednom mjestu.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span class="text-2xl">🏆</span>
                            <div>
                                <h3 class="font-semibold mb-1">Organizatore turnira</h3>
                                <p class="text-ink-dim text-sm">Grupna faza, knockout bracket i tabele bez ručnog računanja.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <span class="text-2xl">🎯</span>
                            <div>
                                <h3 class="font-semibold mb-1">Saveze i federacije</h3>
                                <p class="text-ink-dim text-sm">Centralizovan pregled više organizacija i njihovih takmičenja.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="f-display text-2xl md:text-3xl font-semibold mb-8">Zašto MojTurnir</h2>
                    <ul class="space-y-6">
                        <li class="flex gap-4">
                            <svg class="w-5 h-5 text-accent flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <div>
                                <h3 class="font-semibold mb-1">Bez ručnog vođenja tabela</h3>
                                <p class="text-ink-dim text-sm">Poredak i statistike se računaju automatski poslije svakog meča.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <svg class="w-5 h-5 text-accent flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <div>
                                <h3 class="font-semibold mb-1">Javan pregled za igrače</h3>
                                <p class="text-ink-dim text-sm">Rasporedi i rezultati dostupni svima, bez prijave.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <svg class="w-5 h-5 text-accent flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <div>
                                <h3 class="font-semibold mb-1">Besplatno za osnovne potrebe</h3>
                                <p class="text-ink-dim text-sm">Bez ograničenja ako ne prelazite osnovni paket takmičenja i igrača.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- O aplikaciji -->
        <section id="o-aplikaciji" class="border-t border-line bg-raised">
            <div class="max-w-6xl mx-auto px-6 py-20 md:py-28">
                <h2 class="f-display text-2xl md:text-3xl font-semibold mb-6">O aplikaciji</h2>
                <p class="text-ink-dim text-lg max-w-2xl leading-relaxed mb-6">
                    MojTurnir je platforma za organizaciju sportskih takmičenja — stoni tenis, tenis i padel.
                    Cilj je da klubovi, lige i federacije jednostavno kreiraju takmičenja, prate rezultate uživo
                    i imaju detaljnu analitiku, bez potrebe za tabelama i grupama za dopisivanje.
                </p>
                <div class="inline-flex items-start gap-3 border-l-2 border-accent pl-5 py-1 mb-16">
                    <p class="text-sm text-ink-dim max-w-lg">
                        <span class="text-accent font-semibold">Besplatno</span> za osnovne potrebe — ako ne prelazite
                        osnovni paket takmičenja i igrača, aplikacija je potpuno besplatna.
                    </p>
                </div>

                <h3 class="f-display text-xl font-semibold mb-8">Tim</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-line rounded-2xl overflow-hidden border border-line">
                    <div class="bg-[#121309] p-8">
                        <h4 class="f-display font-semibold text-lg mb-1">Ermin Selimović</h4>
                        <p class="text-accent text-sm font-medium mb-4">Dizajn &amp; Razvoj</p>
                        <p class="text-ink-dim text-sm leading-relaxed mb-6">
                            Full-stack developer i dizajner odgovoran za kompletnu izradu aplikacije,
                            od korisničkog interfejsa do serverske logike.
                        </p>
                        <div class="flex gap-3">
                            <a href="https://github.com/ermin1990" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-xs font-medium text-ink-dim hover:text-ink border border-line rounded-full px-3 py-1.5 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                                GitHub
                            </a>
                            <a href="https://instagram.com/infinitycreative.agency" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-xs font-medium text-ink-dim hover:text-ink border border-line rounded-full px-3 py-1.5 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                Instagram
                            </a>
                        </div>
                    </div>
                    <div class="bg-[#121309] p-8">
                        <h4 class="f-display font-semibold text-lg mb-1">Sanel Moranjkić</h4>
                        <p class="text-accent text-sm font-medium mb-4">Analiza &amp; Takmičarska Logika</p>
                        <p class="text-ink-dim text-sm leading-relaxed">
                            Tehnička podrška u razvoju aplikacije.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-line">
            <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-ink-dim">
                <span>© 2026 MojTurnir. Sva prava zadržana.</span>
                <span>Verzija <span class="text-ink font-medium">v2</span></span>
            </div>
        </footer>

        <script>
            (function () {
                var canvas = document.getElementById('courtCanvas');
                if (!canvas) return;
                var ctx = canvas.getContext('2d');
                var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                var w, h, dpr = Math.min(window.devicePixelRatio || 1, 2);

                function resize() {
                    w = canvas.parentElement.offsetWidth;
                    h = canvas.parentElement.offsetHeight;
                    canvas.width = w * dpr;
                    canvas.height = h * dpr;
                    canvas.style.width = w + 'px';
                    canvas.style.height = h + 'px';
                    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                }

                function drawCourt(t) {
                    ctx.clearRect(0, 0, w, h);
                    ctx.strokeStyle = 'rgba(215, 255, 63, 0.10)';
                    ctx.lineWidth = 1;
                    var spacing = 64;
                    var shift = reduceMotion ? 0 : (t * 0.012) % spacing;
                    for (var x = -spacing + shift; x < w + h; x += spacing) {
                        ctx.beginPath();
                        ctx.moveTo(x, 0);
                        ctx.lineTo(x - h, h);
                        ctx.stroke();
                    }
                    // baseline accents
                    ctx.strokeStyle = 'rgba(215, 255, 63, 0.16)';
                    ctx.beginPath();
                    ctx.moveTo(0, h * 0.82);
                    ctx.lineTo(w, h * 0.82);
                    ctx.stroke();
                }

                function frame(t) {
                    drawCourt(t);
                    if (!reduceMotion) requestAnimationFrame(frame);
                }

                window.addEventListener('resize', resize);
                resize();
                if (reduceMotion) {
                    drawCourt(0);
                } else {
                    requestAnimationFrame(frame);
                }
            })();
        </script>
    </body>
</html>
