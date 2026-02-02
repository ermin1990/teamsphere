{{-- Match Rules Section --}}
<div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-6 border border-gray-700/50 shadow-xl">
    <h3 class="text-xl font-bold text-white mb-4">{{ __('Match Rules') }}</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gray-700/30 rounded-lg p-4">
            <p class="text-gray-400 text-sm mb-1">{{ __('Sets to Win') }}</p>
            <p class="text-white text-2xl font-bold">{{ $competition->sets_to_win ?? 2 }}</p>
        </div>
        <div class="bg-gray-700/30 rounded-lg p-4">
            <p class="text-gray-400 text-sm mb-1">{{ __('Points per Set') }}</p>
            <p class="text-white text-2xl font-bold">{{ $competition->points_per_set ?? 11 }}</p>
        </div>
        <div class="bg-gray-700/30 rounded-lg p-4">
            <p class="text-gray-400 text-sm mb-1">{{ __('Win by Two') }}</p>
            <p class="text-white text-2xl font-bold">{{ $competition->must_win_by_two ? __('Yes') : __('No') }}</p>
        </div>
        @if($competition->type === 'tournament')
        <div class="bg-gray-700/30 rounded-lg p-4">
            <p class="text-gray-400 text-sm mb-1">{{ __('Win Points') }}</p>
            <p class="text-white text-2xl font-bold">{{ $competition->points_for_win ?? 2 }}</p>
        </div>
        @endif
    </div>
</div>
