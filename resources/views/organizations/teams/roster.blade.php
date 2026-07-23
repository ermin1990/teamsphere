<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-3xl text-white leading-tight">
                        {{ $team->name }}
                    </h2>
                    <p class="text-gray-400 text-sm mt-1">Profil kluba i upravljanje rosterom</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($team->competition_id)
                    <a href="{{ route('organizations.teams.create', ['organization' => $organization, 'competition_id' => $team->competition_id]) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl transition-all text-sm font-medium">
                        + Nova ekipa
                    </a>
                @endif
                <a href="{{ route('organizations.teams.edit', [$organization, $team]) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all text-sm font-medium">
                    Uredi Klub
                </a>
                <a href="{{ route('organizations.teams.index', array_filter(['organization' => $organization, 'competition_id' => $team->competition_id])) }}" class="text-gray-400 hover:text-white transition-colors flex items-center text-sm font-medium">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Svi timovi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Info & Matches -->
                <div class="lg:col-span-1 space-y-8">
                    <!-- Club Info -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informacije o klubu
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-gray-500 uppercase font-bold tracking-wider">Opis</label>
                                <p class="text-gray-300 text-sm mt-1">{{ $team->description ?: 'Nema opisa.' }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-gray-500 uppercase font-bold tracking-wider">Igrača</label>
                                    <p class="text-white font-bold">{{ $teamPlayers->count() }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 uppercase font-bold tracking-wider">Status</label>
                                    <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-full border border-emerald-500/20">AKTIVAN</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coaches Management -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Treneri ekipe
                        </h3>
                        
                        <form action="{{ route('organizations.teams.coaches.add', [$organization, $team]) }}" method="POST" class="mb-6">
                            @csrf
                            <div class="flex gap-2">
                                <x-text-input name="name" placeholder="Ime novog trenera" class="flex-1 bg-gray-900/50 border-gray-700 text-white text-sm" required />
                                <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 text-xs">
                                    Dodaj
                                </x-primary-button>
                            </div>
                        </form>

                        <div class="space-y-3">
                            @forelse($coaches as $coach)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-900/30 border {{ $coach->is_active ? 'border-emerald-500/30' : 'border-gray-700/30' }}">
                                    <div>
                                        <p class="text-sm font-bold {{ $coach->is_active ? 'text-white' : 'text-gray-400' }}">{{ $coach->name }}</p>
                                        <p class="text-[10px] text-gray-500">
                                            {{ $coach->start_date ? $coach->start_date->format('d.m.Y') : '' }} 
                                            @if($coach->end_date) - {{ $coach->end_date->format('d.m.Y') }} @elseif($coach->is_active) (Trenutni) @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if(isset($coach->id) && $coach->id > 0)
                                            <form action="{{ route('organizations.teams.coaches.toggle', [$organization, $team, $coach]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="p-1 rounded hover:bg-gray-700 transition-colors" title="{{ $coach->is_active ? 'Deaktiviraj' : 'Postavi kao aktivnog' }}">
                                                    @if($coach->is_active)
                                                        <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>
                                            <form action="{{ route('organizations.teams.coaches.remove', [$organization, $team, $coach]) }}" method="POST" onsubmit="return confirm('Sigurno želite ukloniti ovog trenera?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 rounded hover:bg-red-500/20 text-gray-600 hover:text-red-500 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-500 text-center py-2">Nema dodanih trenera.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Matches -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
                        <div class="p-6 border-b border-gray-700/50">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Zadnji mečevi
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-700/50">
                            @forelse($recentMatches as $match)
                                <div class="p-4 hover:bg-gray-700/20 transition-colors">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[10px] text-gray-500 font-bold uppercase">{{ $match->competition->name }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $match->scheduled_at ? $match->scheduled_at->format('d.m.Y') : '-' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 text-sm {{ $match->home_team_id == $team->id ? 'text-white font-bold' : 'text-gray-400' }}">
                                            {{ $match->homeTeam->name }}
                                        </div>
                                        <div class="px-3 py-1 bg-gray-900/50 rounded font-mono text-sm text-white mx-2">
                                            {{ $match->home_score ?? 0 }} : {{ $match->away_score ?? 0 }}
                                        </div>
                                        <div class="flex-1 text-sm text-right {{ $match->away_team_id == $team->id ? 'text-white font-bold' : 'text-gray-400' }}">
                                            {{ $match->awayTeam->name }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-500 text-sm italic">
                                    Nema odigranih mečeva.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column: Roster & Add Players -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Current Roster -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
                        <div class="p-6 border-b border-gray-700/50 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white">Trenutni Roster ({{ $teamPlayers->count() }})</h3>
                            <button onclick="document.getElementById('bulkAddModal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Dodaj u ekipu
                            </button>
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
                                                    <div>
                                                        <div class="text-white font-medium">{{ $player->name }}</div>
                                                        <div class="text-[10px] text-gray-500">{{ $player->email ?: 'Nema emaila' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-400 text-sm">
                                                {{ $player->position ?: '-' }}
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
                                                Roster je prazan. Dodajte igrače koristeći opcije iznad.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Add Modal -->
    <div id="bulkAddModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-2xl max-w-2xl w-full max-h-[80vh] flex flex-col">
            <div class="p-6 border-b border-gray-700 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white">Dodaj u Ekipu</h3>
                <button onclick="document.getElementById('bulkAddModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('organizations.teams.roster.bulk-add', [$organization, $team]) }}" method="POST" class="flex-1 overflow-hidden flex flex-col">
                @csrf
                <div class="p-6 overflow-y-auto flex-1">
                    <!-- Bulk Names Input -->
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-wider">Brzo dodavanje po imenu</label>
                        <textarea name="names_list" rows="4" 
                            class="w-full bg-gray-900/50 border-gray-700 text-white focus:border-blue-500 focus:ring-blue-500 rounded-xl text-sm"
                            placeholder="Upišite imena (jedno po redu ili odvojeno zarezom)...&#10;Ime Prezime&#10;Drugi Igrač, Treći Igrač"></textarea>
                        <p class="mt-2 text-[10px] text-gray-500 italic">Sistem će automatski kreirati nove igrače i dodati ih u ovaj tim.</p>
                    </div>

                    <div class="relative flex items-center py-4 mb-4">
                        <div class="flex-grow border-t border-gray-700"></div>
                        <span class="flex-shrink mx-4 text-gray-500 text-[10px] font-bold uppercase">ILI ODABERI POSTOJEĆE</span>
                        <div class="flex-grow border-t border-gray-700"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($availablePlayers as $player)
                            <label class="flex items-center p-3 bg-gray-900/50 border border-gray-700 rounded-xl cursor-pointer hover:border-blue-500/50 transition-all group">
                                <input type="checkbox" name="player_ids[]" value="{{ $player->id }}" class="rounded border-gray-600 bg-gray-800 text-blue-600 focus:ring-blue-500 mr-3">
                                <div>
                                    <div class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors">{{ $player->name }}</div>
                                    <div class="text-[10px] text-gray-500">
                                        @if($player->teams->count() > 0)
                                            {{ $player->teams->first()->name }}
                                        @else
                                            {{ $player->position ?: 'Nema kluba' }}
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500 italic">
                                Nema dostupnih igrača za dodavanje.
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <div class="p-6 border-t border-gray-700 bg-gray-900/30 flex gap-3">
                    <button type="button" onclick="document.getElementById('bulkAddModal').classList.add('hidden')" class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-bold transition">
                        Odustani
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-lg shadow-blue-500/20">
                        Dodaj označene
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
