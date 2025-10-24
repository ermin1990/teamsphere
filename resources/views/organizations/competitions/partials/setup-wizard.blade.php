{{-- Setup Wizard (Draft Only) --}}
@if($isOwner && $competition->status === 'draft')
<div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 backdrop-blur-xl rounded-xl p-6 border border-blue-500/30 shadow-xl mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-white mb-2">Postavi Takmičenje</h3>
            <p class="text-gray-300">Pratite ove korake da postavite vaše takmičenje</p>
        </div>
        <a href="{{ route('organizations.competitions.manage-players', [$organization, $competition]) }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors font-semibold flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Upravljaj Igračima
        </a>
    </div>

    <!-- Setup Steps -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800/50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 rounded-full {{ $competition->players->count() > 0 ? 'bg-green-600' : 'bg-gray-600' }} flex items-center justify-center mr-3">
                    @if($competition->players->count() > 0)
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <span class="text-white font-bold text-sm">1</span>
                    @endif
                </div>
                <h4 class="text-white font-semibold">Dodaj Igrače</h4>
            </div>
            <p class="text-gray-400 text-sm">{{ $competition->players->count() }} igrača dodano</p>
        </div>

        @if($competition->type === 'tournament')
        <div class="bg-gray-800/50 rounded-lg p-4 {{ $competition->tournamentGroups->count() > 0 ? 'cursor-pointer hover:bg-gray-700/50' : '' }} transition-colors"
             @if($competition->tournamentGroups->count() > 0)
             onclick="window.location.href='{{ route('organizations.competitions.setup-groups', [$organization, $competition]) }}'"
             @endif>
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-semibold text-white">Grupe</h4>
                @if($competition->tournamentGroups->count() > 0)
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                @else
                <a href="{{ route('organizations.competitions.setup-groups', [$organization, $competition]) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded transition-colors">
                    Postavi
                </a>
                @endif
            </div>
            <p class="text-gray-400 text-sm">
                @if($competition->tournamentGroups->count() > 0)
                    {{ $competition->tournamentGroups->count() }} grupa konfigurisano - Kliknite za uređivanje
                @else
                    {{ __('Organize into groups') }}
                @endif
            </p>
        </div>
        @endif

        <div class="bg-gray-800/50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full
                        @if(($competition->type === 'tournament' && $competition->players->count() > 0 && $competition->tournamentGroups->count() > 0) ||
                            ($competition->type === 'league' && $competition->players->count() > 0))
                            bg-green-600
                        @else
                            bg-gray-600
                        @endif
                        flex items-center justify-center mr-3">
                        @if(($competition->type === 'tournament' && $competition->players->count() > 0 && $competition->tournamentGroups->count() > 0) ||
                            ($competition->type === 'league' && $competition->players->count() > 0))
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <span class="text-white font-bold text-sm">{{ $competition->type === 'tournament' ? '3' : '2' }}</span>
                        @endif
                    </div>
                    <h4 class="text-white font-semibold">{{ __('Start Competition') }}</h4>
                </div>
                @if(($competition->type === 'tournament' && $competition->players->count() > 0 && $competition->tournamentGroups->count() > 0) ||
                    ($competition->type === 'league' && $competition->players->count() > 0))
                <form method="POST" action="{{ route('organizations.competitions.start', [$organization, $competition]) }}" class="inline">
                    @csrf
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
