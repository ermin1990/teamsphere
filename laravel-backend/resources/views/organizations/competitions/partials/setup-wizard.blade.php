{{-- Setup Wizard (Draft Only) --}}
@if($isOwner && $competition->status === 'draft')
<div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 backdrop-blur-xl rounded-xl p-6 border border-blue-500/30 shadow-xl mb-6">
    <div class="flex items-center justify-center sm:justify-start">
        <div class="text-center sm:text-left">
            <h3 class="text-xl font-bold text-white mb-2">Postavi Takmičenje</h3>
            <p class="text-gray-300">Pratite ove korake da postavite vaše takmičenje</p>
        </div>
    </div>

    <!-- Setup Steps -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-800/50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h4 class="text-white font-semibold">Postavke</h4>
            </div>
            <p class="text-gray-400 text-sm mb-3">Konfigurišite pravila i podešavanja takmičenja</p>
            <a href="{{ route('organizations.competitions.settings', [$organization, $competition]) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-2 rounded transition-colors inline-block">
                Namjesti Postavke
            </a>
        </div>

        <div class="bg-gray-800/50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 rounded-full {{ $competition->players->count() > 0 ? 'bg-green-600' : 'bg-gray-600' }} flex items-center justify-center mr-3">
                    @if($competition->players->count() > 0)
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <span class="text-white font-bold text-sm">2</span>
                    @endif
                </div>
                <h4 class="text-white font-semibold">Dodaj Igrače</h4>
            </div>
            <p class="text-gray-400 text-sm mb-3">{{ $competition->players->count() }} igrača dodano</p>
            <a href="{{ route('organizations.competitions.manage-players', [$organization, $competition]) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-2 rounded transition-colors inline-block">
                Upravljaj Igračima
            </a>
        </div>

        @if($competition->is_team_based)
        <div class="bg-gray-800/50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 rounded-full {{ $competition->teams->count() > 0 ? 'bg-green-600' : 'bg-gray-600' }} flex items-center justify-center mr-3">
                    @if($competition->teams->count() > 0)
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <span class="text-white font-bold text-sm">3</span>
                    @endif
                </div>
                <h4 class="text-white font-semibold">Ekipe i Rosteri</h4>
            </div>
            <p class="text-gray-400 text-sm mb-3">{{ $competition->teams->count() }} ekipa kreirano</p>
            <a href="{{ route('organizations.teams.index', [$organization, 'competition_id' => $competition->id]) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-2 rounded transition-colors inline-block">
                Upravljaj Ekipama
            </a>
        </div>
        @endif

        @if($competition->type === 'tournament')
        <div class="bg-gray-800/50 rounded-lg p-4 {{ $competition->tournamentGroups->count() > 0 ? 'cursor-pointer hover:bg-gray-700/50' : '' }} transition-colors"
             @if($competition->tournamentGroups->count() > 0)
             onclick="window.location.href='{{ route('organizations.competitions.setup-groups', [$organization, $competition]) }}'"
             @endif>
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 rounded-full {{ $competition->tournamentGroups->count() > 0 ? 'bg-green-600' : 'bg-gray-600' }} flex items-center justify-center mr-3">
                    @if($competition->tournamentGroups->count() > 0)
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <span class="text-white font-bold text-sm">3</span>
                    @endif
                </div>
                <h4 class="font-semibold text-white">Grupe</h4>
            </div>
            <p class="text-gray-400 text-sm mb-3">
                @if($competition->tournamentGroups->count() > 0)
                    {{ $competition->tournamentGroups->count() }} grupa konfigurisano - Kliknite za uređivanje
                @else
                    {{ __('Organize into groups') }}
                @endif
            </p>
            @if($competition->tournamentGroups->count() === 0)
            <a href="{{ route('organizations.competitions.setup-groups', [$organization, $competition]) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-2 rounded transition-colors inline-block">
                Postavi
            </a>
            @endif
        </div>
        @endif

        <div class="bg-gray-800/50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full
                        @if(($competition->type === 'tournament' && $competition->players->count() > 0 && $competition->tournamentGroups->count() > 0) ||
                            ($competition->is_team_based && $competition->teams->count() >= 2) ||
                            (!$competition->is_team_based && $competition->type === 'league' && $competition->players->count() > 0))
                            bg-green-600
                        @else
                            bg-gray-600
                        @endif
                        flex items-center justify-center mr-3">
                        @if(($competition->type === 'tournament' && $competition->players->count() > 0 && $competition->tournamentGroups->count() > 0) ||
                            ($competition->is_team_based && $competition->teams->count() >= 2) ||
                            (!$competition->is_team_based && $competition->type === 'league' && $competition->players->count() > 0))
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <span class="text-white font-bold text-sm">
                                @if($competition->type === 'tournament') 4 @elseif($competition->is_team_based) 4 @else 3 @endif
                            </span>
                        @endif
                    </div>
                    <h4 class="text-white font-semibold">{{ __('Start Competition') }}</h4>
                </div>
                @if(($competition->type === 'tournament' && $competition->players->count() > 0 && $competition->tournamentGroups->count() > 0) ||
                    ($competition->is_team_based && $competition->teams->count() >= 2) ||
                    (!$competition->is_team_based && $competition->type === 'league' && $competition->players->count() > 0))
                <form method="POST" action="{{ route('organizations.competitions.start', [$organization, $competition]) }}" class="flex flex-col items-end gap-2">
                    @csrf
                    <div class="flex items-center gap-2 mb-1">
                        <input type="checkbox" name="manual_matches" id="manual_matches" value="1" class="rounded border-gray-700 bg-gray-900 text-blue-600 focus:ring-blue-500">
                        <label for="manual_matches" class="text-[10px] text-gray-400 uppercase font-bold cursor-pointer">Ručno dodavanje mečeva</label>
                    </div>
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg transition-colors font-semibold">
                        🚀 {{ __('Start') }}
                    </button>
                </form>
                @endif
            </div>
            <p class="text-gray-400 text-sm">
                @if(($competition->type === 'tournament' && $competition->players->count() > 0 && $competition->tournamentGroups->count() > 0) ||
                    ($competition->type === 'league' && $competition->players->count() > 0))
                    {{ __('Ready to start!') }}
                @else
                    {{ __('Begin matches') }}
                @endif
            </p>
        </div>
    </div>
</div>
@endif
