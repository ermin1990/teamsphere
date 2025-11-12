<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">
                    ⚙️ Postavke Futsal Takmičenja
                </h2>
                <p class="text-gray-400 mt-1">{{ $competition->name }} - {{ $organization->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                    <p class="text-green-400">✅ {{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                    <p class="text-red-400">❌ {{ session('error') }}</p>
                </div>
            @endif

            @if($competition->status !== 'draft')
                <div class="mb-6 bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4">
                    <p class="text-yellow-400">⚠️ Postavke se ne mogu mijenjati nakon što takmičenje počne.</p>
                </div>
            @endif

            <form method="POST" action="{{ route('organizations.competitions.update-settings', [$organization, $competition]) }}" class="space-y-6">
                @csrf

                {{-- Match Duration Settings --}}
                <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
                    <h3 class="text-xl font-bold text-green-400 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Trajanje Utakmice
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Match Duration --}}
                        <div>
                            <label for="match_duration" class="block text-sm font-medium text-gray-300 mb-2">
                                Trajanje utakmice (minute)
                            </label>
                            <input type="number" 
                                   id="match_duration" 
                                   name="match_duration" 
                                   value="{{ old('match_duration', $competition->match_duration ?? 40) }}"
                                   min="20" 
                                   max="90"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('match_duration') border-red-500 @enderror"
                                   {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                            @error('match_duration')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Standardno: 40 minuta (2x20)</p>
                        </div>

                        {{-- Halftime Duration --}}
                        <div>
                            <label for="halftime_duration" class="block text-sm font-medium text-gray-300 mb-2">
                                Poluvrijeme (minute)
                            </label>
                            <input type="number" 
                                   id="halftime_duration" 
                                   name="halftime_duration" 
                                   value="{{ old('halftime_duration', $competition->halftime_duration ?? 10) }}"
                                   min="5" 
                                   max="20"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('halftime_duration') border-red-500 @enderror"
                                   {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                            @error('halftime_duration')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Standardno: 10 minuta</p>
                        </div>
                    </div>
                </div>

                {{-- Team & Roster Settings --}}
                <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
                    <h3 class="text-xl font-bold text-blue-400 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Tim i Igrači
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Max Players on Pitch --}}
                        <div>
                            <label for="max_players_on_pitch" class="block text-sm font-medium text-gray-300 mb-2">
                                Maksimalno igrača na terenu
                            </label>
                            <input type="number" 
                                   id="max_players_on_pitch" 
                                   name="max_players_on_pitch" 
                                   value="{{ old('max_players_on_pitch', $competition->max_players_on_pitch ?? 5) }}"
                                   min="3" 
                                   max="11"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('max_players_on_pitch') border-red-500 @enderror"
                                   {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                            @error('max_players_on_pitch')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Uključujući golmana (standardno: 5)</p>
                        </div>

                        {{-- Min Players on Pitch --}}
                        <div>
                            <label for="min_players_on_pitch" class="block text-sm font-medium text-gray-300 mb-2">
                                Minimalno igrača za nastavak
                            </label>
                            <input type="number" 
                                   id="min_players_on_pitch" 
                                   name="min_players_on_pitch" 
                                   value="{{ old('min_players_on_pitch', $competition->min_players_on_pitch ?? 3) }}"
                                   min="2" 
                                   max="7"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('min_players_on_pitch') border-red-500 @enderror"
                                   {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                            @error('min_players_on_pitch')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Ispod ovog broja, utakmica se prekida</p>
                        </div>

                        {{-- Max Substitutes --}}
                        <div>
                            <label for="max_substitutes" class="block text-sm font-medium text-gray-300 mb-2">
                                Maksimalno zamjena (klupa)
                            </label>
                            <input type="number" 
                                   id="max_substitutes" 
                                   name="max_substitutes" 
                                   value="{{ old('max_substitutes', $competition->max_substitutes ?? 7) }}"
                                   min="0" 
                                   max="15"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('max_substitutes') border-red-500 @enderror"
                                   {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                            @error('max_substitutes')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Broj igrača na klupi</p>
                        </div>

                        {{-- Unlimited Substitutions --}}
                        <div class="flex items-center">
                            <div class="flex-1">
                                <label for="unlimited_substitutions" class="block text-sm font-medium text-gray-300 mb-2">
                                    Neograničene zamjene
                                </label>
                                <div class="flex items-center gap-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" 
                                               name="unlimited_substitutions" 
                                               value="1" 
                                               {{ old('unlimited_substitutions', $competition->unlimited_substitutions ?? 1) == 1 ? 'checked' : '' }}
                                               class="text-green-600 focus:ring-green-500 @error('unlimited_substitutions') border-red-500 @enderror"
                                               {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                                        <span class="ml-2 text-gray-300">Da</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" 
                                               name="unlimited_substitutions" 
                                               value="0" 
                                               {{ old('unlimited_substitutions', $competition->unlimited_substitutions ?? 1) == 0 ? 'checked' : '' }}
                                               class="text-green-600 focus:ring-green-500"
                                               {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                                        <span class="ml-2 text-gray-300">Ne</span>
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Futsal obično dozvoljava neograničene zamjene</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Disciplinary Settings --}}
                <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
                    <h3 class="text-xl font-bold text-yellow-400 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Disciplina (Kartoni)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Yellow Card Limit --}}
                        <div>
                            <label for="yellow_card_limit" class="block text-sm font-medium text-gray-300 mb-2">
                                Žuti kartoni do isključenja
                            </label>
                            <input type="number" 
                                   id="yellow_card_limit" 
                                   name="yellow_card_limit" 
                                   value="{{ old('yellow_card_limit', $competition->yellow_card_limit ?? 2) }}"
                                   min="1" 
                                   max="5"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('yellow_card_limit') border-red-500 @enderror"
                                   {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                            @error('yellow_card_limit')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Standardno: 2 žuta = 1 crveni</p>
                        </div>

                        {{-- Red Card Suspension --}}
                        <div>
                            <label for="red_card_suspension" class="block text-sm font-medium text-gray-300 mb-2">
                                Suspenzija za crveni karton (utakmice)
                            </label>
                            <input type="number" 
                                   id="red_card_suspension" 
                                   name="red_card_suspension" 
                                   value="{{ old('red_card_suspension', $competition->red_card_suspension ?? 1) }}"
                                   min="0" 
                                   max="10"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('red_card_suspension') border-red-500 @enderror"
                                   {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                            @error('red_card_suspension')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Broj utakmica suspenzije nakon crvenog</p>
                        </div>
                    </div>
                </div>

                {{-- Overtime & Penalty Settings --}}
                <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
                    <h3 class="text-xl font-bold text-purple-400 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Produžeci i Penali
                    </h3>

                    <div class="space-y-4">
                        {{-- Has Overtime --}}
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="has_overtime" 
                                       value="1"
                                       {{ old('has_overtime', $competition->has_overtime ?? false) ? 'checked' : '' }}
                                       class="rounded text-green-600 focus:ring-green-500 @error('has_overtime') border-red-500 @enderror"
                                       {{ $competition->status !== 'draft' ? 'disabled' : '' }}
                                       onchange="document.getElementById('overtime_duration').disabled = !this.checked">
                                <span class="ml-2 text-gray-300">Omogući produžetke (extra time)</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-400 ml-6">Za knockout fazu ili važne utakmice</p>
                        </div>

                        {{-- Overtime Duration --}}
                        <div class="ml-6">
                            <label for="overtime_duration" class="block text-sm font-medium text-gray-300 mb-2">
                                Trajanje produžetaka (minute)
                            </label>
                            <input type="number" 
                                   id="overtime_duration" 
                                   name="overtime_duration" 
                                   value="{{ old('overtime_duration', $competition->overtime_duration ?? 10) }}"
                                   min="5" 
                                   max="30"
                                   class="w-full md:w-64 bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('overtime_duration') border-red-500 @enderror"
                                   {{ ($competition->status !== 'draft' || !($competition->has_overtime ?? false)) ? 'disabled' : '' }}>
                            @error('overtime_duration')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Standardno: 10 minuta (2x5)</p>
                        </div>

                        {{-- Has Penalty Shootout --}}
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="has_penalty_shootout" 
                                       value="1"
                                       {{ old('has_penalty_shootout', $competition->has_penalty_shootout ?? true) ? 'checked' : '' }}
                                       class="rounded text-green-600 focus:ring-green-500"
                                       {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                                <span class="ml-2 text-gray-300">Omogući izvođenje penala</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-400 ml-6">Ako je rezultat i dalje neriješen nakon produžetaka</p>
                        </div>
                    </div>
                </div>

                {{-- Standings Points System --}}
                <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-6">
                    <h3 class="text-xl font-bold text-orange-400 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Bodovni Sistem
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Points for Win --}}
                        <div>
                            <label for="points_for_win" class="block text-sm font-medium text-gray-300 mb-2">
                                Bodova za pobjedu
                            </label>
                            <input type="number" 
                                   id="points_for_win" 
                                   name="points_for_win" 
                                   value="{{ old('points_for_win', $competition->points_for_win ?? 3) }}"
                                   min="0" 
                                   max="10"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('points_for_win') border-red-500 @enderror"
                                   {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                            @error('points_for_win')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Standardno: 3</p>
                        </div>

                        {{-- Points for Draw --}}
                        <div>
                            <label for="points_for_draw" class="block text-sm font-medium text-gray-300 mb-2">
                                Bodova za remi
                            </label>
                            <input type="number" 
                                   id="points_for_draw" 
                                   name="points_for_draw" 
                                   value="{{ old('points_for_draw', $competition->points_for_draw ?? 1) }}"
                                   min="0" 
                                   max="10"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('points_for_draw') border-red-500 @enderror"
                                   {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                            @error('points_for_draw')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Standardno: 1</p>
                        </div>

                        {{-- Points for Loss --}}
                        <div>
                            <label for="points_for_loss" class="block text-sm font-medium text-gray-300 mb-2">
                                Bodova za poraz
                            </label>
                            <input type="number" 
                                   id="points_for_loss" 
                                   name="points_for_loss" 
                                   value="{{ old('points_for_loss', $competition->points_for_loss ?? 0) }}"
                                   min="0" 
                                   max="10"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent @error('points_for_loss') border-red-500 @enderror"
                                   {{ $competition->status !== 'draft' ? 'disabled' : '' }}>
                            @error('points_for_loss')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Standardno: 0</p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}"
                       class="inline-flex items-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-semibold">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Nazad
                    </a>

                    @if($competition->status === 'draft')
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg transition-all duration-200 transform hover:scale-[1.02] font-semibold shadow-lg hover:shadow-green-500/50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sačuvaj Postavke
                        </button>
                    @endif
                </div>
            </form>

        </div>
    </div>

</x-app-layout>
