{{--
    Tab strip shown above the body on a competition's public page (and the
    player's own view of it, via player.leagues.show). Expects $competition
    and $activeTab ('overview'|'announcements'|'rules'). Route names default
    to the public ones but can be overridden (e.g. by the player view).
--}}
@php
    $showRouteName = $showRouteName ?? 'competitions.show';
    $announcementsRouteName = $announcementsRouteName ?? 'competitions.announcements';
    $rulesRouteName = $rulesRouteName ?? 'competitions.rules';
@endphp
<div class="flex items-center gap-1 mb-6 lg:mb-8 border-b border-outline-variant">
    <a href="{{ route($showRouteName, $competition) }}"
       class="px-4 py-3 font-label-bold text-sm border-b-2 transition-colors {{ ($activeTab ?? 'overview') === 'overview' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface' }}">
        Pregled
    </a>
    <a href="{{ route($announcementsRouteName, $competition) }}"
       class="px-4 py-3 font-label-bold text-sm border-b-2 transition-colors flex items-center gap-1.5 {{ ($activeTab ?? 'overview') === 'announcements' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface' }}">
        <span class="material-symbols-outlined text-[18px]">campaign</span> Obavijesti
    </a>
    <a href="{{ route($rulesRouteName, $competition) }}"
       class="px-4 py-3 font-label-bold text-sm border-b-2 transition-colors flex items-center gap-1.5 {{ ($activeTab ?? 'overview') === 'rules' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface' }}">
        <span class="material-symbols-outlined text-[18px]">gavel</span> Pravila
    </a>
</div>
