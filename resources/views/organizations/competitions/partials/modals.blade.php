{{-- Quick Result Modal --}}
<div id="quickResultModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    <div class="bg-gray-800 border border-gray-700/80 w-full max-w-md rounded-[24px] shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">

        {{-- Header --}}
        <div class="p-4 border-b border-white/5 flex justify-between items-center shrink-0">
            <div>
                <h3 class="text-white font-black uppercase tracking-tighter text-sm leading-none">Rezultat</h3>
                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest mt-1">Konačan rezultat meča</p>
            </div>
            <button type="button" onclick="closeQuickResultModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-700/70 text-gray-400 hover:text-red-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="quickResultForm" method="POST" class="overflow-y-auto flex-1">
            @csrf
            <input type="hidden" id="quickMatchId" name="match_id">
            <input type="hidden" id="scrollPosition" name="scroll_position">

            <div class="p-4 space-y-4">
                {{-- Score --}}
                <div class="flex items-center justify-between gap-2">
                    <div class="flex-1 text-center space-y-1 p-2.5 rounded-2xl bg-gray-900/60 border border-white/5">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest truncate" id="homePlayerName">Player 1</p>
                        <input type="number" name="home_score" id="homeScoreInput" min="0" max="10" required onblur="syncSetScoreRows()"
                               class="w-full bg-transparent border-none text-center text-4xl p-0 font-black text-white outline-none focus:ring-0">
                    </div>
                    <div class="text-xl font-black text-gray-700 shrink-0">:</div>
                    <div class="flex-1 text-center space-y-1 p-2.5 rounded-2xl bg-gray-900/60 border border-white/5">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest truncate" id="awayPlayerName">Player 2</p>
                        <input type="number" name="away_score" id="awayScoreInput" min="0" max="10" required onblur="syncSetScoreRows()"
                               class="w-full bg-transparent border-none text-center text-4xl p-0 font-black text-white outline-none focus:ring-0">
                    </div>
                </div>
                <p class="text-gray-500 text-[9px] font-bold uppercase tracking-widest text-center -mt-2">Broj osvojenih setova</p>

                <div class="space-y-1">
                    <label for="quickPlayedAt" class="text-[9px] font-black text-gray-500 uppercase tracking-widest px-1">Datum i vrijeme</label>
                    <input type="datetime-local" id="quickPlayedAt" name="played_at"
                           class="w-full bg-gray-900/60 border border-white/5 rounded-xl p-2.5 text-white font-bold outline-none focus:ring-2 focus:ring-blue-500/40 text-xs">
                </div>

                @if($venues->isNotEmpty())
                <div class="space-y-1">
                    <label for="quickVenueId" class="text-[9px] font-black text-gray-500 uppercase tracking-widest px-1">Teren (opcionalno)</label>
                    <select id="quickVenueId" name="venue_id"
                            class="w-full bg-gray-900/60 border border-white/5 rounded-xl p-2.5 text-white font-bold outline-none focus:ring-2 focus:ring-blue-500/40 text-xs">
                        <option value="">— nije odabran —</option>
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}">{{ $venue->name }}{{ $venue->city ? ' ('.$venue->city->name.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Set scores --}}
                <div class="bg-gray-900/60 border border-white/5 rounded-[20px] p-3">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Setovi (opcionalno)</p>
                            <p class="text-gray-500 text-[9px] mt-0.5">Unesi samo ako želiš detaljan tok meča</p>
                        </div>
                        <button type="button" onclick="addSetScore()"
                                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-[9px] font-black uppercase tracking-widest rounded-full transition-colors whitespace-nowrap shrink-0">
                            + Set
                        </button>
                    </div>
                    <div id="setScoresContainer" class="space-y-1.5">
                        {{-- Set rows are injected here by addSetScore(), auto-synced to the score above via syncSetScoreRows() --}}
                    </div>
                </div>
            </div>

            <div class="p-4 pt-0 flex gap-2 shrink-0">
                <button type="button" onclick="closeQuickResultModal()"
                        class="flex-1 py-3 rounded-xl bg-gray-700/70 hover:bg-gray-700 text-white font-black uppercase tracking-widest text-[10px] transition-colors">
                    Odustani
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-black font-black uppercase tracking-widest text-[10px] shadow-xl shadow-emerald-500/20 active:scale-95 transition-all">
                    Sačuvaj
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Add Team Match Modal --}}
<div id="addMatchModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-2xl p-6 max-w-md w-full border border-gray-700 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white">➕ Dodaj novi meč</h3>
            <button onclick="document.getElementById('addMatchModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="{{ route('organizations.competitions.team-matches.store', [$organization, $competition]) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <!-- Round -->
                <div>
                    <label for="round" class="block text-sm font-medium text-gray-300 mb-2">
                        Kolo
                    </label>
                    <input type="number" id="round" name="round" required min="1" value="{{ ($competition->teamMatches->max('round') ?? 0) + 1 }}"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Home Team -->
                <div>
                    <label for="home_team_id" class="block text-sm font-medium text-gray-300 mb-2">
                        Domaća Ekipa
                    </label>
                    <select id="home_team_id" name="home_team_id" required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Odaberite ekipu...</option>
                        @foreach($competition->teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Away Team -->
                <div>
                    <label for="away_team_id" class="block text-sm font-medium text-gray-300 mb-2">
                        Gostujuća Ekipa
                    </label>
                    <select id="away_team_id" name="away_team_id" required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Odaberite ekipu...</option>
                        @foreach($competition->teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label for="scheduled_at" class="block text-sm font-medium text-gray-300 mb-2">
                        Datum i Vrijeme (opcionalno)
                    </label>
                    <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="button" 
                            onclick="document.getElementById('addMatchModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Odustani
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-bold">
                        ➕ Dodaj meč
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Add Match Modal --}}
<div id="addMatchModal_old" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-2xl p-6 max-w-md w-full border border-gray-700 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-white">➕ Dodaj novi meč</h3>
            <button onclick="closeAddMatchModal()" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="addMatchForm" onsubmit="event.preventDefault(); addNewMatch();">
            @csrf
            <div class="space-y-4">
                <!-- Match Name -->
                <div>
                    <label for="matchName" class="block text-sm font-medium text-gray-300 mb-2">
                        Naziv meča
                    </label>
                    <input type="text" id="matchName" name="name" required
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="npr. Meč za 3. mjesto">
                </div>

                <!-- Home Player -->
                <div>
                    <label for="homePlayer" class="block text-sm font-medium text-gray-300 mb-2">
                        Prvi igrač
                    </label>
                    <select id="homePlayer" name="home_player_id" required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Odaberite igrača...</option>
                    </select>
                </div>

                <!-- Away Player -->
                <div>
                    <label for="awayPlayer" class="block text-sm font-medium text-gray-300 mb-2">
                        Drugi igrač
                    </label>
                    <select id="awayPlayer" name="away_player_id" required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Odaberite igrača...</option>
                    </select>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="button" 
                            onclick="closeAddMatchModal()"
                            class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Odustani
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        ➕ Dodaj meč
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

