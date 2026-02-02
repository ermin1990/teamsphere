<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 sm:gap-0">
                    <div class="flex-1 flex flex-col sm:flex-row sm:items-center">
                        <div class="flex items-center justify-center sm:justify-start">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-600 rounded-full flex-shrink-0">
                                <span class="text-white font-bold">1</span>
                            </div>
                            <div class="ml-4 text-center sm:text-left">
                                <h3 class="text-white font-semibold">Dodaj Igrače</h3>
                                <p class="text-gray-400 text-sm">Odaberite učesnike</p>
                            </div>
                        </div>
                    </div>

                    <!-- Connecting Line - Hidden on mobile, horizontal on desktop -->
                    <div class="hidden sm:flex flex-1 h-1 bg-gray-700 mx-4"></div>
                    <!-- Vertical connecting line on mobile -->
                    <div class="flex sm:hidden justify-center">
                        <div class="w-1 h-6 bg-gray-700"></div>
                    </div>

                    <div class="flex-1 flex flex-col sm:flex-row sm:items-center">
                        <div class="flex items-center justify-center sm:justify-start">
                            <div class="flex items-center justify-center w-10 h-10 bg-gray-700 rounded-full flex-shrink-0">
                                <span class="text-gray-400 font-bold">2</span>
                            </div>
                            <div class="ml-4 text-center sm:text-left">
                                <h3 class="text-gray-400 font-semibold">Postavi Grupe</h3>
                                <p class="text-gray-500 text-sm">Organizujte učesnike</p>
                            </div>
                        </div>
                    </div>

                    <!-- Connecting Line - Hidden on mobile, horizontal on desktop -->
                    <div class="hidden sm:flex flex-1 h-1 bg-gray-700 mx-4"></div>
                    <!-- Vertical connecting line on mobile -->
                    <div class="flex sm:hidden justify-center">
                        <div class="w-1 h-6 bg-gray-700"></div>
                    </div>

                    <div class="flex-1 flex flex-col sm:flex-row sm:items-center">
                        <div class="flex items-center justify-center sm:justify-start">
                            <div class="flex items-center justify-center w-10 h-10 bg-gray-700 rounded-full flex-shrink-0">
                                <span class="text-gray-400 font-bold">3</span>
                            </div>
                            <div class="ml-4 text-center sm:text-left">
                                <h3 class="text-gray-400 font-semibold">Započni Takmičenje</h3>
                                <p class="text-gray-500 text-sm">Započni mečeve</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Content - Add Players (2 columns) -->
                <div class="lg:col-span-2">
                    @livewire('add-player-to-competition', ['organization' => $organization, 'competition' => $competition], key('add-player-' . $competition->id))

                    <!-- Current Participants -->
                    <div class="mt-6 bg-gray-800/50 backdrop-blur-xl rounded-xl p-6 border border-gray-700/50 shadow-xl"
                         id="participants-list">
                        <h3 class="text-xl font-bold text-white mb-4">
                            Trenutni Učesnici
                            <span class="text-gray-400 text-sm ml-2">({{ $competition->players->count() }}/{{ $competition->max_participants ?? '∞' }})</span>
                        </h3>

                        @if($competition->players->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($competition->players as $player)
                                    <div class="flex items-center justify-between bg-gray-700/30 rounded-lg p-3 hover:bg-gray-700/50 transition-colors">
                                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-white font-bold text-sm">{{ substr($player->name, 0, 2) }}</span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-white font-medium truncate">{{ $player->name }}</p>
                                                @if($player->position)
                                                    <p class="text-gray-400 text-xs truncate">({{ $player->position }})</p>
                                                @elseif($player->email)
                                                    <p class="text-gray-400 text-xs truncate">{{ $player->email }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        @livewire('remove-player-from-competition', ['organization' => $organization, 'competition' => $competition, 'player' => $player], key('remove-player-' . $player->id))
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-400">Još nema dodanih igrača. Koristite obrazac iznad da dodate igrače.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar - Info & Actions (1 column) -->
                <div class="space-y-6">

                    <!-- Competition Info -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-5 border border-gray-700/50 shadow-xl">
                        <h3 class="text-base font-bold text-white mb-3">Informacije o Takmičenju</h3>
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">Tip</span>
                                <span class="text-white text-xs font-medium">{{ ucfirst($competition->type) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">Format</span>
                                <span class="text-white text-xs font-medium">{{ $competition->is_team_based ? 'Tim' : 'Individualno' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">Sport</span>
                                <span class="text-white text-xs font-medium">{{ $competition->sport->name }}</span>
                            </div>
                            @if($competition->type === 'tournament')
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">Grupe</span>
                                <span class="text-white text-xs font-medium">{{ $competition->group_count }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-gray-700/30">
                                <span class="text-gray-400 text-xs">Po Grupi</span>
                                <span class="text-white text-xs font-medium">{{ $competition->players_per_group }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Next Step Card -->
                    @php
                        $hasEnoughPlayers = $competition->players->count() >= ($competition->type === 'tournament' ? 4 : 2);
                    @endphp

                    @if($hasEnoughPlayers)
                        <div class="bg-gradient-to-r from-green-600/20 to-emerald-600/20 backdrop-blur-xl rounded-xl p-5 border border-green-500/30 shadow-xl">
                            <div class="flex items-start mb-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-base font-bold text-white">Spremni za Sledeći Korak!</h3>
                                    <p class="text-gray-300 text-sm mt-1">Imate dovoljno igrača da nastavite.</p>
                                </div>
                            </div>

                            @if($competition->type === 'tournament')
                                <a href="{{ route('organizations.competitions.setup-groups', [$organization, $competition]) }}"
                                   class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-3 rounded-lg transition-colors font-semibold">
                                    Nastavi na Postavljanje Grupa →
                                </a>
                            @else
                                <form method="POST" action="{{ route('organizations.competitions.start', [$organization, $competition]) }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition-colors font-semibold">
                                        {{ __('Start League') }} →
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="bg-gradient-to-r from-yellow-600/20 to-orange-600/20 backdrop-blur-xl rounded-xl p-5 border border-yellow-500/30 shadow-xl">
                            <div class="flex items-start mb-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-base font-bold text-white">Dodaj više igrača</h3>
                                <p class="text-gray-300 text-sm mt-1">
                                    @if($competition->type === 'tournament')
                                        Potrebno je najmanje 4 igrača za početak.
                                    @else
                                        Potrebno je najmanje 2 igrača za početak.
                                    @endif
                                </p>
                                <div class="mt-3 flex items-center justify-between bg-gray-800/50 rounded-lg px-3 py-2">
                                    <span class="text-gray-400 text-xs">Napredak</span>
                                    <span class="text-white font-bold">{{ $competition->players->count() }}/{{ $competition->type === 'tournament' ? 4 : 2 }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-5 border border-gray-700/50 shadow-xl">
                        <h3 class="text-base font-bold text-white mb-3">{{ __('Quick Actions') }}</h3>
                        <div class="space-y-2">
                            <a href="{{ route('organizations.competitions.bulk-import', [$organization, $competition]) }}"
                               class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center text-sm px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                                📄 {{ __('Bulk Import Players') }}
                            </a>
                            <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}"
                               class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center text-sm px-4 py-2 rounded-lg transition-colors">
                                ← {{ __('Back to Competition') }}
                            </a>
                            <a href="{{ route('organizations.competitions.settings', [$organization, $competition]) }}"
                               class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center text-sm px-4 py-2 rounded-lg transition-colors">
                                ⚙️ {{ __('Settings') }}
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>