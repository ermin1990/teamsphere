<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl sm:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent" style="font-family: 'Unbounded', ui-sans-serif, sans-serif; letter-spacing: -0.01em;">
                Zabilježi meč
            </h2>
            <p class="text-sm mt-1" style="color: var(--text-tertiary);">{{ $competition->name }}</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl p-5 sm:p-8 backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                @if($errors->any())
                    <div class="rounded-xl p-4 text-sm mb-6" style="background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3); color: #f87171;">
                        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('player.matches.store', $competition) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="opponent_id" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Protivnik</label>
                        <select id="opponent_id" name="opponent_id" required
                                class="mt-input w-full px-4 py-3 rounded-xl text-sm focus:outline-none transition-all"
                                style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                            <option value="">Odaberi protivnika...</option>
                            @foreach($opponents as $opponent)
                                <option value="{{ $opponent->id }}">{{ $opponent->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="played_at" class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Datum meča</label>
                        <input type="datetime-local" id="played_at" name="played_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                               class="mt-input w-full px-4 py-3 rounded-xl text-sm focus:outline-none transition-all"
                               style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Rezultat po setovima ({{ $unitLabel }})</label>
                        <p class="text-xs mb-3" style="color: var(--text-tertiary);">Popuni samo onoliko setova koliko je odigrano - prazni setovi na kraju se ignorišu.</p>
                        <div class="space-y-2">
                            @for($i = 0; $i < $maxSets; $i++)
                                <div class="flex items-center gap-3">
                                    <span class="text-sm w-14 font-medium" style="color: var(--text-tertiary);">Set {{ $i + 1 }}</span>
                                    <input type="number" name="sets[{{ $i }}][mine]" min="0" placeholder="Ja"
                                           class="mt-input w-20 text-center px-3 py-2.5 rounded-xl focus:outline-none transition-all"
                                           style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                                    <span style="color: var(--text-muted);">:</span>
                                    <input type="number" name="sets[{{ $i }}][theirs]" min="0" placeholder="Protivnik"
                                           class="mt-input w-24 text-center px-3 py-2.5 rounded-xl focus:outline-none transition-all"
                                           style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('player.dashboard') }}" class="flex-1 text-center px-4 py-3 rounded-full font-semibold transition-all active:scale-95" style="background: var(--bg-tertiary); color: var(--text-secondary);">
                            Odustani
                        </a>
                        <button type="submit" class="flex-1 px-4 py-3 font-semibold rounded-full transition-all active:scale-95" style="background: var(--accent-blue); color: #14141F;">
                            Sačuvaj meč
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @once
    <style>
        .mt-input:focus { border-color: var(--accent-blue) !important; box-shadow: 0 0 0 2px rgba(180,192,255,0.2); }
        .mt-input::placeholder { color: var(--text-muted); }
    </style>
    @endonce
</x-app-layout>
