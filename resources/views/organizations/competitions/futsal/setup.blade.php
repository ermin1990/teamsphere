<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Futsal Takmičenje - {{ $competition->name }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                <a href="{{ route('organizations.show', $organization) }}" class="hover:underline">{{ $organization->name }}</a>
                / Setup
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Teams Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Timovi u Takmičenju</h3>
                        <a href="{{ route('organizations.competitions.futsal.teams.index', [$organization, $competition]) }}"
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Upravljaj Timovima →
                        </a>
                    </div>

                    @if($teams->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nema timova</h3>
                            <p class="mt-1 text-sm text-gray-500">Dodajte najmanje 2 tima da biste mogli kreirati takmičenje.</p>
                            <div class="mt-6">
                                <a href="{{ route('organizations.competitions.futsal.teams.create', [$organization, $competition]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    Dodaj Tim
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($teams as $team)
                                <div class="flex items-center p-3 border border-gray-200 rounded-lg">
                                    <div class="w-10 h-10 rounded-full mr-3 flex items-center justify-center text-2xl"
                                         style="background-color: {{ $team->primary_color ?? '#3B82F6' }}">
                                        ⚽
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $team->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $team->activePlayers->count() }} igrača</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-sm text-gray-600 text-center">
                            Ukupno: <span class="font-semibold">{{ $teams->count() }}</span> timova
                        </div>
                    @endif
                </div>
            </div>

            <!-- Competition Setup Form -->
            @if($teams->count() >= 2)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Postavke Takmičenja</h3>

                        <form action="{{ route('organizations.competitions.futsal.generate', [$organization, $competition]) }}"
                              method="POST" id="futsalSetupForm">
                            @csrf

                            <!-- Competition Format -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Format Takmičenja <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="relative">
                                        <input type="radio" id="format_league" name="competition_format" value="league" 
                                               class="peer sr-only" onchange="toggleFormat()" checked>
                                        <label for="format_league" 
                                               class="flex flex-col p-4 bg-gray-50 border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:bg-gray-100 transition-all">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-lg font-bold text-gray-900">🏆 Liga</span>
                                                <svg class="w-6 h-6 text-blue-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-600">Svi timovi igraju jedni protiv drugih. Rang lista određuje pobjednika.</p>
                                        </label>
                                    </div>

                                    <div class="relative">
                                        <input type="radio" id="format_tournament" name="competition_format" value="tournament" 
                                               class="peer sr-only" onchange="toggleFormat()">
                                        <label for="format_tournament" 
                                               class="flex flex-col p-4 bg-gray-50 border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:bg-gray-100 transition-all">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-lg font-bold text-gray-900">🥇 Turnir</span>
                                                <svg class="w-6 h-6 text-blue-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-600">Grupna faza + eliminaciona faza (polufinale, finale).</p>
                                        </label>
                                    </div>
                                </div>
                                @error('competition_format')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Round Robin Type -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Sistem Igranja <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="relative">
                                        <input type="radio" id="single_round" name="round_robin_type" value="single" 
                                               class="peer sr-only" checked>
                                        <label for="single_round" 
                                               class="flex flex-col p-4 bg-gray-50 border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-green-600 peer-checked:bg-green-50 hover:bg-gray-100 transition-all">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="font-bold text-gray-900">Jednokružni</span>
                                                <svg class="w-5 h-5 text-green-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-600">Svaki tim igra protiv svakog po jednom.</p>
                                        </label>
                                    </div>

                                    <div class="relative">
                                        <input type="radio" id="double_round" name="round_robin_type" value="double" 
                                               class="peer sr-only">
                                        <label for="double_round" 
                                               class="flex flex-col p-4 bg-gray-50 border-2 border-gray-300 rounded-lg cursor-pointer peer-checked:border-green-600 peer-checked:bg-green-50 hover:bg-gray-100 transition-all">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="font-bold text-gray-900">Dvokružni</span>
                                                <svg class="w-5 h-5 text-green-600 hidden peer-checked:block" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-600">Svaki tim igra protiv svakog dva puta (domaćin/gost).</p>
                                        </label>
                                    </div>
                                </div>
                                @error('round_robin_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tournament Settings (hidden initially) -->
                            <div id="tournamentSettings" style="display: none;">
                                <div class="border-t pt-6 space-y-6">
                                    <h4 class="font-semibold text-gray-900">Postavke Turnira</h4>

                                    <!-- Group Count -->
                                    <div>
                                        <label for="group_count" class="block text-sm font-medium text-gray-700 mb-2">
                                            Broj Grupa <span class="text-red-500">*</span>
                                        </label>
                                        <select id="group_count" name="group_count"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="2">2 grupe</option>
                                            <option value="3">3 grupe</option>
                                            <option value="4" selected>4 grupe</option>
                                            <option value="6">6 grupa</option>
                                            <option value="8">8 grupa</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">Timovi će biti ravnomjerno raspoređeni po grupama</p>
                                        @error('group_count')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Teams Advancing Per Group -->
                                    <div>
                                        <label for="teams_advancing_per_group" class="block text-sm font-medium text-gray-700 mb-2">
                                            Timova Koji Napreduju Po Grupi <span class="text-red-500">*</span>
                                        </label>
                                        <select id="teams_advancing_per_group" name="teams_advancing_per_group"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="1">1 tim (pobjednik grupe)</option>
                                            <option value="2" selected>2 tima (prvo i drugo mjesto)</option>
                                            <option value="3">3 tima</option>
                                            <option value="4">4 tima</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">Broj timova iz svake grupe koji napreduju u eliminacionu fazu</p>
                                        @error('teams_advancing_per_group')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Info Box -->
                            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="text-sm text-blue-800">
                                        <p class="font-medium mb-1">Raspored će biti generisan automatski</p>
                                        <p>Koristimo Berger algoritam za pravičan raspored utakmica. Možete kasnije urediti termine i lokacije.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                                <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}"
                                   class="px-6 py-3 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors font-medium">
                                    Odustani
                                </a>
                                <button type="submit"
                                        class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                                    Generiši Takmičenje
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleFormat() {
            const tournamentSettings = document.getElementById('tournamentSettings');
            const formatTournament = document.getElementById('format_tournament');

            if (formatTournament.checked) {
                tournamentSettings.style.display = 'block';
            } else {
                tournamentSettings.style.display = 'none';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleFormat();
        });
    </script>
</x-app-layout>
