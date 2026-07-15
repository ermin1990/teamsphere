<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl sm:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent" style="font-family: 'Unbounded', ui-sans-serif, sans-serif; letter-spacing: -0.01em;">
                Upiši rezultat
            </h2>
            <p class="text-sm mt-1" style="color: var(--text-tertiary);">{{ $competition->name }} · protiv {{ $opponent->name ?? 'TBD' }}</p>
            @if($match->status !== 'completed')
                <p class="text-sm mt-2">
                    <a href="{{ route('player.matches.live', $match) }}" class="font-semibold" style="color: #4ade80;">🏓 Ili unesi rezultat uživo, poen po poen →</a>
                </p>
            @endif
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl p-6 sm:p-8 backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                @if($errors->any())
                    <div class="rounded-xl p-4 text-sm mb-6" style="background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3); color: #f87171;">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('player.matches.result.update', $match) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Protivnik</label>
                        <div class="w-full px-4 py-3 rounded-xl text-sm" style="background: var(--bg-hover); border: 1px solid var(--border-secondary); color: var(--text-secondary);">
                            {{ $opponent->name ?? 'TBD' }}
                        </div>
                    </div>

                    <div>
                        <label for="played_at" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Datum i vrijeme</label>
                        <input type="datetime-local" id="played_at" name="played_at"
                               value="{{ old('played_at', optional($match->played_at ?? $match->scheduled_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}"
                               required
                               class="mt-input w-full px-4 py-3 rounded-xl text-sm focus:outline-none transition-all"
                               style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                    </div>

                    <div>
                        <label for="venue_id" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Teren</label>
                        @if($venues->isNotEmpty())
                            <select id="venue_id" name="venue_id"
                                    class="mt-input w-full px-4 py-3 rounded-xl text-sm focus:outline-none transition-all"
                                    style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                                <option value="">— nije odabran —</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ (string) old('venue_id', $match->venue_id) === (string) $venue->id ? 'selected' : '' }}>
                                        {{ $venue->name }}{{ $venue->address ? ' - '.$venue->address : '' }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="w-full px-4 py-3 rounded-xl text-sm" style="background: var(--bg-hover); border: 1px solid var(--border-secondary); color: var(--text-muted);">
                                Još nema unesenih terena{{ $competition->city ? ' za grad '.$competition->city->name : '' }} - zamoli organizatora da doda teren u admin panelu.
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Rezultat po setovima ({{ $unitLabel }})</label>
                        <p class="text-xs mb-3" style="color: var(--text-tertiary);">Popuni samo onoliko setova koliko je odigrano - prazni setovi na kraju se ignorišu.</p>
                        <div class="space-y-2">
                            @for($i = 0; $i < $maxSets; $i++)
                                <div class="flex items-center gap-3">
                                    <span class="text-sm w-14" style="color: var(--text-tertiary);">Set {{ $i + 1 }}</span>
                                    <input type="number" name="sets[{{ $i }}][mine]" min="0" placeholder="Ja"
                                           value="{{ old("sets.$i.mine", $existingSets[$i]['mine'] ?? '') }}"
                                           class="mt-input w-20 text-center px-3 py-2 rounded-xl text-sm focus:outline-none transition-all"
                                           style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                                    <span style="color: var(--text-muted);">:</span>
                                    <input type="number" name="sets[{{ $i }}][theirs]" min="0" placeholder="Protivnik"
                                           value="{{ old("sets.$i.theirs", $existingSets[$i]['theirs'] ?? '') }}"
                                           class="mt-input w-20 text-center px-3 py-2 rounded-xl text-sm focus:outline-none transition-all"
                                           style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('player.dashboard.matches') }}" class="flex-1 text-center px-4 py-3 rounded-xl text-sm font-semibold transition-all" style="background: var(--bg-tertiary); color: var(--text-primary);">
                            Odustani
                        </a>
                        <button type="submit" class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold transition-all active:scale-95" style="background: var(--accent-blue); color: #14141F;">
                            Sačuvaj rezultat
                        </button>
                    </div>
                </form>

                @if($match->status !== 'scheduled')
                    <div class="mt-4 pt-4" style="border-top: 1px solid var(--border-secondary);">
                        <form method="POST" action="{{ route('player.matches.result.reset', $match) }}"
                              onsubmit="return confirm('Resetovati ovaj meč? Uneseni rezultat, setovi, datum i teren će biti obrisani, a meč vraćen na zakazano - koristi ovo ako si unio rezultat na pogrešan meč.');">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-xs font-semibold transition-all" style="background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3); color: #f87171;">
                                Resetuj meč (obriši uneseni rezultat)
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
