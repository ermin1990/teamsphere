@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900">
    <!-- Header -->
    <div class="bg-gray-800/50 backdrop-blur-xl border-b border-gray-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Moje Lige</h1>
                        <p class="mt-1 text-sm text-gray-400">Lige gdje ste dodijeljeni kao sudija</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('referee.dashboard') }}" class="inline-flex items-center px-6 py-3 border border-gray-600 text-sm font-medium rounded-xl text-gray-300 bg-gray-700/50 hover:bg-gray-600/50 transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-gray-500/25">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                            </svg>
                            Kontrolna tabla
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($leagues->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($leagues as $league)
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 transform hover:scale-[1.02] overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="text-3xl">{{ $league->sport->icon }}</span>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-white">{{ $league->name }}</h3>
                                        <p class="text-sm text-gray-400">{{ $league->organization->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Ukupno Mečeva:</span>
                                    <span class="font-medium text-white">{{ $league->matches->count() }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Završeno:</span>
                                    <span class="font-medium text-white">{{ $league->matches->where('status', 'finished')->count() }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Zakazani:</span>
                                    <span class="font-medium text-white">{{ $league->matches->where('status', 'scheduled')->count() }}</span>
                                </div>
                            </div>

                            <a href="{{ route('referee.league.matches', $league) }}"
                               class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Pogledaj Mečeve
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-300">Nema dodijeljenih liga</h3>
                <p class="mt-1 text-sm text-gray-500">Još niste dodijeljeni kao sudija ni u jednoj ligi.</p>
                <div class="mt-6">
                    <a href="{{ route('referee.dashboard') }}" class="text-blue-400 hover:text-blue-300 transition-colors duration-200">
                        Nazad na Kontrolnu Tablu
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection