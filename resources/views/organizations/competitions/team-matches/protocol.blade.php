@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">PROTOKOL MEČA</h1>
                <p class="text-gray-400 mt-1">Unesite sastave ekipa za predstojeći susret</p>
            </div>
            <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-2 rounded-xl transition-all flex items-center border border-gray-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Nazad na takmičenje
            </a>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl border border-gray-700/50 shadow-2xl overflow-hidden">
            <!-- Match Header -->
            <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 p-8 border-b border-gray-700/50">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="text-center md:text-right flex-1">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-4 shadow-lg shadow-blue-500/20">
                            <span class="text-2xl font-black text-white">{{ substr($teamMatch->homeTeam->name, 0, 1) }}</span>
                        </div>
                        <h2 class="text-2xl font-black text-white">{{ $teamMatch->homeTeam->name }}</h2>
                        <p class="text-blue-400 font-bold text-sm uppercase tracking-widest mt-1">Domaćin (A, B, C)</p>
                    </div>

                    <div class="flex flex-col items-center">
                        <div class="bg-gray-900/80 px-6 py-2 rounded-full border border-gray-700 text-gray-400 font-black text-xl">VS</div>
                        <div class="mt-2 text-xs text-gray-500 font-medium uppercase tracking-tighter">Kolo {{ $teamMatch->round }}</div>
                    </div>

                    <div class="text-center md:text-left flex-1">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-600 rounded-2xl mb-4 shadow-lg shadow-purple-500/20">
                            <span class="text-2xl font-black text-white">{{ substr($teamMatch->awayTeam->name, 0, 1) }}</span>
                        </div>
                        <h2 class="text-2xl font-black text-white">{{ $teamMatch->awayTeam->name }}</h2>
                        <p class="text-purple-400 font-bold text-sm uppercase tracking-widest mt-1">Gost (X, Y, Z)</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('organizations.competitions.team-matches.store-protocol', [$organization, $competition, $teamMatch]) }}" method="POST" class="p-8">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Home Team Lineup -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-1 h-6 bg-blue-500 rounded-full"></div>
                            <h3 class="font-black text-xl text-white uppercase tracking-tight">Sastav Domaćina</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-gray-900/50 p-4 rounded-2xl border border-gray-700/30">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Igrač A</label>
                                <select name="home_a" class="block w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Odaberi igrača</option>
                                    @foreach($homePlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="bg-gray-900/50 p-4 rounded-2xl border border-gray-700/30">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Igrač B</label>
                                <select name="home_b" class="block w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Odaberi igrača</option>
                                    @foreach($homePlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="bg-gray-900/50 p-4 rounded-2xl border border-gray-700/30">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Igrač C</label>
                                <select name="home_c" class="block w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Odaberi igrača</option>
                                    @foreach($homePlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="bg-blue-600/5 p-5 rounded-2xl border border-blue-500/20 mt-2">
                                <h4 class="text-xs font-black text-blue-400 uppercase mb-3 tracking-widest">Dubl par</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <select name="home_dubl_1" class="block w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm" required>
                                        <option value="">Igrač 1</option>
                                        @foreach($homePlayers as $player)
                                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                                        @endforeach
                                    </select>
                                    <select name="home_dubl_2" class="block w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm" required>
                                        <option value="">Igrač 2</option>
                                        @foreach($homePlayers as $player)
                                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Away Team Lineup -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-1 h-6 bg-purple-500 rounded-full"></div>
                            <h3 class="font-black text-xl text-white uppercase tracking-tight">Sastav Gosta</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-gray-900/50 p-4 rounded-2xl border border-gray-700/30">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Igrač X</label>
                                <select name="away_x" class="block w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-purple-500 focus:border-purple-500" required>
                                    <option value="">Odaberi igrača</option>
                                    @foreach($awayPlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="bg-gray-900/50 p-4 rounded-2xl border border-gray-700/30">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Igrač Y</label>
                                <select name="away_y" class="block w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-purple-500 focus:border-purple-500" required>
                                    <option value="">Odaberi igrača</option>
                                    @foreach($awayPlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="bg-gray-900/50 p-4 rounded-2xl border border-gray-700/30">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Igrač Z</label>
                                <select name="away_z" class="block w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-purple-500 focus:border-purple-500" required>
                                    <option value="">Odaberi igrača</option>
                                    @foreach($awayPlayers as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="bg-purple-600/5 p-5 rounded-2xl border border-purple-500/20 mt-2">
                                <h4 class="text-xs font-black text-purple-400 uppercase mb-3 tracking-widest">Dubl par</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <select name="away_dubl_1" class="block w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-purple-500 focus:border-purple-500 text-sm" required>
                                        <option value="">Igrač 1</option>
                                        @foreach($awayPlayers as $player)
                                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                                        @endforeach
                                    </select>
                                    <select name="away_dubl_2" class="block w-full bg-gray-800 border-gray-700 text-white rounded-xl focus:ring-purple-500 focus:border-purple-500 text-sm" required>
                                        <option value="">Igrač 2</option>
                                        @foreach($awayPlayers as $player)
                                            <option value="{{ $player->id }}">{{ $player->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-700/50 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-gray-500 text-sm italic">
                        * Nakon čuvanja protokola, sistem će automatski generisati 7 pojedinačnih mečeva.
                    </div>
                    <button type="submit" class="w-full md:w-auto bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest transition-all shadow-xl shadow-blue-500/20 transform hover:scale-[1.02] active:scale-[0.98]">
                        Sačuvaj i Generiši Mečeve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection