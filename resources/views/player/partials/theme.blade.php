{{--
    Recolors the "Moje lige" (player) area to the same teal/dark palette and
    Montserrat/Inter fonts used across the public pages, without touching
    components/app-layout.blade.php (shared with the organizer/admin areas)
    or changing any font sizes/spacing. Works by overriding the same CSS
    custom properties app-layout.blade.php defines - since each page render
    is a fresh document, this can't leak into other authenticated pages.
    Include once per view: @push('styles') @include('player.partials.theme') @endpush
--}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Inter:wght@400;500;600;700&display=swap">
<style>
    body { font-family: 'Inter', ui-sans-serif, sans-serif !important; }

    :root {
        --bg-primary: #0b0e14;
        --bg-secondary: #191c22;
        --bg-tertiary: #272a31;
        --bg-card: rgba(25, 28, 34, 0.75);
        --bg-card-solid: #191c22;
        --bg-accent: rgba(11, 14, 20, 0.95);
        --bg-hover: rgba(39, 42, 49, 0.6);

        --text-primary: #e1e2eb;
        --text-secondary: #bacac5;
        --text-tertiary: #8b9a96;
        --text-muted: #667572;

        --border-primary: rgba(87, 241, 219, 0.16);
        --border-secondary: rgba(255, 255, 255, 0.07);
        --border-accent: rgba(87, 241, 219, 0.3);

        --accent-blue: #57f1db;
        --accent-green-solid: #16a34a;

        --glow-purple: radial-gradient(ellipse 900px 700px at 88% -5%, rgba(45, 212, 191, 0.25), transparent 60%);
    }

    .mt-input:focus { border-color: var(--accent-blue) !important; box-shadow: 0 0 0 2px rgba(87, 241, 219, 0.2); }
    .mt-input::placeholder { color: var(--text-muted); }
    .mt-comp-card:hover { border-color: var(--border-accent) !important; }
</style>
