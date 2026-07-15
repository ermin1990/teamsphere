@php
    $navItems = [
        [
            'label' => 'Moje lige',
            'href'  => route('player.dashboard'),
            'active' => request()->routeIs('player.dashboard') || request()->routeIs('player.leagues.show') || request()->routeIs('player.matches.create'),
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5.5l1.2 1.2M19 5.5l-1.2 1.2"/>',
        ],
        [
            'label' => 'Mečevi',
            'href'  => route('player.dashboard.matches'),
            'active' => request()->routeIs('player.dashboard.matches') || request()->routeIs('player.matches.result.*'),
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.5 15.5l1.8 1.8 3.2-3.4"/>',
        ],
        [
            'label' => 'Takmičenja',
            'href'  => route('player.leagues.index'),
            'active' => request()->routeIs('player.leagues.index'),
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>',
        ],
        [
            'label' => 'Nalog',
            'href'  => route('profile.edit'),
            'active' => request()->routeIs('profile.*'),
            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.5 8.5a3.5 3.5 0 11-7 0 3.5 3.5 0 017 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 20a7.5 7.5 0 0115 0"/><circle cx="12" cy="12" r="9.5" stroke-width="1.6"/>',
        ],
    ];
@endphp

{{-- Spacer so page content is never hidden behind the fixed bar (mobile only) --}}
<div class="h-[4.75rem] sm:hidden" aria-hidden="true"></div>

<nav class="sm:hidden fixed bottom-0 inset-x-0 z-50"
     style="background: var(--bg-accent); border-top: 1px solid var(--border-primary); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); padding-bottom: env(safe-area-inset-bottom); box-shadow: 0 -8px 24px rgba(0,0,0,0.35);">
    <div class="grid grid-cols-4">
        @foreach($navItems as $item)
            <a href="{{ $item['href'] }}"
               class="relative flex flex-col items-center justify-center gap-1 py-2.5 transition-colors active:scale-95"
               style="color: {{ $item['active'] ? 'var(--accent-blue)' : 'var(--text-muted)' }};">
                @if($item['active'])
                    <span class="absolute top-0 h-[3px] w-9 rounded-full" style="background: var(--accent-blue);"></span>
                @endif
                <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                <span class="text-[10px] font-semibold tracking-tight leading-none">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
