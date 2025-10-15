<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $bugReport->type === 'bug' ? '🐛 Bug Report' : '💡 Feature Request' }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $bugReport->subject }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.bug-reports.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                    ← Nazad na Izvještaje
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="space-y-6">
            <!-- Report Details -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Tip</label>
                        <div class="mt-1">
                            @if($bugReport->type === 'bug')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500/20 text-red-400">
                                    🐛 Izvještaj o Bagu
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-500/20 text-blue-400">
                                    💡 Zahtjev za Funkciju
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400">Status</label>
                        <div class="mt-1">
                            @switch($bugReport->status)
                                @case('pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-500/20 text-yellow-400">
                                        Na Čekanju
                                    </span>
                                    @break
                                @case('in_review')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-500/20 text-purple-400">
                                        U Pregledu
                                    </span>
                                    @break
                                @case('resolved')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-400">
                                        Riješeno
                                    </span>
                                    @break
                                @case('closed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-500/20 text-gray-400">
                                        Zatvoreno
                                    </span>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400">Poslao</label>
                        <div class="mt-1 text-white">
                            {{ $bugReport->user ? $bugReport->user->name : ($bugReport->name ?: 'Anonymous') }}
                            @if($bugReport->email)
                                <div class="text-sm text-gray-400">{{ $bugReport->email }}</div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400">Datum Slanja</label>
                        <div class="mt-1 text-white">
                            {{ $bugReport->created_at->format('M d, Y \a\t H:i') }}
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Predmet</label>
                    <div class="text-white font-medium">{{ $bugReport->subject }}</div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Opis</label>
                    <div class="bg-white/5 rounded-lg p-4 text-white whitespace-pre-wrap">{{ $bugReport->description }}</div>
                </div>
            </div>

            <!-- Update Status Form -->
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <h3 class="text-lg font-semibold text-white mb-4">Ažuriraj Status</h3>

                <form action="{{ route('admin.bug-reports.update', $bugReport) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="status" class="block text-sm font-medium text-white mb-2">Status</label>
                        <select id="status" name="status" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="pending" {{ $bugReport->status === 'pending' ? 'selected' : '' }}>Na Čekanju</option>
                            <option value="in_review" {{ $bugReport->status === 'in_review' ? 'selected' : '' }}>U Pregledu</option>
                            <option value="resolved" {{ $bugReport->status === 'resolved' ? 'selected' : '' }}>Riješeno</option>
                            <option value="closed" {{ $bugReport->status === 'closed' ? 'selected' : '' }}>Zatvoreno</option>
                        </select>
                    </div>

                    <div>
                        <label for="admin_notes" class="block text-sm font-medium text-white mb-2">Admin Bilješke</label>
                        <textarea id="admin_notes" name="admin_notes" rows="4" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Dodajte interne bilješke o ovom izvještaju...">{{ $bugReport->admin_notes }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Ažuriraj Izvještaj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>