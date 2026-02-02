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
                Unesite željene pozicije za svakog igrača i kliknite "Sačuvaj" da primijenite promjene.
            </p>
        </div>

        <div class="space-y-2">
            @foreach($standings as $index => $standing)
                <div class="bg-gray-700/50 rounded-lg p-4 border border-gray-600/50">
                    <div class="flex items-center space-x-4">
                        <div class="w-20">
                            <input type="number" 
                                   wire:model="standingsOrder.{{ $index }}.position"
                                   min="1" 
                                   max="{{ count($standings) }}"
                                   class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex-1">
                            <div class="text-white font-medium">{{ $standing->player->name }}</div>
                            <div class="text-gray-400 text-sm">
                                Bodovi: {{ $standing->points }} | Setovi: {{ $standing->sets_won }}-{{ $standing->sets_lost }} | Gemovi: {{ $standing->points_won }}-{{ $standing->points_lost }}
                            </div>
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
