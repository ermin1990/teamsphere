@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Upravljanje Ligama i Turnirima
            </h2>
            <p class="text-gray-400 mt-2">Grupisano po organizacijama - odaberi više takmičenja da ih zatvoriš odjednom</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-400">Ukupno</p>
            <p class="text-2xl font-bold text-white">{{ $total }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Status filter -->
    <div class="flex flex-wrap gap-2">
        @php
            $statuses = ['' => 'Sve', 'active' => 'Aktivne', 'in_progress' => 'U toku', 'draft' => 'Draft', 'completed' => 'Zatvorene'];
        @endphp
        @foreach($statuses as $value => $label)
            <a href="{{ route('admin.leagues.index', array_filter(['status' => $value])) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium border transition-colors {{ request('status', '') === $value ? 'bg-blue-500/20 text-blue-400 border-blue-500/30' : 'text-gray-300 border-gray-700/50 hover:bg-gray-700/30' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <form method="POST" action="{{ route('admin.leagues.bulk-close') }}" id="bulk-close-form">
        @csrf

        <!-- Bulk action bar -->
        <div class="sticky top-0 z-10 mb-4 flex items-center justify-between gap-4 bg-gray-800/80 backdrop-blur-xl border border-gray-700/50 rounded-xl px-5 py-3">
            <label class="flex items-center gap-2 text-sm text-gray-300 cursor-pointer">
                <input type="checkbox" id="select-all" class="rounded bg-gray-700 border-gray-600 text-blue-500 focus:ring-blue-500">
                Označi sve
            </label>
            <span id="selected-count" class="text-sm text-gray-400">0 odabrano</span>
            <button type="submit" id="bulk-close-btn" disabled
                    formaction="{{ route('admin.leagues.bulk-close') }}"
                    onclick="return confirm(document.querySelectorAll('.league-checkbox:checked').length + ' takmičenje/a će biti zatvoreno. Nastaviti?');"
                    class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg border border-red-500/30 text-sm font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed hover:enabled:bg-red-500/30">
                Zatvori odabrane
            </button>
        </div>

        <!-- Leagues grouped by organization -->
        <div class="space-y-6">
            @forelse($leagues as $organizationName => $competitions)
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
                <div class="p-4 border-b border-gray-700/50 flex items-center justify-between bg-gray-900/30">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="text-blue-400">🏢</span> {{ $organizationName }}
                    </h3>
                    <span class="text-xs text-gray-500">{{ $competitions->count() }} {{ $competitions->count() === 1 ? 'takmičenje' : 'takmičenja' }}</span>
                </div>
                <div class="divide-y divide-gray-700/50">
                    @foreach($competitions as $league)
                    <div class="p-4 sm:p-6 hover:bg-gray-700/20 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <div class="flex items-center space-x-4 min-w-0">
                                <input type="checkbox" name="ids[]" value="{{ $league->id }}"
                                       class="league-checkbox rounded bg-gray-700 border-gray-600 text-blue-500 focus:ring-blue-500 shrink-0"
                                       {{ $league->status === 'completed' ? 'disabled' : '' }}>
                                <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shrink-0">
                                    <span class="text-xl">{{ $league->sport->icon ?? '🏆' }}</span>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-white font-semibold truncate">{{ $league->name }}</h4>
                                    <p class="text-gray-400 text-sm truncate">{{ $league->sport->name ?? '' }} • {{ $league->type === 'tournament' ? 'Turnir' : 'Liga' }}</p>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-6">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase self-start sm:self-auto
                                    @if($league->status === 'completed') bg-gray-700/60 text-gray-400
                                    @elseif($league->status === 'in_progress') bg-yellow-500/20 text-yellow-400
                                    @elseif($league->status === 'active') bg-emerald-500/20 text-emerald-400
                                    @else bg-purple-500/20 text-purple-400 @endif">
                                    @if($league->status === 'completed') Zatvoreno
                                    @elseif($league->status === 'in_progress') U toku
                                    @elseif($league->status === 'active') Aktivno
                                    @else Draft @endif
                                </span>

                                <p class="text-sm text-gray-400">{{ $league->created_at->format('d.m.Y') }}</p>

                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.leagues.show', $league) }}" class="px-3 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-colors border border-blue-500/30 text-sm whitespace-nowrap">
                                        Pogledaj
                                    </a>
                                    @if($league->status !== 'completed')
                                        <button type="submit" formaction="{{ route('admin.leagues.close', $league) }}"
                                                onclick="return confirm('Zatvoriti \'{{ $league->name }}\'?');"
                                                class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-colors border border-red-500/30 text-sm whitespace-nowrap">
                                            Zatvori
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 p-12 text-center text-gray-400">
                Nema takmičenja za odabrani filter.
            </div>
            @endforelse
        </div>
    </form>
</div>

<script>
    (function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = () => Array.from(document.querySelectorAll('.league-checkbox:not(:disabled)'));
        const countLabel = document.getElementById('selected-count');
        const bulkBtn = document.getElementById('bulk-close-btn');

        function updateCount() {
            const checked = document.querySelectorAll('.league-checkbox:checked').length;
            countLabel.textContent = checked + ' odabrano';
            bulkBtn.disabled = checked === 0;
        }

        selectAll?.addEventListener('change', function () {
            checkboxes().forEach(cb => { cb.checked = selectAll.checked; });
            updateCount();
        });

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('league-checkbox')) updateCount();
        });

        updateCount();
    })();
</script>
@endsection
