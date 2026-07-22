{{--
    Shared competition hero (sport icon, status/type/season badges, title,
    organization/sport/city line) - used identically across all tabs of a
    competition's public page (Pregled/Obavijesti/Pravila) and the player's
    own view. Expects $competition, $organization.
--}}
@php
    $sportIcon = $sportIcon ?? function ($sport) {
        $n = mb_strtolower($sport?->name ?? '');
        if (str_contains($n, 'fudbal')) return 'sports_soccer';
        if (str_contains($n, 'košark') || str_contains($n, 'kosark')) return 'sports_basketball';
        if (str_contains($n, 'odbojk')) return 'sports_volleyball';
        return 'sports_tennis';
    };
    $isTournament = $isTournament ?? ($competition->type === 'tournament');
@endphp
<section class="-mx-margin-mobile lg:mx-0 mb-6 lg:mb-10 bg-surface-container-low lg:p-8 border-y lg:border border-outline-variant lg:rounded-xl relative overflow-hidden">
    <div class="absolute top-0 right-0 p-6 lg:p-8 opacity-10 hidden sm:block">
        <span class="material-symbols-outlined text-[80px] lg:text-[120px]">{{ $sportIcon($competition->sport) }}</span>
    </div>
    <div class="relative z-10 px-margin-mobile py-5 lg:p-0">
        <div class="flex flex-wrap items-center gap-2 mb-3 lg:mb-4">
            <span class="bg-primary/20 text-primary px-3 py-1 rounded-full text-label-bold uppercase">
                @if($competition->status === 'completed') Završeno
                @elseif($competition->status === 'in_progress') U toku
                @elseif($competition->status === 'active') Aktivno
                @elseif($competition->status === 'cancelled') Otkazano
                @else Zakazano @endif
            </span>
            <span class="bg-secondary/20 text-secondary px-3 py-1 rounded-full text-label-bold uppercase">{{ $isTournament ? 'Turnir' : 'Liga' }}</span>
            @if($competition->season)
                <span class="bg-surface-container-highest text-on-surface-variant px-3 py-1 rounded-full text-label-bold uppercase">{{ $competition->season->name }}</span>
            @endif
            @if($competition->sets_to_win)
                <span class="bg-surface-container-highest text-on-surface-variant px-3 py-1 rounded-full text-label-bold uppercase">Do {{ $competition->sets_to_win }} dobijena</span>
            @endif
        </div>
        <h1 class="font-display text-3xl lg:text-display mb-2 truncate">{{ $competition->name }}</h1>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-on-surface-variant text-sm lg:text-body-md">
            <a href="{{ route('competitions.organization', $organization) }}" class="flex items-center gap-1 hover:text-primary transition-colors"><span class="material-symbols-outlined text-body-sm">group</span> {{ $organization->name }}</a>
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-body-sm">{{ $sportIcon($competition->sport) }}</span> {{ $competition->sport->name }}</span>
            @if($competition->effectiveCity())
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-body-sm">location_on</span> {{ $competition->effectiveCity()->name }}</span>
            @endif
        </div>
    </div>
</section>
