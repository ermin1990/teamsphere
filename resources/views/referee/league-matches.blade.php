@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $league->name }}</h1>
                        <p class="mt-1 text-sm text-gray-500">{{ $league->organization->name }} • {{ $league->sport->name }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('referee.leagues') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Nazad na Lige
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ukupno Mečeva</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $league->matches->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Završeno</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $league->matches->where('status', 'completed')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Otkazano</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $league->matches->where('status', 'cancelled')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">U Toku</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $league->matches->where('status', 'in_progress')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matches List -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Mečevi</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Svi mečevi u ovoj ligi</p>
            </div>

            @if($matches->count() > 0)
                @foreach($matches as $round => $roundMatches)
                    <div class="border-b border-gray-200 last:border-b-0">
                        <div class="px-4 py-3 sm:px-6 bg-gray-50">
                            <h4 class="text-sm font-medium text-gray-900">Kolo {{ $round }}</h4>
                        </div>
                        <ul class="divide-y divide-gray-200">
                            @foreach($roundMatches as $match)
                                <li>
                                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <span class="text-lg">{{ $league->sport->icon }}</span>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        @if($league->is_team_based)
                                                            {{ $match->homeTeam?->name ?? 'TBD' }} vs {{ $match->awayTeam?->name ?? 'TBD' }}
                                                        @else
                                                            {{ $match->homePlayer?->name ?? 'TBD' }} vs {{ $match->awayPlayer?->name ?? 'TBD' }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex items-center space-x-4">
                                                <div class="text-right">
                                                    @if($match->status === 'completed')
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                            {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                                        </span>
                                                    @elseif($match->status === 'in_progress')
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 animate-pulse">
                                                            Uživo: {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                                        </span>
                                                    @elseif($match->status === 'cancelled')
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                            Otkazano
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                            {{ ucfirst($match->status) }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('referee.match.show', [$league, $match]) }}"
                                                       class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                        Pogledaj
                                                    </a>
                                                    @if($match->status !== 'completed')
                                                        <a href="{{ route('referee.match.live', [$league, $match]) }}"
                                                           class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                                            🎯 Uživo
                                                        </a>
                                                        <a href="{{ route('referee.match.edit', [$league, $match]) }}"
                                                           class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                            Uredi
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @else
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nema mečeva</h3>
                    <p class="mt-1 text-sm text-gray-500">Ova liga još nema mečeva.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection