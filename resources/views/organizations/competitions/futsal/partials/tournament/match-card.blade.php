{{-- Futsal Match Card --}}
@php
    $homeTeam = $match->homeTeam;
    $awayTeam = $match->awayTeam;
    $matchStatus = $match->status;
    $canEdit = $isOwner || $isRefereeForMatch($match);
@endphp

<div class="bg-gray-700/30 rounded-lg p-3 hover:bg-gray-700/50 transition-colors {{ $matchStatus === 'completed' ? 'border-l-4 border-green-500' : ($matchStatus === 'live' ? 'border-l-4 border-yellow-500' : 'border-l-4 border-gray-500') }}">
    <div class="flex items-center justify-between">
        {{-- Match Info --}}
        <div class="flex-1">
            <div class="flex items-center gap-3">
                {{-- Home Team --}}
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <span class="text-white font-medium text-sm truncate">{{ $homeTeam ? $homeTeam->name : 'TBD' }}</span>
                </div>

                {{-- Score --}}
                <div class="flex items-center gap-1 px-2 py-1 bg-gray-600/50 rounded text-xs font-mono min-w-[60px] justify-center">
                    @if($matchStatus === 'completed')
                        <span class="text-white">{{ $match->home_score ?? 0 }}</span>
                        <span class="text-gray-400">-</span>
                        <span class="text-white">{{ $match->away_score ?? 0 }}</span>
                    @elseif($matchStatus === 'live')
                        <span class="text-yellow-400">{{ $match->home_score ?? 0 }}</span>
                        <span class="text-gray-400">-</span>
                        <span class="text-yellow-400">{{ $match->away_score ?? 0 }}</span>
                    @else
                        <span class="text-gray-400">-</span>
                        <span class="text-gray-400">-</span>
                    @endif
                </div>

                {{-- Away Team --}}
                <div class="flex items-center gap-2 min-w-0 flex-1 justify-end">
                    <span class="text-white font-medium text-sm truncate">{{ $awayTeam ? $awayTeam->name : 'TBD' }}</span>
                </div>
            </div>

            {{-- Match Details --}}
            <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                @if($match->scheduled_at)
                    <span>{{ $match->scheduled_at->format('d.m H:i') }}</span>
                @endif
                @if($match->venue)
                    <span>•</span>
                    <span>{{ $match->venue }}</span>
                @endif
            </div>
        </div>

        {{-- Match Actions --}}
        @if($canEdit)
        <div class="flex items-center gap-1 ml-3">
            @if($matchStatus === 'scheduled')
                <button onclick="quickResult({{ $match->id }}, '{{ $homeTeam ? $homeTeam->name : 'TBD' }}', '{{ $awayTeam ? $awayTeam->name : 'TBD' }}')"
                        class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-colors"
                        title="Unesi rezultat">
                    📝
                </button>
            @elseif($matchStatus === 'live')
                <button onclick="quickResult({{ $match->id }}, '{{ $homeTeam ? $homeTeam->name : 'TBD' }}', '{{ $awayTeam ? $awayTeam->name : 'TBD' }}')"
                        class="px-2 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-xs rounded transition-colors"
                        title="Ažuriraj rezultat">
                    📝
                </button>
            @elseif($matchStatus === 'completed')
                <button onclick="editMatch({{ $match->id }})"
                        class="px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs rounded transition-colors"
                        title="Uredi meč">
                    ✏️
                </button>
            @endif

            {{-- Walkover Button --}}
            @if($isOwner && in_array($matchStatus, ['scheduled', 'live']))
                <button onclick="awardWalkover({{ $match->id }}, 'home')"
                        class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors"
                        title="Walkover domaćinu">
                    🏆
                </button>
            @endif
        </div>
        @endif
    </div>
</div>