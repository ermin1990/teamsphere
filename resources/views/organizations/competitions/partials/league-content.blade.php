@if($competition->is_team_based)
    <div class="space-y-6">
        <!-- Team Standings -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50">
            <h3 class="text-xl font-bold text-white mb-4">Tabela Ekipa</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Poz</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Ekipa</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">OU</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">P</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">I</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Bod</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($competition->standings->sortByDesc('points') as $standing)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">{{ $loop->iteration }}.</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-white">{{ $standing->team->name ?? 'Nepoznato' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-300">{{ $standing->played }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-300">{{ $standing->won }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-300">{{ $standing->lost }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-blue-400">{{ $standing->points }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Team Matches -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white">Raspored i Rezultati</h3>
                @if($isOwner)
                    <button onclick="document.getElementById('addMatchModal').classList.remove('hidden')" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Dodaj Meč
                    </button>
                @endif
            </div>
            <div class="space-y-8">
                @foreach($competition->teamMatches->sortBy('round')->groupBy('round') as $round => $matches)
                    @php
                        $isRoundFinished = $matches->every(fn($m) => $m->status === 'completed');
                    @endphp
                    <div x-data="{ open: {{ $isRoundFinished ? 'false' : 'true' }} }" class="space-y-4">
                        <div @click="open = !open" class="flex items-center space-x-4 cursor-pointer group">
                            <div class="h-px flex-1 bg-gray-700 group-hover:bg-blue-500/50 transition-colors"></div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-wider group-hover:text-blue-400 transition-colors">Kolo {{ $round }}</span>
                                @if($isRoundFinished)
                                    <span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full font-bold">ZAVRŠENO</span>
                                @endif
                                <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div class="h-px flex-1 bg-gray-700 group-hover:bg-blue-500/50 transition-colors"></div>
                        </div>
                        
                        <div x-show="open" class="grid grid-cols-1 gap-4">
                            @foreach($matches as $match)
                                <div class="bg-gray-900/50 rounded-xl p-4 border border-gray-700/30 flex items-center justify-between hover:border-gray-600 transition-colors">
                                    <div class="flex-1 text-right pr-4">
                                        <span class="text-white font-medium">{{ $match->homeTeam->name ?? 'Nepoznato' }}</span>
                                    </div>
                                    <div class="flex flex-col items-center px-4 min-w-[120px]">
                                        @if($match->scheduled_at)
                                            <span class="text-[10px] text-gray-500 uppercase mb-1">{{ $match->scheduled_at->format('d.m.Y H:i') }}</span>
                                        @endif
                                        @if($match->status === 'scheduled')
                                            <div class="flex flex-col items-center gap-2">
                                                <a href="{{ route('organizations.competitions.team-matches.protocol', [$organization, $competition, $match]) }}" class="bg-blue-600/20 text-blue-400 px-3 py-1 rounded-lg text-xs font-bold hover:bg-blue-600/30 transition">
                                                    PROTOKOL
                                                </a>
                                                @if($match->individualMatches->count() < 7)
                                                    <form action="{{ route('organizations.competitions.team-matches.initialize', [$organization, $competition, $match]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-[9px] text-gray-500 hover:text-blue-400 transition uppercase font-bold">
                                                            + Inicijalizuj mečeve
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center gap-1">
                                                <a href="{{ route('organizations.competitions.team-matches.show', [$organization, $competition, $match]) }}" class="text-2xl font-black text-white hover:text-blue-400 transition">
                                                    {{ $match->home_score }} : {{ $match->away_score }}
                                                </a>
                                                @if($match->individualMatches->count() < 7)
                                                    <form action="{{ route('organizations.competitions.team-matches.initialize', [$organization, $competition, $match]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-[9px] text-yellow-500/70 hover:text-yellow-400 transition uppercase font-bold">
                                                            + Dodaj mečeve koji fale
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 text-left pl-4 flex items-center justify-between">
                                        <span class="text-white font-medium">{{ $match->awayTeam->name ?? 'Nepoznato' }}</span>
                                        
                                        @if($isOwner)
                                            <form action="{{ route('organizations.competitions.team-matches.destroy', [$organization, $competition, $match]) }}" method="POST" onsubmit="return confirm('Sigurno želite obrisati ovaj meč?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-600 hover:text-red-500 transition-colors p-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    @php
        // Kolo se historijski upisivalo ili u 'round' ili u 'round_number' zavisno od
        // generatora - uzmi koje god od ta dva postoji da grupisanje ne bi palo na
        // pogresan default.
        $roundOf = fn($match) => $match->round_number ?? $match->round;
    @endphp
    <div class="space-y-6">
        <!-- Standings -->
        <div class="max-w-3xl">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-white">Tabela</h3>
                        @if($competition->is_recreational)
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30" title="Ručno dodavanje revanš-mečeva i ranije završavanje meča su dozvoljeni">
                                Rekreativna liga
                            </span>
                        @endif
                    </div>
                    @if($isOwner)
                        <button type="button" onclick="document.getElementById('invitePlayerModal').classList.remove('hidden')"
                                class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2">
                            ✉️ Pozovi igrača
                        </button>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Poz</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Igrač</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">OU</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">P</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">I</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Bod</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($competition->standings->sortByDesc('points') as $standing)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">{{ $loop->iteration }}.</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-white">{{ $standing->player->name ?? 'Nepoznato' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-300">{{ $standing->played }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-300">{{ $standing->won }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm text-gray-300">{{ $standing->lost }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-blue-400">{{ $standing->points }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Matches -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white">Raspored i Rezultati</h3>
                @if($competition->is_recreational && $isOwner)
                    <button type="button" onclick="document.getElementById('addRecreationalMatchModal').classList.remove('hidden')"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Dodaj Meč
                    </button>
                @endif
            </div>
            <div class="space-y-8">
                @foreach($competition->matches->sortBy($roundOf)->groupBy($roundOf) as $round => $matches)
                    <div x-data="{ open: false }" class="space-y-4">
                        <div @click="open = !open" class="flex items-center space-x-4 cursor-pointer group">
                            <div class="h-px flex-1 bg-gray-700 group-hover:bg-purple-500/50 transition-colors"></div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-wider group-hover:text-purple-400 transition-colors">Kolo {{ $round }}</span>
                                <span class="text-[10px] bg-gray-700/60 text-gray-400 px-2 py-0.5 rounded-full font-bold">{{ $matches->count() }}</span>
                                <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div class="h-px flex-1 bg-gray-700 group-hover:bg-purple-500/50 transition-colors"></div>
                        </div>

                        <div x-show="open" x-cloak class="grid grid-cols-1 gap-4">
                            @foreach($matches as $match)
                                <div class="bg-gray-900/50 rounded-xl p-3 sm:p-4 border border-gray-700/30 flex flex-wrap sm:flex-nowrap sm:items-center">
                                    <div class="w-1/2 sm:w-auto sm:flex-1 text-left sm:text-right pr-2 sm:pr-4 min-w-0 order-1">
                                        <span class="text-white font-medium text-sm sm:text-base break-words">{{ $match->homePlayer->name ?? 'TBD' }}</span>
                                    </div>
                                    <div class="w-full sm:w-auto flex flex-col items-center px-2 sm:px-4 sm:min-w-[110px] shrink-0 order-3 sm:order-2 mt-3 sm:mt-0">
                                        @if($match->status === 'in_progress')
                                            <span class="mb-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide animate-pulse" style="background: rgba(248,113,113,0.2); color: #f87171;">🔴 Uživo</span>
                                        @endif
                                        <span class="text-lg sm:text-xl font-black text-white">
                                            {{ $match->status === 'scheduled' ? '-' : $match->home_score }} : {{ $match->status === 'scheduled' ? '-' : $match->away_score }}
                                        </span>
                                        @if($match->status === 'completed' && !empty($match->sets))
                                            <div class="flex flex-wrap items-center justify-center gap-1 mt-1.5">
                                                @foreach($match->sets as $set)
                                                    @php
                                                        $sh = $set['home'] ?? $set['home_score'] ?? $set['p1'] ?? null;
                                                        $sa = $set['away'] ?? $set['away_score'] ?? $set['p2'] ?? null;
                                                    @endphp
                                                    @if($sh !== null && $sa !== null && !($sh === '' && $sa === ''))
                                                        <span class="text-[10px] font-bold text-gray-400 bg-gray-800/80 border border-gray-700/50 rounded px-1.5 py-0.5 tabular-nums">{{ $sh }}:{{ $sa }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="w-1/2 sm:w-auto sm:flex-1 text-right sm:text-left pl-2 sm:pl-4 min-w-0 order-2 sm:order-3">
                                        <span class="text-white font-medium text-sm sm:text-base break-words">{{ $match->awayPlayer->name ?? 'TBD' }}</span>
                                    </div>

                                    @if($isOwner)
                                        <div class="w-full sm:w-auto flex items-center justify-center gap-5 sm:gap-1 mt-3 sm:mt-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-700/30 sm:ml-2 order-4">
                                            <button type="button"
                                                    onclick="openQuickResultModal({{ $match->id }}, '{{ addslashes($match->homePlayer->name ?? 'TBD') }}', '{{ addslashes($match->awayPlayer->name ?? 'TBD') }}', {{ $match->status === 'completed' ? $match->home_score : 'null' }}, {{ $match->status === 'completed' ? $match->away_score : 'null' }}, {{ json_encode($match->sets ?? []) }}, '{{ $match->played_at ? $match->played_at->format('Y-m-d\TH:i') : '' }}', '{{ $match->venue_id }}')"
                                                    class="text-gray-500 hover:text-yellow-400 transition-colors p-1" title="{{ $match->status === 'completed' ? 'Uredi rezultat' : 'Brzi unos rezultata' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            </button>
                                            @if($match->status !== 'completed' && ($organization->sport->isPointsBased() || $organization->sport->isSetsGamesBased()))
                                                <a href="{{ route('referee.competition.match.live', [$competition, $match]) }}" class="text-gray-500 hover:text-green-400 transition-colors p-1" title="Uživo bodovanje">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            <a href="{{ route('organizations.competitions.matches.edit', [$organization, $competition, $match]) }}" class="text-gray-600 hover:text-blue-400 transition-colors p-1" title="Uredi meč">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('organizations.competitions.matches.destroy', [$organization, $competition, $match]) }}" method="POST" onsubmit="return confirm('Sigurno želite obrisati ovaj meč?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-600 hover:text-red-500 transition-colors p-1" title="Obriši meč">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($competition->is_recreational && $isOwner)
        <!-- Dodaj Meč Modal (samo rekreativne lige) -->
        <div id="addRecreationalMatchModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-gray-800 rounded-2xl p-6 max-w-md w-full border border-gray-700 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Dodaj Meč</h3>
                    <button type="button" onclick="document.getElementById('addRecreationalMatchModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-gray-400 text-xs mb-4">Rekreativna liga - možeš dodati meč između bilo koje dvojice prijavljenih igrača, uključujući ponovni meč (revanš) između istih igrača.</p>
                <form method="POST" action="{{ route('organizations.competitions.matches.store', [$organization, $competition]) }}">
                    @csrf
                    <label for="rec_home_player_id" class="block text-sm font-medium text-gray-300 mb-2">Domaći igrač</label>
                    <select name="home_player_id" id="rec_home_player_id" required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 mb-4">
                        <option value="">Odaberi igrača...</option>
                        @foreach($competition->players as $player)
                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                        @endforeach
                    </select>

                    <label for="rec_away_player_id" class="block text-sm font-medium text-gray-300 mb-2">Gostujući igrač</label>
                    <select name="away_player_id" id="rec_away_player_id" required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 mb-4">
                        <option value="">Odaberi igrača...</option>
                        @foreach($competition->players as $player)
                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                        @endforeach
                    </select>

                    <label for="rec_scheduled_at" class="block text-sm font-medium text-gray-300 mb-2">Datum i vrijeme (opcionalno)</label>
                    <input type="datetime-local" name="scheduled_at" id="rec_scheduled_at"
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 mb-4">

                    <div class="flex gap-3">
                        <button type="button" onclick="document.getElementById('addRecreationalMatchModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            Odustani
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                            Dodaj meč
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($isOwner)
        <!-- Pozovi igrača Modal -->
        <div id="invitePlayerModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-gray-800 rounded-2xl p-6 max-w-md w-full border border-gray-700 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">✉️ Pozovi igrača</h3>
                    <button type="button" onclick="document.getElementById('invitePlayerModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-gray-400 text-xs mb-4">Igrač dobija email sa linkom da se registruje/prijavi i vidi ovo takmičenje u svom "Moje lige" pregledu.</p>
                <form method="POST" action="{{ route('organizations.competitions.invite-player', [$organization, $competition]) }}">
                    @csrf
                    <label for="invite_name" class="block text-sm font-medium text-gray-300 mb-2">Ime i prezime</label>
                    <input type="text" name="name" id="invite_name" required
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">

                    <label for="invite_email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" id="invite_email" required
                           class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">

                    <div class="flex gap-3">
                        <button type="button" onclick="document.getElementById('invitePlayerModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            Odustani
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            Pošalji pozivnicu
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
@endif
