<x-app-layout>

    {{-- Futsal Competition Header --}}
    <x-slot name="header">
        <!-- Mobile Layout -->
        <div class="block md:hidden">
            <div class="text-center">
                <h2 class="font-bold text-2xl bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent mb-2">
                    ⚽ {{ $competition->name }}
                </h2>
                <p class="text-gray-400 text-sm mb-4">{{ $organization->name }}</p>
                <div class="flex items-center justify-center gap-2 flex-wrap">
                    <span class="px-3 py-1 text-xs rounded-full
                        @if($competition->status === 'active') bg-green-500/20 text-green-400
                        @elseif($competition->status === 'draft') bg-yellow-500/20 text-yellow-400
                        @elseif($competition->status === 'completed') bg-blue-500/20 text-blue-400
                        @else bg-red-500/20 text-red-400 @endif"
                    >
                        @if($competition->status === 'active') ⚡ Aktivno
                        @elseif($competition->status === 'draft') 📝 Nacrt
                        @elseif($competition->status === 'completed') 🏆 Završeno
                        @else {{ ucfirst($competition->status) }} @endif
                    </span>
                    @if($competition->type === 'tournament')
                    <span class="px-3 py-1 text-xs rounded-full bg-purple-500/20 text-purple-400">
                        🏆 Turnir
                    </span>
                    @else
                    <span class="px-3 py-1 text-xs rounded-full bg-green-500/20 text-green-400">
                        🏅 Liga
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="hidden md:flex md:items-center md:justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent flex items-center gap-3">
                    ⚽ {{ $competition->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $organization->name }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-4 py-2 text-sm rounded-full
                    @if($competition->status === 'active') bg-green-500/20 text-green-400
                    @elseif($competition->status === 'draft') bg-yellow-500/20 text-yellow-400
                    @elseif($competition->status === 'completed') bg-blue-500/20 text-blue-400
                    @else bg-red-500/20 text-red-400 @endif"
                >
                    @if($competition->status === 'active') ⚡ Aktivno
                    @elseif($competition->status === 'draft') 📝 Nacrt
                    @elseif($competition->status === 'completed') 🏆 Završeno
                    @else {{ ucfirst($competition->status) }} @endif
                </span>
                @if($competition->type === 'tournament')
                <span class="px-4 py-2 text-sm rounded-full bg-purple-500/20 text-purple-400">
                    🏆 Turnir
                </span>
                @else
                <span class="px-4 py-2 text-sm rounded-full bg-green-500/20 text-green-400">
                    🏅 Liga
                </span>
                @endif
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                <p class="text-green-400">✅ {{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                <p class="text-red-400">❌ {{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Futsal Actions Bar --}}
            @if($isOwner)
            <div class="mb-6 flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
                {{-- Team Management --}}
                <a href="{{ route('organizations.competitions.futsal.teams.index', [$organization, $competition]) }}"
                   class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all duration-200 transform hover:scale-[1.02] font-semibold text-sm sm:text-base shadow-lg hover:shadow-green-500/50">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Timovi
                </a>
                
                @if($competition->status === 'draft' && $competition->futsalTeams->count() >= 2)
                    <a href="{{ route('organizations.competitions.futsal.setup', [$organization, $competition]) }}"
                       class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-all duration-200 transform hover:scale-[1.02] font-semibold text-sm sm:text-base shadow-lg hover:shadow-purple-500/50">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Generiši Raspored
                    </a>
                @endif
                
                @if($competition->status === 'active')
                    <a href="{{ route('organizations.competitions.futsal.schedule', [$organization, $competition]) }}"
                       class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 transform hover:scale-[1.02] font-semibold text-sm sm:text-base shadow-lg hover:shadow-blue-500/50">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Raspored
                    </a>
                    
                    <a href="{{ route('organizations.competitions.futsal.standings', [$organization, $competition]) }}"
                       class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-all duration-200 transform hover:scale-[1.02] font-semibold text-sm sm:text-base shadow-lg hover:shadow-yellow-500/50">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Tabela
                    </a>
                @endif
                
                <a href="{{ route('organizations.competitions.settings', [$organization, $competition]) }}"
                   class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors font-semibold text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Postavke
                </a>
                
                <a href="{{ route('organizations.show', $organization) }}"
                   class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-semibold text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Nazad
                </a>

                @if($competition->status === 'draft')
                <form action="{{ route('organizations.competitions.destroy', [$organization, $competition]) }}" 
                      method="POST" 
                      onsubmit="return confirm('Da li ste sigurni da želite obrisati ovo takmičenje? Ova akcija se ne može poništiti.')"
                      class="sm:ml-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-semibold text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Obriši
                    </button>
                </form>
                @endif
            </div>
            @endif

            <div class="space-y-6">
                {{-- Futsal Info Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Teams Count --}}
                    <div class="bg-gradient-to-br from-green-500/10 to-emerald-500/10 border border-green-500/20 rounded-xl p-6 hover:shadow-lg hover:shadow-green-500/20 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm font-medium">Timovi</p>
                                <p class="text-3xl font-bold text-green-400 mt-2">{{ $competition->futsalTeams->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Matches Count --}}
                    <div class="bg-gradient-to-br from-blue-500/10 to-cyan-500/10 border border-blue-500/20 rounded-xl p-6 hover:shadow-lg hover:shadow-blue-500/20 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm font-medium">Utakmice</p>
                                <p class="text-3xl font-bold text-blue-400 mt-2">
                                    {{ $competition->futsalMatches->where('status', 'completed')->count() }} / {{ $competition->futsalMatches->count() }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Odigrano / Ukupno</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Total Goals --}}
                    <div class="bg-gradient-to-br from-yellow-500/10 to-orange-500/10 border border-yellow-500/20 rounded-xl p-6 hover:shadow-lg hover:shadow-yellow-500/20 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm font-medium">Golova</p>
                                <p class="text-3xl font-bold text-yellow-400 mt-2">
                                    {{ $competition->futsalMatches->where('status', 'completed')->sum(function($match) {
                                        return ($match->home_score ?? 0) + ($match->away_score ?? 0);
                                    }) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Ukupno postignutih</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                                <span class="text-2xl">⚽</span>
                            </div>
                        </div>
                    </div>

                    {{-- Average Goals --}}
                    <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 border border-purple-500/20 rounded-xl p-6 hover:shadow-lg hover:shadow-purple-500/20 transition-all duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-sm font-medium">Prosjek Golova</p>
                                <p class="text-3xl font-bold text-purple-400 mt-2">
                                    @php
                                        $completedMatches = $competition->futsalMatches->where('status', 'completed')->count();
                                        $totalGoals = $competition->futsalMatches->where('status', 'completed')->sum(function($match) {
                                            return ($match->home_score ?? 0) + ($match->away_score ?? 0);
                                        });
                                        $average = $completedMatches > 0 ? number_format($totalGoals / $completedMatches, 1) : '0.0';
                                    @endphp
                                    {{ $average }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Po utakmici</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Competition Setup Wizard (Draft Status) --}}
                @if($competition->status === 'draft')
                <div class="bg-gradient-to-br from-yellow-500/10 to-orange-500/10 border-2 border-yellow-500/30 rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-yellow-400 mb-2">📝 Takmičenje u Pripremi</h3>
                            <p class="text-gray-300 mb-4">Pratite korake ispod da pokrenete takmičenje:</p>
                            
                            <div class="space-y-3">
                                {{-- Step 1: Add Teams --}}
                                <div class="flex items-start gap-3">
                                    @if($competition->futsalTeams->count() >= 2)
                                        <span class="text-green-400 text-xl flex-shrink-0">✅</span>
                                        <div>
                                            <p class="text-green-400 font-medium">Dodati timovi ({{ $competition->futsalTeams->count() }})</p>
                                            <p class="text-gray-400 text-sm">Svi timovi su spremni za takmičenje</p>
                                        </div>
                                    @else
                                        <span class="text-gray-500 text-xl flex-shrink-0">⭕</span>
                                        <div>
                                            <p class="text-gray-300 font-medium">Dodajte timove (minimum 2)</p>
                                            <p class="text-gray-400 text-sm">Trenutno: {{ $competition->futsalTeams->count() }} tim(a)</p>
                                            <a href="{{ route('organizations.competitions.futsal.teams.index', [$organization, $competition]) }}" class="text-green-400 hover:text-green-300 text-sm underline">
                                                Upravljaj timovima →
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                {{-- Step 2: Generate Schedule --}}
                                <div class="flex items-start gap-3">
                                    @if($competition->futsalMatches->count() > 0)
                                        <span class="text-green-400 text-xl flex-shrink-0">✅</span>
                                        <div>
                                            <p class="text-green-400 font-medium">Raspored generisan ({{ $competition->futsalMatches->count() }} utakmica)</p>
                                            <p class="text-gray-400 text-sm">Sve utakmice su spremne</p>
                                        </div>
                                    @else
                                        <span class="text-gray-500 text-xl flex-shrink-0">⭕</span>
                                        <div>
                                            <p class="text-gray-300 font-medium">Generiši raspored utakmica</p>
                                            @if($competition->futsalTeams->count() >= 2)
                                                <a href="{{ route('organizations.competitions.futsal.setup', [$organization, $competition]) }}" class="text-green-400 hover:text-green-300 text-sm underline">
                                                    Generiši raspored →
                                                </a>
                                            @else
                                                <p class="text-gray-400 text-sm">Prvo dodajte najmanje 2 tima</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- Step 3: Activate --}}
                                <div class="flex items-start gap-3">
                                    <span class="text-gray-500 text-xl flex-shrink-0">⭕</span>
                                    <div>
                                        <p class="text-gray-300 font-medium">Aktiviraj takmičenje</p>
                                        <p class="text-gray-400 text-sm">Takmičenje će se automatski aktivirati nakon generisanja rasporeda</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Tournament Content (Active Status) --}}
                @if($competition->status !== 'draft' && $competition->type === 'tournament')
                    @include('organizations.competitions.futsal.partials.tournament-content')
                @endif

                {{-- Main Content Area --}}
                @if($competition->status !== 'draft')
                <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
                    <h3 class="text-xl font-bold text-white mb-4">Upravljanje Takmičenjem</h3>
                    <p class="text-gray-400 mb-4">Koristite dugmad iznad za pregled rasporeda, tabela i upravljanje timovima.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('organizations.competitions.futsal.schedule', [$organization, $competition]) }}" 
                           class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 hover:bg-blue-500/20 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-blue-400">Raspored</p>
                                    <p class="text-xs text-gray-400">Pregled svih utakmica</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('organizations.competitions.futsal.standings', [$organization, $competition]) }}" 
                           class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4 hover:bg-yellow-500/20 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-yellow-400">Tabela</p>
                                    <p class="text-xs text-gray-400">Standings i rezultati</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('organizations.competitions.futsal.teams.index', [$organization, $competition]) }}" 
                           class="bg-green-500/10 border border-green-500/20 rounded-lg p-4 hover:bg-green-500/20 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-green-400">Timovi</p>
                                    <p class="text-xs text-gray-400">{{ $competition->futsalTeams->count() }} timova</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                @endif

            </div>

        </div>
    </div>

</x-app-layout>
