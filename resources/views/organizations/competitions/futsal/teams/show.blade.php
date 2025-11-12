<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $team->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    <a href="{{ route('organizations.show', $organization) }}" class="hover:underline">{{ $organization->name }}</a>
                    / <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" class="hover:underline">{{ $competition->name }}</a>
                    / <a href="{{ route('organizations.competitions.futsal.teams.index', [$organization, $competition]) }}" class="hover:underline">Timovi</a>
                </p>
            </div>
            @can('update', $organization)
                <div class="flex space-x-2">
                    <a href="{{ route('organizations.competitions.futsal.teams.edit', [$organization, $competition, $team]) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Uredi Tim
                    </a>
                </div>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Team Info Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <!-- Logo -->
                        <div class="text-center mb-6">
                            @if($team->logo_url)
                                <img src="{{ $team->logo_url }}" alt="{{ $team->name }}" class="w-32 h-32 rounded-full object-cover mx-auto mb-4">
                            @else
                                <div class="w-32 h-32 rounded-full mx-auto mb-4 flex items-center justify-center text-6xl"
                                     style="background-color: {{ $team->primary_color ?? '#3B82F6' }}">
                                    ⚽
                                </div>
                            @endif
                            <h3 class="text-xl font-bold text-gray-900">{{ $team->name }}</h3>
                            @if($team->short_name)
                                <p class="text-gray-500">{{ $team->short_name }}</p>
                            @endif
                        </div>

                        <!-- Team Details -->
                        <div class="space-y-3 mb-6">
                            @if($team->coach_name)
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Trener:</span>
                                    <p class="text-gray-900">{{ $team->coach_name }}</p>
                                </div>
                            @endif
                            @if($team->captain_name)
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Kapiten:</span>
                                    <p class="text-gray-900">{{ $team->captain_name }}</p>
                                </div>
                            @endif
                            @if($team->home_venue)
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Domaća dvorana:</span>
                                    <p class="text-gray-900">{{ $team->home_venue }}</p>
                                </div>
                            @endif
                            @if($team->primary_color || $team->secondary_color)
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Boje:</span>
                                    <div class="flex mt-1">
                                        @if($team->primary_color)
                                            <div class="w-8 h-8 rounded border border-gray-300 mr-2"
                                                 style="background-color: {{ $team->primary_color }}"></div>
                                        @endif
                                        @if($team->secondary_color)
                                            <div class="w-8 h-8 rounded border border-gray-300"
                                                 style="background-color: {{ $team->secondary_color }}"></div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Team Statistics -->
                        <div class="border-t pt-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Statistika</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Ukupno igrača:</span>
                                    <span class="font-medium">{{ $team->activePlayers->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Odigrane utakmice:</span>
                                    <span class="font-medium">{{ $team->getAllMatches()->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roster Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-900">Roster Igrača</h3>
                                @can('update', $organization)
                                    <button onclick="openAddPlayerModal()"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Dodaj Igrača
                                    </button>
                                @endcan
                            </div>

                            @if($team->activePlayers->isEmpty())
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nema igrača</h3>
                                    <p class="mt-1 text-sm text-gray-500">Dodajte igrače u roster.</p>
                                </div>
                            @else
                                <!-- Goalkeepers -->
                                @php
                                    $goalkeepers = $team->activePlayers->where('position', 'goalkeeper');
                                    $defenders = $team->activePlayers->where('position', 'defender');
                                    $midfielders = $team->activePlayers->where('position', 'midfielder');
                                    $forwards = $team->activePlayers->where('position', 'forward');
                                @endphp

                                @if($goalkeepers->count() > 0)
                                    <div class="mb-6">
                                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                            🧤 Golmani ({{ $goalkeepers->count() }})
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($goalkeepers as $player)
                                                @include('organizations.competitions.futsal.teams.partials.player-card', ['player' => $player])
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($defenders->count() > 0)
                                    <div class="mb-6">
                                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                            🛡️ Odbrana ({{ $defenders->count() }})
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($defenders as $player)
                                                @include('organizations.competitions.futsal.teams.partials.player-card', ['player' => $player])
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($midfielders->count() > 0)
                                    <div class="mb-6">
                                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                            ⚙️ Vezni red ({{ $midfielders->count() }})
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($midfielders as $player)
                                                @include('organizations.competitions.futsal.teams.partials.player-card', ['player' => $player])
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($forwards->count() > 0)
                                    <div class="mb-6">
                                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                            ⚽ Napad ({{ $forwards->count() }})
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @foreach($forwards as $player)
                                                @include('organizations.competitions.futsal.teams.partials.player-card', ['player' => $player])
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Player Modal -->
    @can('update', $organization)
        <div id="addPlayerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Dodaj Novog Igrača</h3>
                    <button onclick="closeAddPlayerModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('organizations.competitions.futsal.teams.players.add', [$organization, $competition, $team]) }}"
                      method="POST">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label for="player_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Ime i Prezime <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="player_name" name="player_name" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="jersey_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Broj Dresa <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="jersey_number" name="jersey_number" min="1" max="99" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                                Pozicija <span class="text-red-500">*</span>
                            </label>
                            <select id="position" name="position" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="goalkeeper">Golman</option>
                                <option value="defender">Odbrana</option>
                                <option value="midfielder">Vezni red</option>
                                <option value="forward">Napad</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                                Datum Rođenja
                            </label>
                            <input type="date" id="date_of_birth" name="date_of_birth"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">
                                Nacionalnost
                            </label>
                            <input type="text" id="nationality" name="nationality"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_captain" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Kapiten tima</span>
                            </label>
                        </div>

                        <div class="col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Napomene
                            </label>
                            <textarea id="notes" name="notes" rows="2"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeAddPlayerModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Odustani
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Dodaj Igrača
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    <script>
        function openAddPlayerModal() {
            document.getElementById('addPlayerModal').classList.remove('hidden');
        }

        function closeAddPlayerModal() {
            document.getElementById('addPlayerModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('addPlayerModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddPlayerModal();
            }
        });
    </script>
</x-app-layout>
