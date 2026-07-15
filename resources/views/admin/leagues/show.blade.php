@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-4 min-w-0">
            <a href="{{ route('admin.leagues.index') }}" class="p-2 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg transition-colors shrink-0">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="min-w-0">
                <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent truncate">
                    {{ $league->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $league->organization->name }} • {{ $league->sport->name ?? '' }} • {{ $league->type === 'tournament' ? 'Turnir' : 'Liga' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase
                @if($league->status === 'completed') bg-gray-700/60 text-gray-400
                @elseif($league->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                @elseif($league->status === 'active') bg-emerald-500/20 text-emerald-400
                @else bg-purple-500/20 text-purple-400 @endif">
                @if($league->status === 'completed') Zatvoreno
                @elseif($league->status === 'in_progress') U toku
                @elseif($league->status === 'active') Aktivno
                @else Draft @endif
            </span>
            @if($league->status !== 'completed')
                <form method="POST" action="{{ route('admin.leagues.close', $league) }}" onsubmit="return confirm('Zatvoriti \'{{ $league->name }}\'? Takmičenje više neće biti aktivno, organizator neće moći dalje unositi rezultate.');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-colors border border-red-500/30 font-medium">
                        Zatvori takmičenje
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats -->
    @php
        $matchesCount = $league->is_team_based ? $league->teamMatches()->count() : $league->matches()->count();
        $completedCount = $league->is_team_based
            ? $league->teamMatches()->whereIn('status', ['completed', 'forfeited'])->count()
            : $league->matches()->whereIn('status', ['completed', 'forfeited'])->count();
        $participantsCount = $league->is_team_based ? $league->teams()->count() : $league->players()->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <p class="text-2xl font-bold text-white">{{ $matchesCount }}</p>
            <p class="text-gray-400 text-sm">Mečeva ukupno</p>
        </div>
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <p class="text-2xl font-bold text-white">{{ $completedCount }} / {{ $matchesCount }}</p>
            <p class="text-gray-400 text-sm">Odigrano</p>
        </div>
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <p class="text-2xl font-bold text-white">{{ $participantsCount }}</p>
            <p class="text-gray-400 text-sm">{{ $league->is_team_based ? 'Timova' : 'Igrača' }}</p>
        </div>
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <p class="text-2xl font-bold text-white">{{ $league->created_at->format('d.m.Y') }}</p>
            <p class="text-gray-400 text-sm">Kreirano</p>
        </div>
    </div>

    <!-- Details -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Detalji</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Organizacija</label>
                <a href="{{ route('admin.organizations.show', $league->organization) }}" class="text-blue-400 hover:text-blue-300 bg-gray-700/50 block px-4 py-3 rounded-lg">{{ $league->organization->name }}</a>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Sport</label>
                <p class="text-white bg-gray-700/50 px-4 py-3 rounded-lg">{{ $league->sport->name ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Grad</label>
                <p class="text-white bg-gray-700/50 px-4 py-3 rounded-lg">{{ $league->city->name ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Javno vidljivo</label>
                <p class="text-white bg-gray-700/50 px-4 py-3 rounded-lg">{{ $league->is_public ? 'Da' : 'Ne' }}</p>
            </div>
        </div>
    </div>

    <a href="{{ route('public.leagues.show', $league) }}" target="_blank" class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 text-sm font-medium">
        Otvori javnu stranicu takmičenja →
    </a>
</div>
@endsection
