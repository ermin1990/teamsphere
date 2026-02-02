<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    @if(isset($competition))
                        <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" class="text-blue-400 hover:text-blue-300 transition-colors flex items-center text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Nazad na {{ $competition->name }}
                        </a>
                    @else
                        <a href="{{ route('organizations.show', $organization) }}" class="text-gray-400 hover:text-white transition-colors flex items-center text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Nazad na Organizaciju
                        </a>
                    @endif
                    <span class="text-gray-600">•</span>
                    <h2 class="font-semibold text-xl text-white leading-tight">
                        {{ __('Timovi') }}
                    </h2>
                </div>
                <p class="text-gray-400 text-sm">Upravljajte timovima i njihovim rosterima</p>
            </div>
            <div class="flex items-center gap-3">
                <form action="{{ route('organizations.teams.suggest', $organization) }}" method="POST">
                    @csrf
                    @if(isset($competition))
                        <input type="hidden" name="competition_id" value="{{ $competition->id }}">
                    @endif
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all duration-200 shadow-lg shadow-blue-500/20 font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Predloži Timove
                    </button>
                </form>
                <a href="{{ route('organizations.teams.create', [$organization, 'competition_id' => isset($competition) ? $competition->id : null]) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-all duration-200 shadow-lg shadow-emerald-500/20 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Novi Tim
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl text-blue-400">
                    {{ session('info') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($teams as $team)
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden group hover:border-emerald-500/30 transition-all duration-300">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('organizations.teams.edit', [$organization, $team]) }}" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('organizations.teams.destroy', [$organization, $team]) }}" method="POST" onsubmit="return confirm('Da li ste sigurni?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <h3 class="text-xl font-bold text-white mb-2">{{ $team->name }}</h3>
                            <p class="text-gray-400 text-sm line-clamp-2 mb-4">{{ $team->description ?: 'Nema opisa.' }}</p>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-700/50">
                                <div class="flex items-center text-gray-400 text-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 01-9-4.992m9 4.992a5.942 5.942 0 00-.144-1.992M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ $team->players_count }} Igrača
                                </div>
                                <a href="{{ route('organizations.teams.roster', [$organization, $team]) }}" class="text-emerald-400 hover:text-emerald-300 text-sm font-medium transition-colors">
                                    Upravljaj Rosterom →
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 p-12 text-center">
                        <div class="w-20 h-20 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-white font-bold text-xl mb-2">Nema kreiranih timova</h3>
                        <p class="text-gray-400 mb-6">Kreirajte svoj prvi tim da biste mogli organizovati lige.</p>
                        <a href="{{ route('organizations.teams.create', $organization) }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-all font-medium">
                            Kreiraj Prvi Tim
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
