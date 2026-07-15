<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl sm:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent" style="font-family: 'Unbounded', ui-sans-serif, sans-serif; letter-spacing: -0.01em;">
                Moji mečevi
            </h2>
            <p class="text-sm mt-1" style="color: var(--text-tertiary);">Svi tvoji mečevi u svim takmičenjima</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('player.partials.nav')
        </div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3 sm:space-y-4">
            @if(session('success'))
                <div class="rounded-xl p-4 text-sm" style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #4ade80;">{{ session('success') }}</div>
            @endif

            @php
                $singleCompetition = request()->filled('competition_id')
                    ? $competitions->firstWhere('id', (int) request('competition_id'))
                    : ($competitions->count() === 1 ? $competitions->first() : null);
            @endphp

            <div class="flex items-center justify-between gap-3">
                @if($singleCompetition)
                    <a href="{{ route('player.leagues.show', $singleCompetition) }}" class="text-xs font-semibold whitespace-nowrap" style="color: var(--accent-blue);">Pogledaj ligu →</a>
                @else
                    <span></span>
                @endif
                @include('player.partials.new-match-button')
            </div>

            @if($seasons->count() > 1 || $competitions->count() > 1 || $rounds->count() > 1)
                <form method="GET" action="{{ route('player.dashboard.matches') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @if($seasons->count() > 1)
                        <select name="season_id" onchange="this.form.submit()"
                                class="mt-input w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none transition-all"
                                style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                            <option value="">Sve sezone</option>
                            @foreach($seasons as $season)
                                <option value="{{ $season->id }}" {{ (string) request('season_id') === (string) $season->id ? 'selected' : '' }}>{{ $season->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    @if($competitions->count() > 1)
                        <select name="competition_id" onchange="this.form.submit()"
                                class="mt-input w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none transition-all"
                                style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                            <option value="">Sve lige</option>
                            @foreach($competitions as $competition)
                                <option value="{{ $competition->id }}" {{ (string) request('competition_id') === (string) $competition->id ? 'selected' : '' }}>{{ $competition->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    @if($rounds->count() > 1)
                        <select name="round" onchange="this.form.submit()"
                                class="mt-input w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none transition-all"
                                style="background: var(--bg-hover); border: 1px solid var(--border-primary); color: var(--text-primary);">
                            <option value="">Sva kola</option>
                            @foreach($rounds as $round)
                                <option value="{{ $round }}" {{ (string) request('round') === (string) $round ? 'selected' : '' }}>Kolo {{ $round }}</option>
                            @endforeach
                        </select>
                    @endif
                </form>
            @endif

            @forelse($matches as $match)
                @php
                    $competition = $match->competition;
                    $canEnterResult = $competition
                        && $competition->isLeague()
                        && !$competition->is_team_based
                        && $competition->status === 'active';
                @endphp
                <div class="space-y-1.5">
                    @if($competition)
                        @include('public.leagues.partials.match-card', ['match' => $match, 'competition' => $competition])
                    @endif
                    <div class="flex items-center justify-end gap-4 px-1">
                        @if($canEnterResult && $match->status !== 'completed')
                            <a href="{{ route('player.matches.live', $match) }}" class="text-xs font-semibold whitespace-nowrap" style="color: #4ade80;">Uživo</a>
                        @endif
                        @if($canEnterResult)
                            <a href="{{ route('player.matches.result.edit', $match) }}" class="text-xs font-semibold whitespace-nowrap" style="color: #c4b5fd;">{{ $match->status === 'completed' ? 'Uredi rezultat' : 'Upiši rezultat' }}</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-2xl p-8 sm:p-10 text-center backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                    <p style="color: var(--text-secondary);">Još nemaš odigranih ili zakazanih mečeva.</p>
                </div>
            @endforelse

            <div class="pt-2">{{ $matches->links() }}</div>
        </div>
    </div>

    @once
    <style>
        .mt-comp-card:hover { border-color: var(--border-accent) !important; }
    </style>
    @endonce
</x-app-layout>
