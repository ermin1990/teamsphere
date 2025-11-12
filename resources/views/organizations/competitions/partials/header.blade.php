<x-slot name="header">
    <!-- Mobile Layout -->
    <div class="block md:hidden">
        <div class="text-center">
            <h2 class="font-bold text-2xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
                {{ $competition->name }}
            </h2>
            <p class="text-gray-400 text-sm mb-4">{{ $organization->name }}</p>
            <div class="flex items-center justify-center gap-2 flex-wrap">
                <span class="px-3 py-1 text-xs rounded-full
                    @if($competition->status === 'active') bg-green-500/20 text-green-400
                    @elseif($competition->status === 'draft') bg-yellow-500/20 text-yellow-400
                    @elseif($competition->status === 'completed') bg-blue-500/20 text-blue-400
                    @else bg-red-500/20 text-red-400 @endif"
                >
                    @if($competition->status === 'active') Aktivno
                    @elseif($competition->status === 'draft') Nacrt
                    @elseif($competition->status === 'completed') Završeno
                    @else {{ ucfirst($competition->status) }} @endif
                </span>
                @if($competition->type === 'tournament')
                <span class="px-3 py-1 text-xs rounded-full bg-purple-500/20 text-purple-400">
                    Turnir
                </span>
                @else
                <span class="px-3 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">
                    Liga
                </span>
                @endif
                @if($competition->isFutsal())
                <span class="px-3 py-1 text-xs rounded-full bg-green-500/20 text-green-400">
                    ⚽ Futsal
                </span>
                @else
                <span class="px-3 py-1 text-xs rounded-full bg-orange-500/20 text-orange-400">
                    🏓 Stolni Tenis
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Desktop Layout -->
    <div class="hidden md:flex md:items-center md:justify-between">
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
                @if($competition->status === 'active') Aktivno
                @elseif($competition->status === 'draft') Nacrt
                @elseif($competition->status === 'completed') Završeno
                @else {{ ucfirst($competition->status) }} @endif
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
            @if($competition->isFutsal())
            <span class="px-3 py-1 text-sm rounded-full bg-green-500/20 text-green-400">
                ⚽ Futsal
            </span>
            @else
            <span class="px-3 py-1 text-sm rounded-full bg-orange-500/20 text-orange-400">
                🏓 Stolni Tenis
            </span>
            @endif
        </div>
    </div>
</x-slot>
