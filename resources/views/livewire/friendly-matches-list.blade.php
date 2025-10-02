@php use Carbon\Carbon; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($matches as $match)
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm text-gray-400">{{ Carbon::parse($match->completed_at)->format('d.m.Y H:i') }}</div>
                <div class="text-xs bg-green-600/20 text-green-400 px-2 py-1 rounded-full">{{ __('Table Tennis') }}</div>
            </div>

            <div class="text-center mb-4">
                <div class="text-lg font-bold text-white mb-2">{{ $match->home_player_name }} vs {{ $match->away_player_name }}</div>
                <div class="text-2xl font-extrabold text-blue-400">
                    {{
                        collect($match->sets)->map(fn($set) => $set['home_score'].'-'.$set['away_score'])->implode(' | ')
                    }}
                </div>
            </div>

            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">{{ __('Sets:') }}</span>
                    <span class="text-white">{{ count($match->sets) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">{{ __('Duration:') }}</span>
                    <span class="text-white">{{ collect($match->set_durations)->sum() ? gmdate('i:s', collect($match->set_durations)->sum()) : '?' }}</span>
                </div>
            </div>

            <div class="text-center">
                <div class="inline-flex items-center px-3 py-1 bg-emerald-600/20 border border-emerald-500/30 rounded-full">
                    <span class="text-emerald-400 font-semibold text-sm">{{ __('Winner:') }} {{ $match->winner_name }}</span>
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
