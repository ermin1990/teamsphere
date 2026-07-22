<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Obavijesti
                </h2>
                <p class="text-gray-400 mt-1">
                    {{ $organization->name }}
                    @if($selectedCompetition) &middot; {{ $selectedCompetition->name }} @endif
                </p>
            </div>
            <div class="flex items-center space-x-3">
                @if($canManage)
                <a href="{{ route('organizations.announcements.create', array_filter(['organization' => $organization->slug, 'competition_id' => $selectedCompetition?->id])) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition-all duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nova Obavijest
                </a>
                @endif
                <a href="{{ route('organizations.show', $organization) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Nazad
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                <p class="text-green-400">{{ session('success') }}</p>
            </div>
            @endif

            <!-- Filter -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-4 border border-gray-700/50 shadow-xl">
                <form method="GET" action="{{ route('organizations.announcements.index', $organization) }}" class="flex flex-wrap items-center gap-3">
                    <label class="text-sm text-gray-400">Filter:</label>
                    <select name="competition_id" onchange="this.form.submit()" class="bg-gray-700/50 border border-gray-600 rounded-xl px-3 py-2 text-white text-sm">
                        <option value="" {{ (!$onlyOrganizationWide && !$selectedCompetition) ? 'selected' : '' }}>Sve (organizacija + sve lige)</option>
                        <option value="org" {{ $onlyOrganizationWide ? 'selected' : '' }}>Samo organizacija</option>
                        @foreach($competitions as $comp)
                            <option value="{{ $comp->id }}" {{ $selectedCompetition?->id === $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- List -->
            <div class="space-y-4">
                @forelse($announcements as $announcement)
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $announcement->isOrganizationWide() ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30' }}">
                                        {{ $announcement->isOrganizationWide() ? 'Cijela organizacija' : $announcement->competition->name }}
                                    </span>
                                    @if($announcement->is_featured)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            📌 Izdvojeno
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-500">{{ $announcement->created_at->format('d.m.Y. H:i') }}</span>
                                </div>
                                <h3 class="text-white font-semibold text-lg">{{ $announcement->title }}</h3>
                                <p class="text-gray-300 text-sm mt-1 whitespace-pre-line">{{ $announcement->body }}</p>
                                <p class="text-xs text-gray-500 mt-2">Autor: {{ $announcement->user->name }}</p>
                            </div>
                            @if($canManage)
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('organizations.announcements.edit', [$organization, $announcement]) }}" class="p-2 text-gray-400 hover:text-blue-400 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('organizations.announcements.destroy', [$organization, $announcement]) }}" onsubmit="return confirm('Sigurno obrisati ovu obavijest?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl text-center text-gray-400 italic">
                        Još nema objavljenih obavijesti.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
