<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between">
        <div class="flex items-center flex-1">
            <!-- Jersey Number Badge -->
            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg mr-3"
                 style="background-color: {{ $team->primary_color ?? '#3B82F6' }}; color: white;">
                {{ $player->jersey_number }}
            </div>
            
            <!-- Player Info -->
            <div class="flex-1">
                <div class="flex items-center">
                    <h5 class="font-semibold text-gray-900">{{ $player->player_name }}</h5>
                    @if($player->is_captain)
                        <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full font-medium">
                            ⭐ Kapiten
                        </span>
                    @endif
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    @if($player->nationality)
                        <span class="mr-2">{{ $player->nationality }}</span>
                    @endif
                    @if($player->date_of_birth)
                        <span>{{ \Carbon\Carbon::parse($player->date_of_birth)->age }} god</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        @can('update', $organization)
            <div class="flex items-center space-x-2 ml-2">
                <button onclick="openEditPlayerModal({{ $player->id }}, '{{ $player->player_name }}', {{ $player->jersey_number }}, '{{ $player->position }}', '{{ $player->date_of_birth }}', '{{ $player->nationality }}', {{ $player->is_captain ? 'true' : 'false' }}, '{{ $player->notes }}')"
                        class="text-blue-600 hover:text-blue-800" title="Uredi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <form action="{{ route('organizations.competitions.futsal.teams.players.remove', [$organization, $competition, $team, $player]) }}"
                      method="POST" class="inline"
                      onsubmit="return confirm('Da li ste sigurni da želite ukloniti ovog igrača?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-red-600 hover:text-red-800" title="Ukloni">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        @endcan
    </div>

    @if($player->notes)
        <div class="mt-2 text-xs text-gray-600 border-t pt-2">
            {{ $player->notes }}
        </div>
    @endif
</div>

<!-- Edit Player Modal (will be opened via JavaScript) -->
@can('update', $organization)
    @once
        <div id="editPlayerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Uredi Igrača</h3>
                    <button onclick="closeEditPlayerModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="editPlayerForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label for="edit_player_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Ime i Prezime <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="edit_player_name" name="player_name" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="edit_jersey_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Broj Dresa <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="edit_jersey_number" name="jersey_number" min="1" max="99" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="edit_position" class="block text-sm font-medium text-gray-700 mb-2">
                                Pozicija <span class="text-red-500">*</span>
                            </label>
                            <select id="edit_position" name="position" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="goalkeeper">Golman</option>
                                <option value="defender">Odbrana</option>
                                <option value="midfielder">Vezni red</option>
                                <option value="forward">Napad</option>
                            </select>
                        </div>

                        <div>
                            <label for="edit_date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                                Datum Rođenja
                            </label>
                            <input type="date" id="edit_date_of_birth" name="date_of_birth"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="edit_nationality" class="block text-sm font-medium text-gray-700 mb-2">
                                Nacionalnost
                            </label>
                            <input type="text" id="edit_nationality" name="nationality"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" id="edit_is_captain" name="is_captain" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Kapiten tima</span>
                            </label>
                        </div>

                        <div class="col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" id="edit_is_active" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Aktivan igrač</span>
                            </label>
                        </div>

                        <div class="col-span-2">
                            <label for="edit_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Napomene
                            </label>
                            <textarea id="edit_notes" name="notes" rows="2"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditPlayerModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Odustani
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Sačuvaj Izmjene
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditPlayerModal(playerId, name, jerseyNumber, position, dob, nationality, isCaptain, notes) {
                const form = document.getElementById('editPlayerForm');
                form.action = `{{ route('organizations.competitions.futsal.teams.show', [$organization, $competition, $team]) }}`.replace(/\/[^\/]*$/, '') + `/players/${playerId}`;
                
                document.getElementById('edit_player_name').value = name;
                document.getElementById('edit_jersey_number').value = jerseyNumber;
                document.getElementById('edit_position').value = position;
                document.getElementById('edit_date_of_birth').value = dob || '';
                document.getElementById('edit_nationality').value = nationality || '';
                document.getElementById('edit_is_captain').checked = isCaptain;
                document.getElementById('edit_is_active').checked = true;
                document.getElementById('edit_notes').value = notes || '';
                
                document.getElementById('editPlayerModal').classList.remove('hidden');
            }

            function closeEditPlayerModal() {
                document.getElementById('editPlayerModal').classList.add('hidden');
            }

            // Close modal on outside click
            document.getElementById('editPlayerModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditPlayerModal();
                }
            });
        </script>
    @endonce
@endcan
