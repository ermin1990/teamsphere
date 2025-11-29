<div class="max-w-4xl mx-auto p-6">
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50">
        <h2 class="text-2xl font-bold text-white mb-6">Ručno prilagođavanje pozicija</h2>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-600/20 border border-green-600/50 rounded-lg text-green-400">
                {{ session('message') }}
            </div>
        @endif

        <div class="mb-6">
            <p class="text-gray-300 mb-4">
                Povucite i ispustite igrače da promijenite njihov redoslijed. Kliknite "Sačuvaj" da primijenite promjene.
            </p>
        </div>

        <div wire:sortable="updateOrder" class="space-y-2">
            @foreach($standings as $standing)
                <div wire:sortable.item="{{ $standing->id }}" class="bg-gray-700/50 rounded-lg p-4 border border-gray-600/50 cursor-move hover:bg-gray-600/50 transition-colors">
                    <div wire:sortable.handle class="flex items-center space-x-4">
                        <div class="text-gray-400 cursor-move">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-white font-medium">{{ $standing->player->name }}</div>
                            <div class="text-gray-400 text-sm">
                                Bodovi: {{ $standing->points }} | Setovi: {{ $standing->sets_won }}-{{ $standing->sets_lost }} | Gemovi: {{ $standing->points_won }}-{{ $standing->points_lost }}
                            </div>
                        </div>
                        <div class="text-gray-400 text-sm">
                            Pozicija: {{ $loop->iteration }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex space-x-4 mt-6">
            <button wire:click="saveOrder" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors font-semibold">
                Sačuvaj pozicije
            </button>
            <button wire:click="resetOrder" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors font-semibold">
                Resetuj na automatsko
            </button>
        </div>
    </div>
</div>
