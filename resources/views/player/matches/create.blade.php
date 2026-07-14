<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Zabilježi meč
            </h2>
            <p class="text-gray-400 mt-1">{{ $competition->name }}</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
                @if($errors->any())
                    <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 text-red-400 text-sm mb-6">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('player.matches.store', $competition) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="opponent_id" class="block text-sm font-medium text-white mb-2">Protivnik</label>
                        <select id="opponent_id" name="opponent_id" required
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Odaberi protivnika...</option>
                            @foreach($opponents as $opponent)
                                <option value="{{ $opponent->id }}">{{ $opponent->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="played_at" class="block text-sm font-medium text-white mb-2">Datum meča</label>
                        <input type="datetime-local" id="played_at" name="played_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Rezultat po setovima ({{ $unitLabel }})</label>
                        <p class="text-xs text-gray-400 mb-3">Popuni samo onoliko setova koliko je odigrano - prazni setovi na kraju se ignorišu.</p>
                        <div class="space-y-2">
                            @for($i = 0; $i < $maxSets; $i++)
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-400 text-sm w-14">Set {{ $i + 1 }}</span>
                                    <input type="number" name="sets[{{ $i }}][mine]" min="0" placeholder="Ja"
                                           class="w-20 text-center px-3 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span class="text-gray-500">:</span>
                                    <input type="number" name="sets[{{ $i }}][theirs]" min="0" placeholder="Protivnik"
                                           class="w-20 text-center px-3 py-2 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('player.dashboard') }}" class="flex-1 text-center px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            Odustani
                        </a>
                        <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                            Sačuvaj meč
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
