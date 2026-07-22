<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Postavke Takmičenja</h1>
                        <p class="text-gray-300">{{ $competition->name }}</p>
                    </div>
                    <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors text-center sm:w-auto w-full">
                        Nazad
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                <p class="text-green-400">{{ session('success') }}</p>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                <ul class="list-disc list-inside text-red-400">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Public Visibility Toggle -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <form method="POST" action="{{ route('organizations.competitions.update', [$organization, $competition]) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    
                    <input type="hidden" name="is_public" value="0">
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-white font-medium">Javna Vidljivost</h4>
                            <p class="text-sm text-gray-400">Učini ovo takmičenje vidljivim na javnoj web stranici</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   name="is_public"
                                   value="1"
                                   {{ $competition->isPublic() ? 'checked' : '' }}
                                   class="sr-only peer"
                                   onchange="this.form.submit()">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Registration Open Toggle -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <form method="POST" action="{{ route('organizations.competitions.update', [$organization, $competition]) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="registration_open" value="0">

                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-white font-medium">Otvori prijave igračima</h4>
                            <p class="text-sm text-gray-400">Prikaži ovu ligu na listi "Takmičenja" da joj se igrači mogu prijaviti</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   name="registration_open"
                                   value="1"
                                   {{ $competition->registration_open ? 'checked' : '' }}
                                   class="sr-only peer"
                                   onchange="this.form.submit()">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </form>
            </div>

            @if($competition->sport->isPointsBased())
            <!-- Quick Presets -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <h3 class="text-xl font-semibold text-white mb-4">Brzi Predlošci</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button onclick="applyPreset('standard')"
                            class="bg-blue-600/20 hover:bg-blue-600/30 border-blue-500 border-2 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">🏆 Standard (11 poena)</h4>
                        <p class="text-sm text-gray-300">Najbolji od 3, 11 poena, završetak pri 10</p>
                    </button>
                    <button onclick="applyPreset('extended')"
                            class="bg-purple-600/20 hover:bg-purple-600/30 border-purple-500 border-2 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">🎯 Produženo (15 poena)</h4>
                        <p class="text-sm text-gray-300">Najbolji od 3, 15 poena, završetak pri 14</p>
                    </button>
                    <button onclick="applyPreset('classic')"
                            class="bg-green-600/20 hover:bg-green-600/30 border-green-500 border-2 text-white p-4 rounded-lg transition-colors text-left">
                        <h4 class="font-semibold mb-2">⚡ Classic (21 pts)</h4>
                        <p class="text-sm text-gray-300">Best of 3, 21 points, deuce at 20</p>
                    </button>
                </div>
            </div>
            @endif

            <form action="{{ route('organizations.competitions.update-settings', [$organization, $competition]) }}" method="POST" onsubmit="return validateSettingsForm(event)">
                @csrf

                <fieldset>

                <!-- Basic Info -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Osnovne Informacije</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Competition Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-white mb-2">
                                Naziv Takmičenja <span class="text-red-400">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $competition->name) }}" 
                                   required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-white mb-2">
                                Kategorija
                            </label>
                            <select id="category_id" 
                                    name="category_id" 
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="">Bez kategorije</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $competition->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city_id" class="block text-sm font-medium text-white mb-2">Grad</label>
                            <select id="city_id" name="city_id"
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="">Bez grada</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id', $competition->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @if($organization->city)
                                <p class="mt-1 text-xs text-gray-400">Ostavi na "Bez grada" da koristiš grad organizacije ({{ $organization->city->name }}).</p>
                            @endif
                        </div>

                        <!-- Season -->
                        <div>
                            <label for="season_id" class="block text-sm font-medium text-white mb-2">Sezona</label>
                            <select id="season_id" name="season_id"
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                <option value="">Bez sezone</option>
                                @foreach($seasons as $season)
                                    <option value="{{ $season->id }}" {{ old('season_id', $competition->season_id) == $season->id ? 'selected' : '' }}>
                                        {{ $season->name }}{{ $season->is_active ? ' (aktivna)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Registration deadline -->
                        <div>
                            <label for="registration_deadline" class="block text-sm font-medium text-white mb-2">Prijave otvorene do</label>
                            <input type="datetime-local" id="registration_deadline" name="registration_deadline"
                                   value="{{ old('registration_deadline', optional($competition->registration_deadline)->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>

                        <!-- End date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-white mb-2">Datum do kada se igra {{ $competition->isLeague() ? 'liga' : 'turnir' }}</label>
                            <input type="date" id="end_date" name="end_date"
                                   value="{{ old('end_date', optional($competition->end_date)->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-white mb-2">Opis</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                  placeholder="Opcionalni opis takmičenja">{{ old('description', $competition->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label for="location" class="block text-sm font-medium text-white mb-2">Lokacija/adresa igranja</label>
                            <input type="text" id="location" name="location"
                                   value="{{ old('location', $competition->location) }}"
                                   placeholder="Npr. SC Mejdan, teren 2"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <div>
                            <label for="organizer_contact" class="block text-sm font-medium text-white mb-2">Kontakt organizatora</label>
                            <input type="text" id="organizer_contact" name="organizer_contact"
                                   value="{{ old('organizer_contact', $competition->organizer_contact) }}"
                                   placeholder="Telefon ili email"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <div>
                            <label for="entry_fee" class="block text-sm font-medium text-white mb-2">Kotizacija/cijena učešća</label>
                            <input type="text" id="entry_fee" name="entry_fee"
                                   value="{{ old('entry_fee', $competition->entry_fee) }}"
                                   placeholder="Npr. 20 KM po sezoni, besplatno"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                    </div>
                </div>

                @if($competition->sport->isPointsBased())
                <!-- Match Format (stoni tenis - bodovanje po setovima do X poena) -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Format Meča</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Sets to Win -->
                        <div>
                            <label for="sets_to_win" class="block text-sm font-medium text-white mb-2">
                                Setova za Pobjedu <span class="text-red-400">*</span>
                            </label>
                            <select id="sets_to_win" name="sets_to_win" required
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" {{ old('sets_to_win', $competition->sets_to_win ?? 3) == 1 ? 'selected' : '' }}>1 (Najbolji od 1)</option>
                                <option value="2" {{ old('sets_to_win', $competition->sets_to_win ?? 3) == 2 ? 'selected' : '' }}>2 (Najbolji od 3)</option>
                                <option value="3" {{ old('sets_to_win', $competition->sets_to_win ?? 3) == 3 ? 'selected' : '' }}>3 (Najbolji od 5) - Standard</option>
                                <option value="4" {{ old('sets_to_win', $competition->sets_to_win ?? 3) == 4 ? 'selected' : '' }}>4 (Najbolji od 7)</option>
                            </select>
                            <p class="text-gray-400 text-xs mt-1">Broj setova koji igrač treba da osvoji za pobjedu u meču</p>
                        </div>

                        <!-- Points per Set -->
                        <div>
                            <label for="points_per_set" class="block text-sm font-medium text-white mb-2">
                                Poena po Setu <span class="text-red-400">*</span>
                            </label>
                            <select id="points_per_set" name="points_per_set" required
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="7" {{ old('points_per_set', $competition->points_per_set ?? 11) == 7 ? 'selected' : '' }}>7 poena</option>
                                <option value="11" {{ old('points_per_set', $competition->points_per_set ?? 11) == 11 ? 'selected' : '' }}>11 poena (Standard)</option>
                                <option value="15" {{ old('points_per_set', $competition->points_per_set ?? 11) == 15 ? 'selected' : '' }}>15 poena</option>
                                <option value="21" {{ old('points_per_set', $competition->points_per_set ?? 11) == 21 ? 'selected' : '' }}>21 poen (Klasično)</option>
                            </select>
                            <p class="text-gray-400 text-xs mt-1">Poeni potrebni za pobjedu u setu</p>
                        </div>

                        <!-- Deuce At -->
                        <div>
                            <label for="deuce_at" class="block text-sm font-medium text-white mb-2">
                                Deuce na
                            </label>
                            <input type="number" id="deuce_at" name="deuce_at"
                                   value="{{ old('deuce_at', $competition->deuce_at ?? 10) }}"
                                   min="5" max="20"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Poeni na kojima počinje deuce</p>
                        </div>

                        <!-- Must Win by Two -->
                        <div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="must_win_by_two" name="must_win_by_two" value="1"
                                       {{ old('must_win_by_two', $competition->must_win_by_two ?? true) ? 'checked' : '' }}
                                       class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-white font-medium">Mora pobijediti sa dva poena</span>
                                    <p class="text-gray-400 text-xs">Igrač mora pobijediti sa dva poena razlike</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Tiebreak Settings -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Postavke Tiebreak-a</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Has Tiebreak -->
                        <div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" id="has_tiebreak" name="has_tiebreak" value="1"
                                       {{ old('has_tiebreak', $competition->has_tiebreak ?? false) ? 'checked' : '' }}
                                       onchange="toggleTiebreakPoints()"
                                       class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-white font-medium">Koristi tiebreak u finalnom setu</span>
                                    <p class="text-gray-400 text-xs">Omogući tiebreak u finalnom setu kada je rezultat jednak</p>
                                </div>
                            </label>
                        </div>

                        <!-- Tiebreak Points -->
                        <div id="tiebreakPointsDiv">
                            <label for="tiebreak_points" class="block text-sm font-medium text-white mb-2">
                                Tiebreak Poeni
                            </label>
                            <input type="number" id="tiebreak_points" name="tiebreak_points"
                                   value="{{ old('tiebreak_points', $competition->tiebreak_points ?? 7) }}"
                                   min="5" max="15"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Poeni potrebni za pobjedu u tiebreak-u</p>
                        </div>
                    </div>
                </div>
                @elseif($competition->sport->isSetsGamesBased())
                <!-- Match Format (Tenis/Padel - gemovi/setovi, fiksna pravila) -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Format Meča</h3>

                    <div>
                        <label for="sets_to_win" class="block text-sm font-medium text-white mb-2">
                            Setova za Pobjedu <span class="text-red-400">*</span>
                        </label>
                        <select id="sets_to_win" name="sets_to_win" required
                                class="w-full max-w-xs px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1" {{ old('sets_to_win', $competition->sets_to_win ?? 2) == 1 ? 'selected' : '' }}>1 (Najbolji od 1)</option>
                            <option value="2" {{ old('sets_to_win', $competition->sets_to_win ?? 2) == 2 ? 'selected' : '' }}>2 (Najbolji od 3) - Standard</option>
                            <option value="3" {{ old('sets_to_win', $competition->sets_to_win ?? 2) == 3 ? 'selected' : '' }}>3 (Najbolji od 5)</option>
                        </select>
                        <p class="text-gray-400 text-xs mt-1">Broj setova koji igrač/par treba da osvoji za pobjedu u meču</p>
                    </div>

                    <p class="text-gray-400 text-sm mt-6 bg-gray-900/40 rounded-xl p-4 border border-gray-700/30">
                        Set se igra do 6 gemova (razlika 2), sa tie-breakom na 6-6 - standardna pravila za {{ $competition->sport->name }}.
                        Gem se igra po klasičnom sistemu (0, 15, 30, 40, deuce/prednost).
                    </p>
                </div>
                @endif

                <!-- Tournament Settings -->
                @if($competition->type === 'tournament')
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Postavke Turnira</h3>
                    
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Players Advancing per Group -->
                        <div>
                            <label for="players_advancing_per_group" class="block text-sm font-medium text-white mb-2">
                                Igrača koji Napreduju po Grupi <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="players_advancing_per_group" name="players_advancing_per_group" 
                                   value="{{ old('players_advancing_per_group', $competition->players_advancing_per_group ?? 2) }}"
                                   min="1" max="4" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Broj igrača koji prolaze u eliminacionu fazu iz svake grupe</p>
                            @error('players_advancing_per_group')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Group Rounds -->
                        <div>
                            <label for="group_rounds" class="block text-sm font-medium text-white mb-2">
                                Broj Krugova u Grupama <span class="text-red-400">*</span>
                            </label>
                            <select id="group_rounds" name="group_rounds" required
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" {{ old('group_rounds', $competition->group_rounds ?? 1) == 1 ? 'selected' : '' }}>1 Krug - Svako protiv svakoga jednom</option>
                                <option value="2" {{ old('group_rounds', $competition->group_rounds ?? 1) == 2 ? 'selected' : '' }}>2 Kruga - Kod kuće i u gostima</option>
                            </select>
                            <p class="text-gray-400 text-xs mt-1">Da li se igra jedan krug ili dva kruga u grupnoj fazi</p>
                            @error('group_rounds')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Manual Knockout Selection -->
                    <div class="mt-6">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" id="manual_knockout_selection" name="manual_knockout_selection" value="1"
                                   {{ old('manual_knockout_selection', $competition->manual_knockout_selection ?? true) ? 'checked' : '' }}
                                   class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-white font-medium">Ručno Odabiranje za Eliminacionu Faz</span>
                                <p class="text-gray-400 text-xs">Administrator ručno odabire igrače koji prolaze u eliminacionu fazu</p>
                            </div>
                        </label>
                    </div>
                </div>
                @endif

                <!-- Group Stage Scoring -->
                @if($competition->type === 'tournament')
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Bodovanje Grupne Faze</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Points for Win -->
                        <div>
                            <label for="points_for_win" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Pobjedu <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_win" name="points_for_win" 
                                   value="{{ old('points_for_win', $competition->points_for_win ?? 2) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za pobjedu u meču</p>
                        </div>

                        <!-- Points for Draw -->
                        <div>
                            <label for="points_for_draw" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Neriješeno <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_draw" name="points_for_draw" 
                                   value="{{ old('points_for_draw', $competition->points_for_draw ?? 1) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za neriješeni meč</p>
                        </div>

                        <!-- Points for Loss -->
                        <div>
                            <label for="points_for_loss" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Poraz <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_loss" name="points_for_loss" 
                                   value="{{ old('points_for_loss', $competition->points_for_loss ?? 0) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za izgubljeni meč</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- League Scoring -->
                @if($competition->type === 'league')
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Postavke Lige</h3>
                    
                    <div class="mb-6">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" id="is_double_round" name="is_double_round" value="1"
                                   {{ old('is_double_round', $competition->is_double_round) ? 'checked' : '' }}
                                   class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-white font-medium">Dvokružni sistem (Domaćin i Gost)</span>
                                <p class="text-gray-400 text-xs">Svaka ekipa igra protiv svake ekipe dva puta</p>
                            </div>
                        </label>
                    </div>

                    <div class="mb-6 pt-4 border-t border-gray-700/50 space-y-4">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" id="is_recreational" name="is_recreational" value="1"
                                   {{ old('is_recreational', $competition->is_recreational) ? 'checked' : '' }}
                                   class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-white font-medium">Rekreativna liga</span>
                                <p class="text-gray-400 text-xs">Za društvo koje igra radi zabave: ručno dodavanje mečeva i ranije završavanje meča/seta sa trenutnim rezultatom bez čekanja zvanične pobjede.</p>
                            </div>
                        </label>

                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" id="allow_rematches" name="allow_rematches" value="1"
                                   {{ old('allow_rematches', $competition->allow_rematches) ? 'checked' : '' }}
                                   class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-white font-medium">Dozvoli povratne mečeve (revanš)</span>
                                <p class="text-gray-400 text-xs">Isti par igrača može igrati više puta jedan protiv drugog u ovom takmičenju.</p>
                            </div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Points for Win -->
                        <div>
                            <label for="points_for_win" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Pobjedu <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_win" name="points_for_win" 
                                   value="{{ old('points_for_win', $competition->points_for_win ?? 2) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za pobjedu u meču</p>
                        </div>

                        <!-- Points for Draw -->
                        <div>
                            <label for="points_for_draw" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Neriješeno <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_draw" name="points_for_draw" 
                                   value="{{ old('points_for_draw', $competition->points_for_draw ?? 1) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za neriješeni meč</p>
                        </div>

                        <!-- Points for Loss -->
                        <div>
                            <label for="points_for_loss" class="block text-sm font-medium text-white mb-2">
                                Bodovi za Poraz <span class="text-red-400">*</span>
                            </label>
                            <input type="number" id="points_for_loss" name="points_for_loss"
                                   value="{{ old('points_for_loss', $competition->points_for_loss ?? 0) }}"
                                   min="0" max="10" required
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-gray-400 text-xs mt-1">Bodovi dodijeljeni za izgubljeni meč</p>
                        </div>
                    </div>

                    <!-- Forfeit / odustajanje -->
                    <div class="mt-6 pt-6 border-t border-gray-700/50">
                        <h4 class="text-lg font-semibold text-white mb-1">Pravila za odustajanje (forfeit)</h4>
                        <p class="text-gray-400 text-xs mb-4">Šta se dešava kad se meč odustane (walkover). Pobjednik uvijek dobija bodove (prazno = isto kao za normalnu pobjedu). Onaj ko je odustao dobija bodove SAMO ako mu se meč računa kao odigran ispod, ili ako ovdje eksplicitno upišeš broj bodova - inače dobija 0.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="forfeit_winner_points" class="block text-sm font-medium text-white mb-2">Bodovi za pobjednika</label>
                                <input type="number" id="forfeit_winner_points" name="forfeit_winner_points"
                                       value="{{ old('forfeit_winner_points', $competition->forfeit_winner_points) }}"
                                       min="0" max="10" placeholder="Isto kao za pobjedu ({{ $competition->points_for_win ?? 2 }})"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="forfeit_loser_points" class="block text-sm font-medium text-white mb-2">Bodovi za onog ko je odustao</label>
                                <input type="number" id="forfeit_loser_points" name="forfeit_loser_points"
                                       value="{{ old('forfeit_loser_points', $competition->forfeit_loser_points) }}"
                                       min="0" max="10" placeholder="0 (osim ako je dolje označeno da se računa kao odigran)"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="mt-4 space-y-3">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="forfeit_winner_counts_as_played" value="1"
                                       {{ old('forfeit_winner_counts_as_played', $competition->forfeit_winner_counts_as_played ?? true) ? 'checked' : '' }}
                                       class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-white text-sm">Pobjedniku se meč računa kao odigran</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="forfeit_loser_counts_as_played" value="1"
                                       {{ old('forfeit_loser_counts_as_played', $competition->forfeit_loser_counts_as_played ?? false) ? 'checked' : '' }}
                                       class="w-5 h-5 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-white text-sm">Onom ko je odustao se meč računa kao odigran (i dobija bodove za poraz iznad, osim ako je gore upisan drugi broj)</span>
                            </label>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Save Button -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        <button type="submit"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-3 sm:px-6 sm:py-3 rounded-lg transition-colors font-semibold text-center">
                            Sačuvaj Postavke
                        </button>
                        <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}"
                           class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 sm:px-6 sm:py-3 rounded-lg transition-colors text-center">
                            Otkaži
                        </a>
                    </div>
                </div>

                </div>
            </form>

        </div>
    </div>

    <script>
        // Presets
        const presets = {
            standard: {
                sets_to_win: 2,
                points_per_set: 11,
                deuce_at: 10,
                must_win_by_two: true,
                points_for_win: 2,
                points_for_draw: 1,
                points_for_loss: 0,
                has_tiebreak: false,
                tiebreak_points: 7
            },
            extended: {
                sets_to_win: 2,
                points_per_set: 15,
                deuce_at: 14,
                must_win_by_two: true,
                points_for_win: 2,
                points_for_draw: 1,
                points_for_loss: 0,
                has_tiebreak: false,
                tiebreak_points: 7
            },
            classic: {
                sets_to_win: 2,
                points_per_set: 21,
                deuce_at: 20,
                must_win_by_two: true,
                points_for_win: 3,
                points_for_draw: 0,
                points_for_loss: 0,
                has_tiebreak: false,
                tiebreak_points: 7
            }
        };

        function applyPreset(presetName) {
            const preset = presets[presetName];
            
            document.getElementById('sets_to_win').value = preset.sets_to_win;
            document.getElementById('points_per_set').value = preset.points_per_set;
            document.getElementById('deuce_at').value = preset.deuce_at;
            document.getElementById('must_win_by_two').checked = preset.must_win_by_two;
            document.getElementById('has_tiebreak').checked = preset.has_tiebreak;
            document.getElementById('tiebreak_points').value = preset.tiebreak_points;
            
            if (document.getElementById('points_for_win')) {
                document.getElementById('points_for_win').value = preset.points_for_win;
                document.getElementById('points_for_draw').value = preset.points_for_draw;
                document.getElementById('points_for_loss').value = preset.points_for_loss;
            }

            toggleTiebreakPoints();
            
            // Show notification
            showNotification(`Applied ${presetName.charAt(0).toUpperCase() + presetName.slice(1)} preset!`, 'success');
        }

        function toggleTiebreakPoints() {
            const hasTiebreak = document.getElementById('has_tiebreak').checked;
            const tiebreakDiv = document.getElementById('tiebreakPointsDiv');
            
            if (hasTiebreak) {
                tiebreakDiv.style.display = 'block';
            } else {
                tiebreakDiv.style.display = 'none';
            }
        }

        function showNotification(message, type = 'info') {
            let container = document.getElementById('toast-container-bottom-center');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container-bottom-center';
                container.className = 'fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 flex flex-col items-center gap-3 pointer-events-none';
                document.body.appendChild(container);
            }

            const notification = document.createElement('div');
            notification.className = `pointer-events-auto max-w-xl w-full px-6 py-3 rounded-lg shadow-lg transition-opacity duration-300 ease-out ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } text-white opacity-0`;
            notification.textContent = message;

            container.appendChild(notification);
            requestAnimationFrame(() => { notification.classList.remove('opacity-0'); notification.classList.add('opacity-100'); });

            setTimeout(() => {
                notification.classList.remove('opacity-100');
                notification.classList.add('opacity-0');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleTiebreakPoints();
            
            // Auto-update deuce_at when points_per_set changes
            document.getElementById('points_per_set').addEventListener('change', function() {
                const points = parseInt(this.value);
                document.getElementById('deuce_at').value = points - 1;
            });
        });

        // Validate settings form submission
        function validateSettingsForm(event) {
            return true;
        }
    </script>
</x-app-layout>
