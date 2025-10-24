<x-slot name="header">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                {{ $competition->name }}
            </h2>
            <p class="text-gray-400 mt-1">{{ $organization->name }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="px-3 py-1 text-sm rounded-full
                @if($competition->status === 'active') bg-green-500/20 text-green-400
                @elseif($competition->status === 'draft') bg-yellow-500/20 text-yellow-400
                @elseif($competition->status === 'completed') bg-blue-500/20 text-blue-400
                @else bg-red-500/20 text-red-400 @endif"
            >
                {{ ucfirst($competition->status) }}
            </span>
            @if($competition->type === 'tournament')
            <span class="px-3 py-1 text-sm rounded-full bg-purple-500/20 text-purple-400">
                Turnir
            </span>
            @else
            <span class="px-3 py-1 text-sm rounded-full bg-blue-500/20 text-blue-400">
                Liga
            </span>
            @endif
        </div>
    </div>
</x-slot>
