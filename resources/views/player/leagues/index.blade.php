<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl sm:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent" style="font-family: 'Unbounded', ui-sans-serif, sans-serif; letter-spacing: -0.01em;">
                Takmičenja
            </h2>
            <p class="text-sm mt-1" style="color: var(--text-tertiary);">Pretraži otvorene lige i prijavi se za učešće</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            @if(session('success'))
                <div class="rounded-xl p-4 text-sm" style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #4ade80;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-xl p-4 text-sm" style="background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3); color: #f87171;">{{ session('error') }}</div>
            @endif

            <form method="GET" action="{{ route('player.leagues.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pretraži po nazivu..."
                       class="mt-input md:col-span-2 w-full px-4 py-3 rounded-xl text-sm focus:outline-none transition-all"
                       style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                <select name="sport_id" onchange="this.form.submit()"
                        class="mt-input w-full px-4 py-3 rounded-xl text-sm focus:outline-none transition-all"
                        style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                    <option value="">Svi sportovi</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}" {{ (string) request('sport_id') === (string) $sport->id ? 'selected' : '' }}>{{ $sport->name }}</option>
                    @endforeach
                </select>
                <select name="city_id" onchange="this.form.submit()"
                        class="mt-input w-full px-4 py-3 rounded-xl text-sm focus:outline-none transition-all"
                        style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                    <option value="">Svi gradovi</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ (string) request('city_id') === (string) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="md:hidden px-4 py-2.5 text-sm font-semibold rounded-xl transition-all active:scale-95" style="background: var(--accent-blue); color: #14141F;">
                    Pretraži
                </button>
            </form>

            @forelse($competitions as $competition)
                <div class="rounded-2xl p-5 space-y-3 backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <p class="font-bold text-lg" style="color: var(--text-primary);">{{ $competition->name }}</p>
                            <p class="text-xs mt-1" style="color: var(--text-tertiary);">
                                {{ $competition->organization->name }}
                                @if($competition->sport) · {{ $competition->sport->name }} @endif
                                @if($competition->city) · {{ $competition->city->name }} @endif
                                @if($competition->season) · {{ $competition->season->name }} @endif
                            </p>
                        </div>
                        <div class="shrink-0">
                            @if($memberCompetitionIds->contains($competition->id))
                                <span class="px-3 py-1.5 text-xs rounded-full font-semibold" style="background: rgba(34,197,94,0.15); color: #4ade80;">Već si član</span>
                            @elseif($pendingCompetitionIds->contains($competition->id))
                                <span class="px-3 py-1.5 text-xs rounded-full font-semibold" style="background: rgba(234,179,8,0.15); color: #eab308;">Zahtjev poslan</span>
                            @elseif($competition->is_team_based)
                                <span class="px-3 py-1.5 text-xs rounded-full font-semibold" style="background: var(--bg-tertiary); color: var(--text-muted);">Timsko takmičenje</span>
                            @else
                                <form method="POST" action="{{ route('player.leagues.apply', $competition) }}">
                                    @csrf
                                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 text-sm font-semibold rounded-full transition-all active:scale-95 whitespace-nowrap" style="background: var(--accent-blue); color: #14141F;">
                                        Prijavi se
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if($competition->description)
                        <p class="text-sm" style="color: var(--text-secondary);">{{ $competition->description }}</p>
                    @endif

                    @if($competition->location || $competition->organizer_contact || $competition->entry_fee)
                        <div class="flex flex-wrap gap-x-6 gap-y-1 text-xs pt-2" style="color: var(--text-tertiary); border-top: 1px solid var(--border-secondary);">
                            @if($competition->location)<span>📍 {{ $competition->location }}</span>@endif
                            @if($competition->entry_fee)<span>💳 {{ $competition->entry_fee }}</span>@endif
                            @if($competition->organizer_contact)<span>☎️ {{ $competition->organizer_contact }}</span>@endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl p-8 sm:p-10 text-center backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                    <p style="color: var(--text-secondary);">Trenutno nema otvorenih takmičenja koja odgovaraju pretrazi.</p>
                </div>
            @endforelse

            <div class="pt-2">{{ $competitions->links() }}</div>
        </div>
    </div>

    @once
    <style>
        .mt-input:focus { border-color: var(--accent-blue) !important; box-shadow: 0 0 0 2px rgba(180,192,255,0.2); }
        .mt-input::placeholder { color: var(--text-muted); }
    </style>
    @endonce
</x-app-layout>
