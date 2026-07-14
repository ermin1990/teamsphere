<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                    {{ $competition->sport->icon }} Live {{ $competition->sport->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $competition->name }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-3 py-1 text-sm rounded-full
                    @if($match->status === 'completed') bg-green-500/20 text-green-400
                    @elseif($match->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                    @else bg-gray-500/20 text-gray-400 @endif"
                >
                    {{ ucfirst(str_replace('_', ' ', $match->status)) }}
                </span>
                <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}"
                   class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    ← Nazad na takmičenje
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @livewire('tennis-live-score', ['match' => $match])
        </div>
    </div>
</x-app-layout>
