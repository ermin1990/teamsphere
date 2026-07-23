{{--
    Single shared mobile bottom nav for every public "takmicenja"/league/
    organization page (Pregled, Obavijesti, Pravila, the /takmicenja listing,
    org page, ...) - included everywhere instead of each page hardcoding its
    own copy, so it's always the same items in the same order regardless of
    which page you're on. Active state is route-based, not hardcoded.
--}}
<nav class="fixed bottom-0 w-full z-50 lg:hidden rounded-t-xl bg-surface-container-highest border-t border-outline-variant shadow-lg flex justify-around items-center h-16 px-4">
    <a class="flex flex-col items-center justify-center {{ request()->routeIs('home') ? 'text-primary' : 'text-on-surface-variant' }}" href="{{ route('home') }}">
        <span class="material-symbols-outlined">home</span><span class="text-[10px] font-label-bold">Home</span>
    </a>
    <a class="flex flex-col items-center justify-center {{ request()->routeIs('competitions.*') ? 'text-primary' : 'text-on-surface-variant' }}" href="{{ route('competitions.index') }}">
        <span class="material-symbols-outlined">emoji_events</span><span class="text-[10px] font-label-bold">Takmičenja</span>
    </a>
    <a class="flex flex-col items-center justify-center {{ request()->routeIs('venues.public.*') ? 'text-primary' : 'text-on-surface-variant' }}" href="{{ route('venues.public.index') }}">
        <span class="material-symbols-outlined">location_on</span><span class="text-[10px] font-label-bold">Tereni</span>
    </a>
    @auth
        <a class="flex flex-col items-center justify-center text-on-surface-variant" href="{{ auth()->user()->isOrganizerOrStaff() ? route('dashboard') : route('player.dashboard') }}">
            <span class="material-symbols-outlined">person</span><span class="text-[10px] font-label-bold">Nalog</span>
        </a>
    @else
        <a class="flex flex-col items-center justify-center text-on-surface-variant" href="{{ route('login') }}">
            <span class="material-symbols-outlined">login</span><span class="text-[10px] font-label-bold">Prijava</span>
        </a>
    @endauth
</nav>
