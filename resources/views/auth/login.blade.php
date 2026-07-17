<!DOCTYPE html>
<html class="dark" lang="bs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Prijava - MojTurnir</title>

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
        <p class="font-headline-md text-headline-md text-on-surface-variant mt-1">Prijavite se na vaš nalog</p>
    </header>

    <section class="w-full bg-surface-container p-6 rounded-xl border border-outline-variant login-glow">
        @if (session('status'))
            <div class="mb-6 text-sm font-medium text-primary">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Email -->
            <div class="space-y-2">
                <label for="email" class="font-label-bold text-label-bold text-on-surface-variant ml-1 uppercase">Email adresa</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">mail</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           placeholder="ime@email.com"
                           class="w-full h-14 pl-12 pr-4 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all outline-none">
                </div>
                <x-input-error :messages="$errors->get('email')" class="text-error text-sm" />
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <div class="flex justify-between items-center px-1">
                    <label for="password" class="font-label-bold text-label-bold text-on-surface-variant uppercase">Lozinka</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="font-label-bold text-label-bold text-primary hover:underline uppercase">Zaboravljena?</a>
                    @endif
                </div>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-primary transition-colors">lock</span>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full h-14 pl-12 pr-12 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-body-md focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all outline-none">
                    <button type="button" id="toggle-password" class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface">
                        <span class="material-symbols-outlined" id="toggle-password-icon">visibility</span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="text-error text-sm" />
            </div>

            <!-- Remember me -->
            <div class="flex items-center gap-3 px-1 py-1">
                <input type="checkbox" id="remember" name="remember" class="w-5 h-5 rounded border-outline-variant bg-surface-container-lowest text-primary focus:ring-primary">
                <label for="remember" class="font-body-sm text-body-sm text-on-surface-variant">Zapamti me 30 dana</label>
            </div>

            <!-- Submit -->
            <button type="submit" id="login-button"
                    class="mt-2 w-full h-16 bg-primary text-on-primary font-headline-md rounded-lg flex items-center justify-center gap-2 hover:opacity-90 transition-all duration-300 active:scale-95 shadow-[0_4px_20px_rgba(87,241,219,0.3)]">
                <span id="login-text" class="flex items-center justify-center gap-2">
                    Prijavi se
                    <span class="material-symbols-outlined">arrow_forward</span>
                </span>
                <span id="login-loader" class="hidden items-center justify-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Prijava u toku...
                </span>
            </button>
        </form>
    </section>

    @if (Route::has('register'))
        <footer class="w-full mt-8 flex flex-col items-center gap-4">
            <div class="font-body-sm text-body-sm text-on-surface-variant">
                Nemate nalog?
                <a href="{{ route('register') }}" class="text-primary font-label-bold hover:underline ml-1">Registrujte se</a>
            </div>
        </footer>
    @endif
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form[action*="login"]');
        const loginText = document.getElementById('login-text');
        const loginLoader = document.getElementById('login-loader');
        const loginButton = document.getElementById('login-button');

        form.addEventListener('submit', function () {
            loginText.classList.add('hidden');
            loginLoader.classList.remove('hidden');
            loginLoader.classList.add('flex');
            loginButton.disabled = true;
            loginButton.classList.add('opacity-75');
        });

        const toggleBtn = document.getElementById('toggle-password');
        const toggleIcon = document.getElementById('toggle-password-icon');
        const passwordInput = document.getElementById('password');
        toggleBtn.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
        });
    });
</script>
</body>
</html>
