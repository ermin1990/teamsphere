@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Detalji Meča</h1>
            <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" class="text-blue-600 hover:text-blue-800">
                &larr; Nazad na takmičenje
            </a>
        </div>

        <!-- Scoreboard -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
            <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wider">Ekipni Meč</span>
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $teamMatch->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $teamMatch->status === 'completed' ? 'Završeno' : 'U toku' }}
                </span>
            </div>
            <div class="p-8 flex justify-between items-center">
                <div class="text-center flex-1">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $teamMatch->homeTeam->name }}</h2>
                </div>
                <div class="flex flex-col items-center px-8">
                    <div class="text-5xl font-black text-gray-900">
                        {{ $teamMatch->home_score }} : {{ $teamMatch->away_score }}
                    </div>
                </div>
                <div class="text-center flex-1">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $teamMatch->awayTeam->name }}</h2>
                </div>
            </div>
        </div>

        <!-- Individual Matches -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="font-bold text-gray-700">Pojedinačni Mečevi (Corbillon sistem)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poz.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domaćin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gost</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rezultat</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akcija</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($teamMatch->individualMatches->sortBy('match_order') as $match)
                        <tr class="{{ $match->status === 'completed' ? 'bg-gray-50' : 'hover:bg-gray-50' }} transition-colors duration-150 border-b last:border-b-0">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ $match->position_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($match->position_code === 'Dubl')
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $doublesPlayers['home_1']->name ?? '?' }}</span>
                                        <span class="font-medium">{{ $doublesPlayers['home_2']->name ?? '?' }}</span>
                                    </div>
                                @else
                                    <span class="font-medium {{ $match->status === 'completed' && $match->home_score > $match->away_score ? 'text-green-600 font-bold' : '' }}">
                                        {{ $match->homePlayer->name ?? 'Nepoznat igrač' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($match->position_code === 'Dubl')
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $doublesPlayers['away_1']->name ?? '?' }}</span>
                                        <span class="font-medium">{{ $doublesPlayers['away_2']->name ?? '?' }}</span>
                                    </div>
                                @else
                                    <span class="font-medium {{ $match->status === 'completed' && $match->away_score > $match->home_score ? 'text-green-600 font-bold' : '' }}">
                                        {{ $match->awayPlayer->name ?? 'Nepoznat igrač' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold">
                                @if($match->status === 'completed' || ($match->home_score > 0 || $match->away_score > 0))
                                    <span class="{{ $match->status === 'completed' ? 'text-gray-900' : 'text-blue-600' }}">
                                        {{ $match->home_score }} : {{ $match->away_score }}
                                    </span>
                                @else
                                    <span class="text-gray-400">- : -</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @if($match->status !== 'completed' && ($teamMatch->home_score < 4 && $teamMatch->away_score < 4))
                                        <button onclick="openQuickResultModal({{ $match->id }}, '{{ $match->position_code === 'Dubl' ? ($doublesPlayers['home_1']->name ?? '?') . ' / ' . ($doublesPlayers['home_2']->name ?? '?') : ($match->homePlayer->name ?? 'Nepoznat') }}', '{{ $match->position_code === 'Dubl' ? ($doublesPlayers['away_1']->name ?? '?') . ' / ' . ($doublesPlayers['away_2']->name ?? '?') : ($match->awayPlayer->name ?? 'Nepoznat') }}')"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 shadow-sm transition-colors duration-150">
                                            ⚡ Quick
                                        </button>
                                        <a href="{{ route('organizations.competitions.matches.show', [$organization, $competition, $match]) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors duration-150">
                                            Unesi rezultat
                                        </a>
                                    @elseif($match->status === 'completed')
                                        <a href="{{ route('organizations.competitions.matches.show', [$organization, $competition, $match]) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-colors duration-150">
                                            Pregled
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('organizations.competitions.partials.modals')
@include('organizations.competitions.partials.scripts')
@endsection