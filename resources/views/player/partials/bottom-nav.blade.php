{{--
    Single shared mobile bottom nav for every player-facing page (dashboard,
    takmicenja, mecevi, ...) - included everywhere instead of each page
    hardcoding its own copy, so it's always the same items in the same
    order. Active state is route-based, not hardcoded per page.
--}}
<nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 py-2 pb-[env(safe-area-inset-bottom)] bg-surface-container-high rounded-t-xl border-t border-outline-variant shadow-lg">
    <a href="{{ route('player.dashboard') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('player.dashboard') ? 'text-primary' : 'text-on-surface-variant' }} active:scale-90 transition-transform">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="font-label-bold text-[10px]">Dashboard</span>
    </a>
    <a href="{{ route('player.dashboard.matches') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('player.dashboard.matches') || request()->routeIs('player.matches.*') ? 'text-primary' : 'text-on-surface-variant' }} active:scale-90 transition-transform">
        <span class="material-symbols-outlined">sports_tennis</span>
        <span class="font-label-bold text-[10px]">Mečevi</span>
    </a>
    <a href="{{ route('player.leagues.index') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('player.leagues.*') ? 'text-primary' : 'text-on-surface-variant' }} active:scale-90 transition-transform">
        <span class="material-symbols-outlined">emoji_events</span>
        <span class="font-label-bold text-[10px]">Takmičenja</span>
    </a>
    <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('profile.*') ? 'text-primary' : 'text-on-surface-variant' }} active:scale-90 transition-transform">
        <span class="material-symbols-outlined">person</span>
        <span class="font-label-bold text-[10px]">Nalog</span>
    </a>
</nav>
