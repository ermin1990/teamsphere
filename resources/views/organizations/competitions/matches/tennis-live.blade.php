<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}"
                   class="p-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors flex-shrink-0" title="Nazad na takmičenje">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-bold text-lg truncate">
                    {{ $competition->sport->icon }} {{ $competition->name }}
                </h2>
            </div>
            <span class="px-2.5 py-1 text-xs rounded-full flex-shrink-0
                @if($match->status === 'completed') bg-green-500/20 text-green-400
                @elseif($match->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                @else bg-gray-500/20 text-gray-400 @endif"
            >
                {{ ucfirst(str_replace('_', ' ', $match->status)) }}
            </span>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto px-3 py-4">
        @livewire('tennis-live-score', ['match' => $match])
    </div>
</x-app-layout>
