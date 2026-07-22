<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Pravila Takmičenja</h1>
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

            <!-- Read-only scoring settings summary -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <h2 class="text-lg font-semibold text-white mb-1">Pravila bodovanja</h2>
                <p class="text-sm text-gray-400 mb-4">Preuzeto iz postavki takmičenja ({{ $competition->sport->name }}). Za izmjenu ovih vrijednosti idi na <a href="{{ route('organizations.competitions.settings', [$organization, $competition]) }}" class="text-blue-400 hover:underline">Postavke</a>.</p>

                @if($competition->sport->isPointsBased())
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-900/40 rounded-lg p-3">
                            <dt class="text-gray-400">Setovi za pobjedu</dt>
                            <dd class="text-white font-semibold">{{ $competition->sets_to_win ?? '-' }}</dd>
                        </div>
                        <div class="bg-gray-900/40 rounded-lg p-3">
                            <dt class="text-gray-400">Poena po setu</dt>
                            <dd class="text-white font-semibold">{{ $competition->points_per_set ?? '-' }}</dd>
                        </div>
                        <div class="bg-gray-900/40 rounded-lg p-3">
                            <dt class="text-gray-400">Deuce na</dt>
                            <dd class="text-white font-semibold">{{ $competition->deuce_at ?? '-' }}</dd>
                        </div>
                        <div class="bg-gray-900/40 rounded-lg p-3">
                            <dt class="text-gray-400">Mora se pobijediti razlikom od 2</dt>
                            <dd class="text-white font-semibold">{{ $competition->must_win_by_two ? 'Da' : 'Ne' }}</dd>
                        </div>
                        <div class="bg-gray-900/40 rounded-lg p-3">
                            <dt class="text-gray-400">Poeni za pobjedu / nerešeno / poraz</dt>
                            <dd class="text-white font-semibold">{{ $competition->points_for_win ?? '-' }} / {{ $competition->points_for_draw ?? '-' }} / {{ $competition->points_for_loss ?? '-' }}</dd>
                        </div>
                        <div class="bg-gray-900/40 rounded-lg p-3">
                            <dt class="text-gray-400">Tiebreak</dt>
                            <dd class="text-white font-semibold">{{ $competition->has_tiebreak ? ('Da, do ' . $competition->tiebreak_points . ' poena') : 'Ne' }}</dd>
                        </div>
                    </dl>
                @elseif($competition->sport->isSetsGamesBased())
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-4">
                        <div class="bg-gray-900/40 rounded-lg p-3">
                            <dt class="text-gray-400">Setovi za pobjedu</dt>
                            <dd class="text-white font-semibold">{{ $competition->sets_to_win ?? '-' }}</dd>
                        </div>
                    </dl>
                    <p class="text-gray-400 text-sm bg-gray-900/40 rounded-xl p-4 border border-gray-700/30">
                        Set se igra do 6 gemova (razlika 2), sa tie-breakom na 6-6 - standardna pravila za {{ $competition->sport->name }}.
                        Gem se igra po klasičnom sistemu (0, 15, 30, 40, deuce/prednost).
                    </p>
                @else
                    <p class="text-gray-400 text-sm italic">Nema automatskih pravila bodovanja za ovaj sport.</p>
                @endif
            </div>

            <!-- Match points -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">Bodovanje</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div class="bg-gray-900/40 rounded-lg p-3">
                        <dt class="text-gray-400">Pobjeda</dt>
                        <dd class="text-white font-semibold">{{ $competition->points_for_win ?? '-' }} bod.</dd>
                    </div>
                    <div class="bg-gray-900/40 rounded-lg p-3">
                        <dt class="text-gray-400">Neriješeno</dt>
                        <dd class="text-white font-semibold">{{ $competition->points_for_draw ?? '-' }} bod.</dd>
                    </div>
                    <div class="bg-gray-900/40 rounded-lg p-3">
                        <dt class="text-gray-400">Poraz</dt>
                        <dd class="text-white font-semibold">{{ $competition->points_for_loss ?? '-' }} bod.</dd>
                    </div>
                </dl>
            </div>

            @if($competition->isLeague())
            <!-- Forfeit rules -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <h2 class="text-lg font-semibold text-white mb-1">Odustajanje (WO)</h2>
                <p class="text-sm text-gray-400 mb-4">Za izmjenu idi na <a href="{{ route('organizations.competitions.settings', [$organization, $competition]) }}" class="text-blue-400 hover:underline">Postavke</a>.</p>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-900/40 rounded-lg p-3">
                        <dt class="text-gray-400">Bodovi za pobjednika</dt>
                        <dd class="text-white font-semibold">{{ $competition->forfeitWinnerPoints() }} bod. &middot; {{ $competition->forfeit_winner_counts_as_played ? 'računa se kao odigran' : 'ne računa se kao odigran' }}</dd>
                    </div>
                    <div class="bg-gray-900/40 rounded-lg p-3">
                        <dt class="text-gray-400">Bodovi za onog ko je odustao</dt>
                        <dd class="text-white font-semibold">{{ $competition->forfeit_loser_counts_as_played ? $competition->forfeitLoserPoints() : ($competition->forfeit_loser_points ?? 0) }} bod. &middot; {{ $competition->forfeit_loser_counts_as_played ? 'računa se kao odigran' : 'ne računa se kao odigran' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- League settings -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">Postavke lige</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div class="bg-gray-900/40 rounded-lg p-3">
                        <dt class="text-gray-400">Dvokružni sistem</dt>
                        <dd class="text-white font-semibold">{{ $competition->is_double_round ? 'Da' : 'Ne' }}</dd>
                    </div>
                    <div class="bg-gray-900/40 rounded-lg p-3">
                        <dt class="text-gray-400">Rekreativna liga</dt>
                        <dd class="text-white font-semibold">{{ $competition->is_recreational ? 'Da' : 'Ne' }}</dd>
                    </div>
                    <div class="bg-gray-900/40 rounded-lg p-3">
                        <dt class="text-gray-400">Dozvoljeni revanš mečevi</dt>
                        <dd class="text-white font-semibold">{{ $competition->allow_rematches ? 'Da' : 'Ne' }}</dd>
                    </div>
                </dl>
            </div>
            @else
            <!-- Tournament settings -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">Postavke turnira</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-900/40 rounded-lg p-3">
                        <dt class="text-gray-400">Igrača napreduje po grupi</dt>
                        <dd class="text-white font-semibold">{{ $competition->players_advancing_per_group ?? '-' }}</dd>
                    </div>
                    <div class="bg-gray-900/40 rounded-lg p-3">
                        <dt class="text-gray-400">Krugova u grupama</dt>
                        <dd class="text-white font-semibold">{{ $competition->group_rounds ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
            @endif

            <!-- Free-text rules -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h2 class="text-lg font-semibold text-white mb-1">Dodatna pravila</h2>
                <p class="text-sm text-gray-400 mb-4">
                    Ako ostaviš prazno, prikazivaće se
                    <a href="{{ route('organizations.rules', $organization) }}" class="text-blue-400 hover:underline">opća pravila organizacije</a>@if($organization->rules_text)
                        <span class="block mt-2 p-3 bg-gray-900/40 rounded-lg whitespace-pre-line">{{ $organization->rules_text }}</span>
                    @else
                        (trenutno nisu postavljena).
                    @endif
                </p>
                <form method="POST" action="{{ route('organizations.competitions.update-rules', [$organization, $competition]) }}">
                    @csrf
                    <textarea name="rules_text" rows="14"
                              class="w-full bg-gray-700/50 border border-gray-600/50 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                              placeholder="Ostavi prazno da koristiš opća pravila organizacije, ili upiši posebna pravila samo za ovu ligu...">{{ old('rules_text', $competition->rules_text) }}</textarea>
                    @error('rules_text')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 font-semibold">
                            Sačuvaj Pravila
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
