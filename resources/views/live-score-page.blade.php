<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-base sm:text-2xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent truncate">
            Live Score ·
            @if($match->league ?? false)
                {{ $match->league->name }}
            @else
                {{ $match->competition->name ?? 'Competition' }}
            @endif
        </h2>
    </x-slot>

    @php
        // Tenis/Padel (sets/games + deuce) use a dedicated scorer instead of the
        // table-tennis point-race-to-11 component - same branching rule as
        // RefereeController@... and PlayerMatchController::liveScore().
        $sport = $match->competition->sport ?? $match->league->sport ?? null;
        $isSetsGamesBased = $sport && $sport->isSetsGamesBased();
    @endphp

    <div class="py-2 sm:py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if($isSetsGamesBased)
                @livewire('tennis-live-score', ['match' => $match])
            @else
                @livewire('live-score', ['match' => $match])
            @endif
        </div>
    </div>
</x-app-layout>