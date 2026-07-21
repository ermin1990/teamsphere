<div class="max-w-3xl mx-auto p-6">
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50">
        <h2 class="text-2xl font-bold text-white mb-2">Ručno prilagođavanje tabele</h2>
        <p class="text-gray-400 text-sm mb-6">
            Pomjeri igrača strelicama gore/dole da ručno postaviš poziciju u tabeli (npr. kod izjednačenja na poene koje sistem ne može automatski riješiti). "Resetuj" vraća automatsko računanje tabele.
        </p>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-600/20 border border-green-600/50 rounded-lg text-green-400 text-sm">
                {{ session('message') }}
            </div>
        @endif

        <div class="space-y-2">
            @foreach($standings as $index => $standing)
                <div class="bg-gray-700/50 rounded-lg p-3 border border-gray-600/50 flex items-center gap-4">
                    <div class="w-8 text-center text-gray-400 font-bold">{{ $index + 1 }}.</div>

                    <div class="flex-1">
                        <div class="text-white font-medium">{{ $standing->team->name ?? $standing->player->name ?? 'Nepoznato' }}</div>
                        <div class="text-gray-400 text-xs">
                            Bodovi: {{ $standing->points }} | Setovi: {{ $standing->sets_won }}-{{ $standing->sets_lost }} | Gemovi: {{ $standing->points_won }}-{{ $standing->points_lost }}
                            @if($standing->manual_order)
                                <span class="ml-2 px-1.5 py-0.5 rounded bg-orange-500/20 text-orange-400 text-[10px] font-bold uppercase">Ručno</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col gap-1">
                        <button type="button" wire:click="moveUp({{ $standing->id }})"
                                @if($index === 0) disabled @endif
                                class="w-8 h-7 flex items-center justify-center rounded bg-gray-600 hover:bg-blue-600 disabled:opacity-30 disabled:hover:bg-gray-600 text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>
                        <button type="button" wire:click="moveDown({{ $standing->id }})"
                                @if($index === $standings->count() - 1) disabled @endif
                                class="w-8 h-7 flex items-center justify-center rounded bg-gray-600 hover:bg-blue-600 disabled:opacity-30 disabled:hover:bg-gray-600 text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <button type="button" wire:click="resetOrder"
                    onclick="return confirm('Sigurno želiš resetovati na automatsko računanje tabele?')"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors font-semibold text-sm">
                Resetuj na automatsko
            </button>
        </div>
    </div>
</div>
