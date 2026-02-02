@extends('layouts.app')

@section('title', __('Team Management - ') . $league->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $league->name }}</h1>
                    <p class="text-gray-400 mt-1">{{ __('Team Management') }} - {{ $organization->name }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('leagues.show', $league) }}"
                       class="bg-gray-700/50 hover:bg-gray-600/50 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
                        {{ __('Back to League') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            @php
                // Get players that are not already in the league
                $currentPlayerIds = $league->players->pluck('id')->toArray();
                $availablePlayers = $organization->players
                    ->filter(function($player) use ($currentPlayerIds) {
                        return !in_array($player->id, $currentPlayerIds);
                    });
            @endphp

            @if($availablePlayers->count() > 0)
            <!-- Add Players to League Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-xl font-semibold text-white mb-6">{{ __('Add Players to League') }}</h3>

                <form id="addPlayersForm" action="{{ route('leagues.addPlayers', $league) }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="space-y-3 mb-6 max-h-96 overflow-y-auto">
                        @foreach($availablePlayers as $player)
                        <div class="flex items-center p-4 bg-gray-700/30 rounded-lg">
                            <input type="checkbox" id="player_{{ $player->id }}" name="player_ids[]" value="{{ $player->id }}"
                                   class="w-4 h-4 text-green-600 bg-gray-700 border-gray-600 rounded focus:ring-green-500 focus:ring-2">
                            <label for="player_{{ $player->id }}" class="ml-3 flex-1">
                                <div class="text-white font-medium">{{ $player->name }}</div>
                                @if($player->email)
                                <div class="text-gray-400 text-sm">{{ $player->email }}</div>
                                @endif
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-400">
                            {{ __('Selected:') }} <span id="selectedCount">0</span> {{ __('players') }}
                        </div>
                        <button type="submit"
                                class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-green-500/25">
                            {{ __('Add Selected Players') }}
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <!-- Current League Players Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-xl font-semibold text-white mb-6">{{ __('League Players') }} ({{ $league->players->count() }})</h3>

                @if($league->players->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($league->players as $player)
                    <div class="flex items-center justify-between p-4 bg-gray-700/30 rounded-lg">
                        <div>
                            <h4 class="text-white font-medium">{{ $player->name }}</h4>
                            @if($player->email)
                            <div class="text-gray-400 text-sm">{{ $player->email }}</div>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400">
                                Active
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h4 class="text-white font-semibold mb-2">{{ __('No players in league') }}</h4>
                    <p class="text-gray-400">{{ __('Add players to the league first.') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Teams Management Section -->
        @if($league->players->count() > 0)
        <div class="mt-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-white">{{ __('Team Formation') }}</h3>
                    <button onclick="openCreateTeamModal()"
                            class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-blue-500/25">
                        {{ __('Create Team') }}
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="teamsContainer">
                    @foreach($league->teams as $team)
                    <div class="bg-gray-700/30 rounded-lg p-4 border border-gray-600/50">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-white font-medium">{{ $team->name }}</h4>
                            <div class="flex space-x-2">
                                <button onclick="editTeam({{ $team->id }}, '{{ $team->name }}')"
                                        class="text-blue-400 hover:text-blue-300 text-sm">
                                    {{ __('Edit') }}
                                </button>
                                <button onclick="deleteTeam({{ $team->id }})"
                                        class="text-red-400 hover:text-red-300 text-sm">
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                        <div class="space-y-2">
                            @if($team->players->count() > 0)
                                @foreach($team->players as $player)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-300">{{ $player->name }}</span>
                                    <button onclick="removePlayerFromTeam({{ $team->id }}, {{ $player->id }})"
                                            class="text-red-400 hover:text-red-300">
                                        ×
                                    </button>
                                </div>
                                @endforeach
                            @else
                                <p class="text-gray-500 text-sm">{{ __('No players in this team') }}</p>
                            @endif
                        </div>
                        <button onclick="openAddPlayerModal({{ $team->id }}, '{{ $team->name }}')"
                                class="mt-3 w-full bg-gray-600/50 hover:bg-gray-600/70 text-white px-3 py-2 rounded text-sm transition-all duration-200">
                            {{ __('Add Player') }}
                        </button>
                    </div>
                    @endforeach
                </div>

                @if($league->teams->count() === 0)
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-white font-semibold mb-2">{{ __('No teams created yet') }}</h4>
                    <p class="text-gray-400">{{ __('Create teams and assign players to start the league.') }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Start League Section -->
        @if($league->status === 'draft' && $league->teams->count() >= 2)
        <div class="mt-8">
            <div class="bg-gradient-to-r from-green-600/20 to-green-700/20 backdrop-blur-xl rounded-2xl p-6 border border-green-500/30 shadow-xl">
                <div class="text-center">
                    <h3 class="text-xl font-semibold text-white mb-2">{{ __('Ready to Start League!') }}</h3>
                    <p class="text-gray-300 mb-6">{{ __('You have') }} {{ $league->teams->count() }} {{ __('teams ready. Click below to generate matches and start the league.') }}</p>

                    <form method="POST" action="{{ route('leagues.start', $league) }}"
                          onsubmit="return confirm('{{ __('Are you sure you want to start this league? This will generate matches and standings.') }}')"
                          class="inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-8 py-4 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-green-500/25 text-lg">
                            {{ __('Start League & Generate Matches') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Create Team Modal -->
<div id="createTeamModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-2xl p-6 w-full max-w-md border border-gray-700">
            <h3 class="text-xl font-semibold text-white mb-4">{{ __('Create New Team') }}</h3>
            <form id="createTeamForm" action="{{ route('leagues.teams.store', $league) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">{{ __('Team Name') }}</label>
                    <input type="text" name="name" required
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">{{ __('Description (Optional)') }}</label>
                    <textarea name="description" rows="3"
                              class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCreateTeamModal()"
                            class="px-4 py-2 text-gray-300 hover:text-white transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
                        {{ __('Create Team') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Player to Team Modal -->
<div id="addPlayerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-2xl p-6 w-full max-w-md border border-gray-700">
            <h3 class="text-xl font-semibold text-white mb-4">{{ __('Add Player to') }} <span id="teamName"></span></h3>
            <form id="addPlayerForm">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">{{ __('Select Player') }}</label>
                    <select id="playerSelect" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">{{ __('Choose a player...') }}</option>
                        @foreach($league->players->where('team_id', null) as $player)
                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddPlayerModal()"
                            class="px-4 py-2 text-gray-300 hover:text-white transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
                        {{ __('Add Player') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Team management functions
function openCreateTeamModal() {
    document.getElementById('createTeamModal').classList.remove('hidden');
}

function closeCreateTeamModal() {
    document.getElementById('createTeamModal').classList.add('hidden');
    document.getElementById('createTeamForm').reset();
}

function openAddPlayerModal(teamId, teamName) {
    document.getElementById('teamName').textContent = teamName;
    document.getElementById('addPlayerForm').setAttribute('data-team-id', teamId);
    document.getElementById('addPlayerModal').classList.remove('hidden');
}

function editTeam(teamId, teamName) {
    // For now, just show an alert. In a real implementation, you'd open an edit modal
    alert('Edit team functionality not yet implemented. Team: ' + teamName);
}

function deleteTeam(teamId) {
    if (confirm('Are you sure you want to delete this team?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("leagues.teams.destroy", [$league, ":teamId"]) }}'.replace(':teamId', teamId);

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        document.body.appendChild(form);
        form.submit();
    }
}

function removePlayerFromTeam(teamId, playerId) {
    if (confirm('Are you sure you want to remove this player from the team?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("leagues.teams.remove-player", [$league, ":teamId", ":playerId"]) }}'.replace(':teamId', teamId).replace(':playerId', playerId);

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        document.body.appendChild(form);
        form.submit();
    }
}

function closeAddPlayerModal() {
    document.getElementById('addPlayerModal').classList.add('hidden');
    document.getElementById('addPlayerForm').reset();
}

// Handle add player form submission
document.getElementById('addPlayerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const teamId = this.getAttribute('data-team-id');
    const playerId = document.getElementById('playerSelect').value;

    if (!playerId) return;

    // Here you would make an AJAX request to add the player to the team
    // For now, we'll just reload the page
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("leagues.teams.add-player", [$league, ":teamId"]) }}'.replace(':teamId', teamId);

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrf);

    const playerInput = document.createElement('input');
    playerInput.type = 'hidden';
    playerInput.name = 'player_id';
    playerInput.value = playerId;
    form.appendChild(playerInput);

    document.body.appendChild(form);
    form.submit();
});

// Update selected count for players
document.querySelectorAll('input[name="player_ids[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const selectedCount = document.querySelectorAll('input[name="player_ids[]"]:checked').length;
        document.getElementById('selectedCount').textContent = selectedCount;
    });
});
</script>
@endsection
<parameter name="filePath">c:\Users\ermin\Projekti\teamsphere\resources\views\organizations\leagues\team-management.blade.php