<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    {{ __('Roster Tima') }}: {{ $team->name }}
                </h2>
                <p class="text-gray-400 text-sm mt-1">Upravljajte igračima koji nastupaju za ovaj tim</p>
            </div>
            <a href="{{ route('organizations.teams.index', $organization) }}" class="text-gray-400 hover:text-white transition-colors flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Nazad na timove
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Current Roster -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
                        <div class="p-6 border-b border-gray-700/50">
                            <h3 class="text-lg font-bold text-white">Trenutni Roster ({{ $teamPlayers->count() }})</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-900/30">
                                        <th class="px-6 py-4 text-gray-400 font-medium text-sm uppercase tracking-wider">Igrač</th>
                                        <th class="px-6 py-4 text-gray-400 font-medium text-sm uppercase tracking-wider">Pozicija/Klub</th>
                                        <th class="px-6 py-4 text-gray-400 font-medium text-sm uppercase tracking-wider text-right">Akcije</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700/50">
                                    @forelse($teamPlayers as $player)
                                        <tr class="hover:bg-gray-700/20 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center text-emerald-400 font-bold">
                                                        {{ substr($player->name, 0, 1) }}
                                                    </div>
                                                    <div class="text-white font-medium">{{ $player->name }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-400 text-sm">
                                                {{ $player->position ?: 'Nema kluba' }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <form action="{{ route('organizations.teams.roster.remove', [$organization, $team, $player]) }}" method="POST" onsubmit="return confirm('Ukloniti igrača iz rostera?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300 transition-colors text-sm font-medium">
                                                        Ukloni
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-12 text-center text-gray-500 italic">
                                                Roster je prazan. Dodajte igrače iz liste desno.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Add Players -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
                        <div class="p-6 border-b border-gray-700/50">
                            <h3 class="text-lg font-bold text-white">Dodaj Igrače</h3>
                            <p class="text-gray-400 text-xs mt-1">Lista igrača organizacije koji nisu u ovom timu</p>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('organizations.teams.roster.add', [$organization, $team]) }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <select name="player_id" class="w-full bg-gray-900/50 border-gray-700 text-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" required>
                                            <option value="">Odaberi igrača...</option>
                                            @foreach($availablePlayers as $player)
                                                <option value="{{ $player->id }}">{{ $player->name }} ({{ $player->position ?: 'Nema kluba' }})</option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('player_id')" />
                                    </div>
                                    <x-primary-button class="w-full justify-center bg-emerald-600 hover:bg-emerald-700">
                                        Dodaj u Roster
                                    </x-primary-button>
                                </div>
                            </form>
                            
                            @if($availablePlayers->isEmpty())
                                <p class="mt-4 text-gray-500 text-sm text-center italic">
                                    Svi igrači organizacije su već u rosteru ili nema dostupnih igrača.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
