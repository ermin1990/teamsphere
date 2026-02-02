@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Upravljanje Sportovima
            </h2>
            <p class="text-gray-400 mt-2">Uključite ili isključite sportove koji su dostupni korisnicima</p>
        </div>
        <div class="text-left sm:text-right">
            <p class="text-sm text-gray-400">Ukupno sportova</p>
            <p class="text-2xl font-bold text-white">{{ $sports->count() }}</p>
        </div>
    </div>

    <!-- Sports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($sports as $sport)
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-[1.02] {{ !$sport->active ? 'opacity-60' : '' }}">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <div class="text-4xl">{{ $sport->icon }}</div>
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $sport->name }}</h3>
                        <p class="text-gray-400 text-sm">{{ Str::limit($sport->description, 50) }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    @if($sport->active)
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-green-400 text-sm font-medium">Aktivan</span>
                    @else
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-red-400 text-sm font-medium">Neaktivan</span>
                    @endif
                </div>
            </div>

            <!-- Sport Rules Preview -->
            <div class="bg-gray-700/30 rounded-xl p-4 mb-4">
                <h4 class="text-white font-semibold text-sm mb-2">Pravila:</h4>
                <div class="text-xs text-gray-400 space-y-1">
                    @if($sport->isPointsBased())
                        <div>• {{ $sport->getMaxPointsPerGame() }} poena po gemu</div>
                        <div>• {{ $sport->getGamesToWin() }} gemova za pobjedu</div>
                    @elseif($sport->isSetsGamesBased())
                        <div>• {{ $sport->getGamesPerSet() }} gemova po setu</div>
                        <div>• {{ $sport->getSetsToWin() }} seta za pobjedu</div>
                    @elseif($sport->isTimeBased())
                        <div>• {{ $sport->getRule('periods') }}x {{ $sport->getRule('period_duration') / 60 }}min</div>
                        <div>• {{ $sport->getPlayersPerTeam() }} igrača po timu</div>
                    @endif
                </div>
            </div>

            <!-- Action Button -->
            <form method="POST" action="{{ route('admin.sports.toggle', $sport) }}">
                @csrf
                <button type="submit" class="w-full px-4 py-3 rounded-xl font-medium transition-all duration-200 {{ $sport->active ? 'bg-red-500/20 hover:bg-red-500/30 text-red-400 border border-red-500/30' : 'bg-green-500/20 hover:bg-green-500/30 text-green-400 border border-green-500/30' }}">
                    <span class="flex items-center justify-center space-x-2">
                        @if($sport->active)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Isključi Sport</span>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Uključi Sport</span>
                        @endif
                    </span>
                </button>
            </form>
        </div>
        @endforeach
    </div>

    <!-- Stats Summary -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
        <h3 class="text-xl font-bold text-white mb-4">Statistike</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent mb-2">
                    {{ $sports->where('active', true)->count() }}
                </div>
                <p class="text-gray-400">Aktivni sportovi</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold bg-gradient-to-r from-red-400 to-pink-400 bg-clip-text text-transparent mb-2">
                    {{ $sports->where('active', false)->count() }}
                </div>
                <p class="text-gray-400">Neaktivni sportovi</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
                    {{ $sports->count() }}
                </div>
                <p class="text-gray-400">Ukupno sportova</p>
            </div>
        </div>
    </div>
</div>
@endsection
