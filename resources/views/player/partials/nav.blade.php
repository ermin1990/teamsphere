@php
    $tabs = [
        ['route' => 'player.dashboard', 'label' => 'Moja takmičenja'],
        ['route' => 'player.dashboard.matches', 'label' => 'Moji mečevi'],
    ];
@endphp
{{-- Top tabs for desktop; on mobile the fixed bottom bar handles navigation --}}
<div class="hidden sm:flex gap-1 mb-8" style="border-bottom: 1px solid var(--border-primary);">
    @foreach($tabs as $tab)
        @php $active = request()->routeIs($tab['route']); @endphp
        <a href="{{ route($tab['route']) }}"
           class="px-4 py-2.5 text-sm font-semibold border-b-2 -mb-px transition-colors"
           style="{{ $active
                ? 'border-color: var(--accent-blue); color: var(--text-primary);'
                : 'border-color: transparent; color: var(--text-tertiary);' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
