{{-- Match Card Component --}}
<div class="bg-gray-700/20 rounded-lg p-2 hover:bg-gray-700/40 transition-all border border-gray-600/10">
    <div class="flex items-center justify-between gap-2">
        <!-- Players and Scores -->
        <div class="flex-1 min-w-0">
            <!-- Home Player -->
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center space-x-1.5 min-w-0 flex-1">
                    <div class="w-5 h-5 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-[9px]">{{ substr($match->homePlayer->name ?? 'TBD', 0, 2) }}</span>
                    </div>
                    <span class="text-white text-xs truncate">{{ $match->homePlayer->name ?? 'TBD' }}</span>
                </div>
                <span class="text-lg font-bold ml-2 flex-shrink-0
                    @if($match->status === 'completed' && $match->home_score > $match->away_score) text-green-400
                    @elseif($match->status === 'completed') text-gray-500
                    @else text-white @endif">
                    {{ $match->status !== 'scheduled' ? ($match->home_score ?? 0) : '-' }}
                </span>
            </div>
            
            <!-- Away Player -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-1.5 min-w-0 flex-1">
                    <div class="w-5 h-5 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-[9px]">{{ substr($match->awayPlayer->name ?? 'TBD', 0, 2) }}</span>
                    </div>
                    <span class="text-white text-xs truncate">{{ $match->awayPlayer->name ?? 'TBD' }}</span>
                </div>
                <span class="text-lg font-bold ml-2 flex-shrink-0
                    @if($match->status === 'completed' && $match->away_score > $match->home_score) text-green-400
                    @elseif($match->status === 'completed') text-gray-500
                    @else text-white @endif">
                    {{ $match->status !== 'scheduled' ? ($match->away_score ?? 0) : '-' }}
                </span>
            </div>

            <!-- Set Scores -->
            @if($match->status === 'completed' && $match->sets && is_array($match->sets) && count($match->sets) > 0)
            <div class="flex gap-1 mt-1">
                @foreach($match->sets as $set)
                <div class="bg-gray-600/40 px-1.5 py-0.5 rounded text-[10px] text-gray-300">
                    {{ ($set['home_score'] ?? $set['home'] ?? 0) }}-{{ ($set['away_score'] ?? $set['away'] ?? 0) }}
                </div>
                @endforeach
            </div>
            @endif
        </div>
        
        <!-- Actions -->
        <div class="flex flex-col gap-1 flex-shrink-0">
            @if($match->status === 'scheduled')
                @if($isOwner || $isRefereeForMatch($match))
                <a href="{{ $isRefereeForMatch($match) ? route('referee.competition.match.edit', [$match]) : route('organizations.competitions.matches.edit', [$organization, $competition, $match]) }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-white text-[10px] px-2 py-1 rounded transition-colors text-center whitespace-nowrap">
                    ✏️ {{ __('Edit') }}
                </a>
                <a href="{{ $isRefereeForMatch($match) ? route('referee.competition.match.live', [$match]) : route('competitions.live-score', [$match->id]) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white text-[10px] px-2 py-1 rounded transition-colors text-center whitespace-nowrap">
                    ▶️ {{ __('Live') }}
                </a>
                <button onclick="openQuickResultModal({{ $match->id }}, '{{ $match->homePlayer->name ?? 'TBD' }}', '{{ $match->awayPlayer->name ?? 'TBD' }}')"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-2 py-1 rounded transition-colors text-center whitespace-nowrap">
                    ⚡ Quick
                </button>
                @else
                <span class="text-[10px] bg-yellow-600/20 text-yellow-400 px-2 py-1 rounded text-center whitespace-nowrap">
                    {{ __('Soon') }}
                </span>
                @endif
            @elseif($match->status === 'in_progress')
                <span class="text-[10px] bg-green-600/20 text-green-400 px-2 py-1 rounded text-center whitespace-nowrap animate-pulse">
                    🔴 {{ __('Live') }}
                </span>
                @if($isOwner || $isRefereeForMatch($match))
                <a href="{{ $isRefereeForMatch($match) ? route('referee.competition.match.live', [$match]) : route('competitions.live-score', [$match->id]) }}" 
                   class="text-blue-400 hover:text-blue-300 text-[10px] text-center whitespace-nowrap">
                    👁️ {{ __('Watch') }}
                </a>
                @endif
            @elseif($match->status === 'completed')
                <span class="text-[10px] bg-gray-600/20 text-gray-400 px-2 py-1 rounded text-center whitespace-nowrap">
                    ✓ {{ __('FT') }}
                </span>
                @if($isOwner || $isRefereeForMatch($match))
                <a href="{{ $isRefereeForMatch($match) ? route('referee.competition.match.edit', [$match]) : route('organizations.competitions.matches.edit', [$organization, $competition, $match]) }}" 
                   class="text-blue-400 hover:text-blue-300 text-[10px] text-center whitespace-nowrap">
                    ✏️ {{ __('Edit') }}
                </a>
                @endif
            @endif
        </div>
    </div>
</div>

