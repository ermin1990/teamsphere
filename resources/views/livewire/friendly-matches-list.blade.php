@php use Carbon\Carbon; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($matches as $match)
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm text-gray-400">{{ Carbon::parse($match->completed_at)->format('d.m.Y H:i') }}</div>
                <div class="text-xs bg-green-600/20 text-green-400 px-2 py-1 rounded-full">Stoni Tenis</div>
            </div>

            <div class="text-center mb-4">
                <div class="flex items-center justify-center space-x-4 mb-2">
                    <!-- Home Team -->
                    <div class="flex flex-col items-center space-y-2">
                        @if(str_contains($match->home_player_name, ' / '))
                            @php [$h1, $h2] = explode(' / ', $match->home_player_name); @endphp
                            <div class="flex space-x-1">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">{{ substr($h1, 0, 1) }}</span>
                                </div>
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">{{ substr($h2, 0, 1) }}</span>
                                </div>
                            </div>
                            <span class="text-white text-sm font-medium">{{ $h1 }} & {{ $h2 }}</span>
                        @else
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ substr($match->home_player_name, 0, 1) }}</span>
                            </div>
                            @if($match->home_player_id)
                                <a href="{{ route('organizations.players.show', ['organization' => $organization->slug, 'player' => $match->home_player_id]) }}" class="text-white hover:text-blue-400 transition text-sm font-medium">{{ $match->home_player_name }}</a>
                            @else
                                <span class="text-white text-sm font-medium">{{ $match->home_player_name }}</span>
                            @endif
                        @endif
                    </div>

                    <div class="text-white font-bold text-lg">vs</div>

                    <!-- Away Team -->
                    <div class="flex flex-col items-center space-y-2">
                        @if(str_contains($match->away_player_name, ' / '))
                            @php [$a1, $a2] = explode(' / ', $match->away_player_name); @endphp
                            <div class="flex space-x-1">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">{{ substr($a1, 0, 1) }}</span>
                                </div>
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-xs">{{ substr($a2, 0, 1) }}</span>
                                </div>
                            </div>
                            <span class="text-white text-sm font-medium">{{ $a1 }} & {{ $a2 }}</span>
                        @else
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ substr($match->away_player_name, 0, 1) }}</span>
                            </div>
                            @if($match->away_player_id)
                                <a href="{{ route('organizations.players.show', ['organization' => $organization->slug, 'player' => $match->away_player_id]) }}" class="text-white hover:text-blue-400 transition text-sm font-medium">{{ $match->away_player_name }}</a>
                            @else
                                <span class="text-white text-sm font-medium">{{ $match->away_player_name }}</span>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="text-2xl font-extrabold text-blue-400">
                    {{
                        collect($match->sets)->map(fn($set) => $set['home_score'].'-'.$set['away_score'])->implode(' | ')
                    }}
                </div>
            </div>

            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Setovi:</span>
                    <span class="text-white">{{ count($match->sets) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">{{ __('Duration:') }}</span>
                    <span class="text-white">{{ collect($match->set_durations)->sum() ? gmdate('i:s', collect($match->set_durations)->sum()) : '?' }}</span>
                </div>
            </div>

            <div class="text-center space-y-2">
                <div class="inline-flex items-center px-3 py-1 bg-emerald-600/20 border border-emerald-500/30 rounded-full">
                    <span class="text-emerald-400 font-semibold text-sm">
                        {{ __('Winner:') }}
                        @if(str_contains($match->winner_name, ' / '))
                            @php [$p1, $p2] = explode(' / ', $match->winner_name); @endphp
                            {{ $p1 }} & {{ $p2 }}
                        @elseif(str_contains($match->home_player_name, ' / ') && str_contains($match->home_player_name, $match->winner_name))
                            {{ str_replace(' / ', ' & ', $match->home_player_name) }}
                        @elseif(str_contains($match->away_player_name, ' / ') && str_contains($match->away_player_name, $match->winner_name))
                            {{ str_replace(' / ', ' & ', $match->away_player_name) }}
                        @else
                            {{ $match->winner_name }}
                        @endif
                    </span>
                </div>
                <div>
                    <a href="{{ route('organizations.friendly-matches.show', ['organization' => $organization->slug, 'match' => $match->id]) }}" class="text-blue-400 hover:underline text-xs">Detalji meča</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <p class="text-gray-400">{{ __('No friendly matches played yet.') }}</p>
        </div>
    @endforelse
</div>
