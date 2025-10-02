<div class="space-y-6">
    <!-- Filter Controls -->
    <div class="bg-gray-700/50 rounded-lg border border-gray-600/50 p-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-white">Historija mečeva</h3>
            <div class="flex items-center space-x-2">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        wire:model.live="showOnlyCompleted"
                        class="rounded border-gray-500 bg-gray-600 text-blue-500 shadow-sm focus:border-blue-400 focus:ring focus:ring-blue-400 focus:ring-opacity-50"
                    >
                    <span class="ml-2 text-sm text-gray-300">Samo završeni mečevi</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Matches List -->
    @if(count($matches) > 0)
        <div class="space-y-4">
            @foreach($matches as $match)
                <div class="bg-gray-700/40 rounded-xl p-6 border-l-4 {{ $match['is_win'] ? 'border-green-400' : 'border-red-400' }} hover:bg-gray-700/60 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <!-- Match Type & League -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $match['type'] === 'Prijateljski' ? 'bg-orange-500/20 text-orange-400' : 'bg-purple-500/20 text-purple-400' }}">
                                    {{ $match['type'] }}
                                </span>
                                @if($match['league'])
                                    <span class="text-sm text-blue-400">{{ $match['league'] }}</span>
                                @endif
                                <span class="text-xs text-gray-500">
                                    {{ $match['date'] ? \Carbon\Carbon::parse($match['date'])->format('d.m.Y H:i') : 'Nepoznat datum' }}
                                </span>
                            </div>

                            <!-- Players -->
                            <div class="flex items-center gap-4">
                                <div class="text-lg">
                                    <span class="font-bold text-white">
                                        {{ $player->name }}
                                    </span>
                                    <span class="mx-2 text-gray-400">vs</span>
                                    <span class="text-gray-300">
                                        {{ $match['opponent'] }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Result -->
                        <div class="text-center">
                            @if($match['result'])
                                <div class="text-2xl font-bold mb-1 {{ $match['is_win'] ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $match['result'] }}
                                </div>
                                <div class="text-sm text-gray-400">
                                    {{ $match['is_win'] ? 'POBJEDA' : 'PORAZ' }}
                                </div>
                            @else
                                <div class="text-lg text-gray-400">
                                    Nema rezultata
                                </div>
                            @endif
                        </div>

                        <!-- Sets Detail -->
                        <div class="text-center lg:text-right">
                            @if($match['sets'] && is_array($match['sets']) && count($match['sets']) > 0)
                                <div class="text-sm text-gray-300 mb-1">
                                    {{ collect($match['sets'])->map(fn($s) => $s['home_score'].'-'.$s['away_score'])->implode(', ') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ count($match['sets']) }} {{ count($match['sets']) === 1 ? 'set' : 'setova' }}
                                </div>
                            @else
                                <div class="text-sm text-gray-400">Nema podataka o setovima</div>
                            @endif
                        </div>

                        <!-- Action Button -->
                        <div class="flex items-center">
                            @if($match['type'] === 'Prijateljski')
                                <a href="{{ $this->getMatchUrl($match) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-orange-500/20 hover:bg-orange-500/30 text-orange-400 rounded-lg text-sm transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Detalji
                                </a>
                            @else
                                <a href="{{ $this->getMatchUrl($match) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-400 rounded-lg text-sm transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Detalji
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Status Display -->
                    @if($match['status'] && $match['status'] !== 'completed')
                        <div class="mt-3 pt-3 border-t border-gray-600">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $match['status'] === 'scheduled' ? 'bg-yellow-500/20 text-yellow-400' : 
                                   ($match['status'] === 'forfeited' ? 'bg-red-500/20 text-red-400' : 'bg-gray-500/20 text-gray-400') }}">
                                @switch($match['status'])
                                    @case('scheduled') Zakazan @break
                                    @case('forfeited') Predaja @break
                                    @default {{ ucfirst($match['status']) }}
                                @endswitch
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-400 text-lg mb-2">Nema odigranih mečeva</p>
            <p class="text-gray-500 text-sm">
                @if($showOnlyCompleted)
                    Igrač nema završenih mečeva.
                @else
                    Kada igrač odigra svoje prve mečeve, ovdje će se prikazati detalji.
                @endif
            </p>
        </div>
    @endif
</div>