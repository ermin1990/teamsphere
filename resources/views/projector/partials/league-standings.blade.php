<!-- League Standings Table for Projector -->
@if(count($competition->standings ?? []) > 0)
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-2xl">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-white flex items-center gap-3">
            <span class="text-4xl">📊</span>
            Tabela - {{ $competition->name }}
        </h2>
        <div class="text-right">
            <p class="text-gray-400 text-sm">{{ $competition->organization->name }}</p>
            <p class="text-gray-500 text-xs">{{ $competition->sport->name }}</p>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b-2 border-blue-500/50">
                    <th class="text-left py-4 px-4 text-gray-400 font-bold text-lg">#</th>
                    <th class="text-left py-4 px-4 text-gray-400 font-bold text-lg">
                        {{ $competition->is_team_based ? 'Tim' : 'Igrač' }}
                    </th>
                    <th class="text-center py-4 px-3 text-gray-400 font-bold text-lg">O</th>
                    <th class="text-center py-4 px-3 text-gray-400 font-bold text-lg">P</th>
                    <th class="text-center py-4 px-3 text-gray-400 font-bold text-lg">N</th>
                    <th class="text-center py-4 px-3 text-gray-400 font-bold text-lg">I</th>
                    @if(!$competition->is_team_based)
                    <th class="text-center py-4 px-3 text-gray-400 font-bold text-lg">S+</th>
                    <th class="text-center py-4 px-3 text-gray-400 font-bold text-lg">S-</th>
                    @endif
                    <th class="text-center py-4 px-4 text-gray-400 font-bold text-lg">Bod</th>
                </tr>
            </thead>
            <tbody>
                @foreach(collect($competition->standings)->sortBy('position') as $standing)
                <tr class="border-b border-gray-700/30 hover:bg-gray-700/20 transition-colors">
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-2">
                            @if(data_get($standing, 'position', 0) <= 3)
                                <span class="text-3xl">
                                    @if(data_get($standing, 'position', 0) == 1) 🥇
                                    @elseif(data_get($standing, 'position', 0) == 2) 🥈
                                    @else 🥉
                                    @endif
                                </span>
                            @endif
                            <span class="text-white font-bold text-xl">{{ data_get($standing, 'position', 0) }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <span class="text-white font-semibold text-xl">
                            @if($competition->is_team_based)
                                {{ data_get($standing, 'team.name', 'TBD') }}
                            @else
                                {{ data_get($standing, 'player.name', 'TBD') }}
                            @endif
                        </span>
                    </td>
                    <td class="py-4 px-3 text-center text-gray-300 text-lg">{{ data_get($standing, 'played', 0) }}</td>
                    <td class="py-4 px-3 text-center text-green-400 font-bold text-lg">{{ data_get($standing, 'won', 0) }}</td>
                    <td class="py-4 px-3 text-center text-yellow-400 font-bold text-lg">{{ data_get($standing, 'drawn', 0) }}</td>
                    <td class="py-4 px-3 text-center text-red-400 font-bold text-lg">{{ data_get($standing, 'lost', 0) }}</td>
                    @if(!$competition->is_team_based)
                    <td class="py-4 px-3 text-center text-blue-300 text-lg">{{ data_get($standing, 'sets_won', 0) }}</td>
                    <td class="py-4 px-3 text-center text-orange-300 text-lg">{{ data_get($standing, 'sets_lost', 0) }}</td>
                    @endif
                    <td class="py-4 px-4 text-center">
                        <span class="text-blue-400 font-black text-2xl">{{ data_get($standing, 'points', 0) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Legend -->
    <div class="mt-6 pt-4 border-t border-gray-700/50">
        <div class="flex flex-wrap gap-6 justify-center text-sm">
            <div class="flex items-center gap-2">
                <span class="text-gray-400">O:</span>
                <span class="text-gray-300">Odigrano</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-green-400">P:</span>
                <span class="text-gray-300">Pobjeda</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-yellow-400">N:</span>
                <span class="text-gray-300">Neriješeno</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-red-400">I:</span>
                <span class="text-gray-300">Izgubljeno</span>
            </div>
            @if(!$competition->is_team_based)
            <div class="flex items-center gap-2">
                <span class="text-blue-300">S+:</span>
                <span class="text-gray-300">Dobijeni setovi</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-orange-300">S-:</span>
                <span class="text-gray-300">Izgubljeni setovi</span>
            </div>
            @endif
            <div class="flex items-center gap-2">
                <span class="text-blue-400">Bod:</span>
                <span class="text-gray-300">Bodovi</span>
            </div>
        </div>
    </div>
</div>
@else
<div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-12 border border-gray-700/50 shadow-2xl text-center">
    <p class="text-gray-400 text-2xl">Tabela nije dostupna</p>
</div>
@endif
