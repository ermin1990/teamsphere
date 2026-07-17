<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Zaboravljena lozinka - MojTurnir</title>

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
                    "surface-container-lowest": "#0b0e14", "surface-dim": "#10131a", "surface": "#10131a",
                    "surface-container-low": "#191c22", "surface-container": "#1d2026", "surface-container-high": "#272a31",
                    "surface-container-highest": "#32353c", "surface-variant": "#32353c", "surface-bright": "#363940",
                    "on-surface": "#e1e2eb", "on-surface-variant": "#bacac5", "outline": "#859490", "outline-variant": "#3c4a46",
                    "primary": "#57f1db", "primary-container": "#2dd4bf", "on-primary": "#003731", "on-primary-container": "#00574d",
                    "secondary": "#ffb95f", "secondary-container": "#ee9800", "on-secondary-container": "#5b3800",
                    "tertiary-container": "#b3bed5", "on-tertiary-container": "#424d61", "error": "#ffb4ab",
                },
                borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
                spacing: { gutter: "24px", "margin-mobile": "16px", "container-max": "1280px", base: "8px" },
                fontFamily: {
                    display: ["Montserrat"], "headline-md": ["Montserrat"], "headline-lg": ["Montserrat"],
                    "body-md": ["Inter"], "body-sm": ["Inter"], "label-bold": ["Inter"],
                },
                fontSize: {
                    display: ["40px", { lineHeight: "1.1", letterSpacing: "-0.02em", fontWeight: "800" }],
                    "headline-md": ["20px", { lineHeight: "1.3", fontWeight: "700" }],
                    "headline-lg": ["28px", { lineHeight: "1.2", letterSpacing: "-0.01em", fontWeight: "700" }],
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
    body { background-color: #0b0e14; color: #e1e2eb; overflow-x: hidden; }
    .login-glow { box-shadow: 0 10px 40px rgba(87, 241, 219, 0.08); }
</style>
</head>
<body class="font-body-md bg-surface-container-lowest text-on-surface min-h-screen flex flex-col items-center justify-center">

<div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-[-10%] right-[-10%] w-[80%] h-[40%] bg-primary/10 blur-[120px] rounded-full"></div>
    <div class="absolute bottom-[-5%] left-[-5%] w-[60%] h-[30%] bg-secondary/5 blur-[100px] rounded-full"></div>
</div>

<main class="relative z-10 w-full max-w-[440px] px-margin-mobile py-12 flex flex-col items-center">
    <header class="w-full text-center mb-8">
        <a href="{{ route('home') }}" class="font-display text-display text-primary tracking-tighter block uppercase">MojTurnir</a>
        <p class="font-headline-md text-headline-md text-on-surface-variant mt-1">Zaboravljena lozinka?</p>
    </header>

    <section class="w-full bg-surface-container p-6 rounded-xl border border-outline-variant login-glow">
        <p class="text-on-surface-variant text-sm mb-6">Nema problema. Unesite email adresu vašeg naloga i poslat ćemo vam link za resetovanje lozinke.</p>

        @if (session('status'))
            <div class="mb-6 text-sm font-medium text-primary">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
            @csrf

            <div class="space-y-2">
                <label for="email" class="font-label-bold text-label-bold text-on-surface-variant ml-1 uppercase">Email adresa</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">mail</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                           placeholder="ime@email.com"
                           class="w-full h-14 pl-12 pr-4 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all outline-none">
                </div>
                <x-input-error :messages="$errors->get('email')" class="text-error text-sm" />
            </div>

            <button type="submit"
                    class="mt-2 w-full h-16 bg-primary text-on-primary font-headline-md rounded-lg flex items-center justify-center gap-2 hover:opacity-90 transition-all duration-300 active:scale-95 shadow-[0_4px_20px_rgba(87,241,219,0.3)]">
                Pošalji link za resetovanje
                <span class="material-symbols-outlined">arrow_forward</span>
            </button>
        </form>
    </section>

    <footer class="w-full mt-8 flex flex-col items-center gap-4">
        <div class="font-body-sm text-body-sm text-on-surface-variant">
            Sjetili ste se lozinke?
            <a href="{{ route('login') }}" class="text-primary font-label-bold hover:underline ml-1">Prijavite se</a>
        </div>
    </footer>
</main>
</body>
</html>
