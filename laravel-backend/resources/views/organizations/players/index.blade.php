<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $organization->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ __('Players') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('bulkAddModal').classList.remove('hidden')"
                   class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 shadow-lg">
                    Bulk Dodavanje
                </button>
                <a href="{{ route('organizations.players.create', $organization) }}"
                   class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    {{ __('Add Player') }}
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Filters Bar -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 mb-8">
            <form action="{{ route('organizations.players.index', $organization) }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search players...') }}"
                           class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-2.5 text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Team Filter -->
                <div>
                    <select name="team_id" onchange="this.form.submit()"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">{{ __('All Teams') }}</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <select name="type" onchange="this.form.submit()"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-xl px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="registered" {{ request('type') == 'registered' ? 'selected' : '' }}>{{ __('Registered Users') }}</option>
                        <option value="named" {{ request('type') == 'named' ? 'selected' : '' }}>{{ __('Named Players') }}</option>
                    </select>
                </div>

                <!-- Reset -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('organizations.players.index', $organization) }}" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-bold transition flex items-center justify-center">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

        @if($players->count() > 0)
            <form id="bulkDeleteForm" action="{{ route('organizations.players.bulk-delete', $organization) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 overflow-hidden shadow-xl">
                    <!-- Bulk Actions Header -->
                    <div id="bulkActionsHeader" class="hidden bg-red-900/20 border-b border-red-500/20 px-6 py-3 flex items-center justify-between">
                        <span class="text-red-400 text-sm font-bold">
                            <span id="selectedCount">0</span> {{ __('players selected') }}
                        </span>
                        <button type="submit" onclick="return confirm('{{ __('Are you sure you want to delete selected players?') }}')"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition">
                            {{ __('Delete Selected') }}
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-700 bg-gray-800 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Player') }}</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Teams') }}</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Type') }}</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Position') }}</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($players as $player)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="player_ids[]" value="{{ $player->id }}" class="player-checkbox rounded border-gray-700 bg-gray-800 text-blue-600 focus:ring-blue-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-white font-bold text-sm">{{ substr($player->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-white">{{ $player->name }}</div>
                                                    <div class="text-xs text-gray-400">{{ $player->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse($player->teams as $team)
                                                    <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 text-[10px] font-bold rounded-full border border-blue-500/20">
                                                        {{ $team->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-gray-500 text-xs italic">{{ __('No teams') }}</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($player->user)
                                                <span class="px-2 py-1 bg-green-500/10 text-green-400 text-[10px] font-bold rounded-full border border-green-500/20">
                                                    {{ __('REGISTERED') }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-yellow-500/10 text-yellow-400 text-[10px] font-bold rounded-full border border-yellow-500/20">
                                                    {{ __('NAMED') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ $player->position ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-3">
                                                <a href="{{ route('organizations.players.show', [$organization, $player]) }}" class="text-blue-400 hover:text-blue-300 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                <a href="{{ route('players.edit', $player) }}" class="text-yellow-400 hover:text-yellow-300 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <button type="button" 
                                                        onclick="deletePlayer('{{ route('players.destroy', $player) }}')"
                                                        class="text-red-400 hover:text-red-300 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $players->links() }}
                </div>
            </form>
        @else
            <!-- Empty State -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-12 border border-gray-700/50 text-center">
                <div class="w-20 h-20 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">{{ __('No players found') }}</h3>
                <p class="text-gray-400 mb-6">{{ __('Try adjusting your filters or add a new player.') }}</p>
                <a href="{{ route('organizations.players.create', $organization) }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold transition">
                    {{ __('Add Your First Player') }}
                </a>
            </div>
        @endif
    </div>

    <!-- Hidden Delete Form -->
    <form id="deletePlayerForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
    <script>
        function deletePlayer(url) {
            if (confirm('{{ __('Are you sure you want to delete this player?') }}')) {
                const form = document.getElementById('deletePlayerForm');
                form.action = url;
                form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const playerCheckboxes = document.querySelectorAll('.player-checkbox');
            const bulkActionsHeader = document.getElementById('bulkActionsHeader');
            const selectedCount = document.getElementById('selectedCount');

            function updateBulkActions() {
                const checkedCount = document.querySelectorAll('.player-checkbox:checked').length;
                if (checkedCount > 0) {
                    bulkActionsHeader.classList.remove('hidden');
                    selectedCount.textContent = checkedCount;
                } else {
                    bulkActionsHeader.classList.add('hidden');
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    playerCheckboxes.forEach(checkbox => {
                        checkbox.checked = selectAll.checked;
                    });
                    updateBulkActions();
                });
            }

            playerCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateBulkActions();
                    
                    // Update selectAll state
                    if (!this.checked) {
                        selectAll.checked = false;
                    } else {
                        const allChecked = Array.from(playerCheckboxes).every(cb => cb.checked);
                        selectAll.checked = allChecked;
                    }
                });
            });
        });
    </script>
    @endpush
</div>
    <!-- Bulk Add Modal -->
    <div id="bulkAddModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-2xl max-w-2xl w-full flex flex-col">
            <div class="p-6 border-b border-gray-700 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white">Bulk Dodavanje Igrača</h3>
                <button onclick="document.getElementById('bulkAddModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('organizations.players.bulk-store', $organization) }}" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-wider">Lista imena</label>
                    <textarea name="names_list" rows="10" 
                        class="w-full bg-gray-900/50 border-gray-700 text-white focus:border-blue-500 focus:ring-blue-500 rounded-xl text-sm"
                        placeholder="Upišite imena (jedno po redu ili odvojeno zarezom)...&#10;Ime Prezime&#10;Drugi Igrač, Treći Igrač" required></textarea>
                    <p class="mt-2 text-xs text-gray-500 italic">Sistem će kreirati nove igrače u organizaciji. Kasnije im možete dodijeliti klubove, emailove i ostale detalje.</p>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('bulkAddModal').classList.add('hidden')" class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-bold transition">
                        Odustani
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-lg shadow-blue-500/20">
                        Dodaj igrače
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>