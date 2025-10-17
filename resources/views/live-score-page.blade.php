<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Live Score
                </h2>
                <p class="text-gray-400 mt-1">
                    @if($match->league ?? false)
                        {{ $match->league->name }}
                    @else
                        {{ $match->competition->name ?? 'Competition' }}
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('live-score', ['match' => $match])
        </div>
    </div>
</x-app-layout>