<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $competition->name }} - Futsal Timovi
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    <a href="{{ route('organizations.show', $organization) }}" class="hover:underline">{{ $organization->name }}</a>
                </p>
            </div>
            @can('update', $organization)
                <a href="{{ route('organizations.competitions.futsal.teams.create', [$organization, $competition]) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Dodaj Tim
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if($teams->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nema timova</h3>
                        <p class="mt-1 text-sm text-gray-500">Započnite dodavanjem prvog tima.</p>
                        @can('update', $organization)
                            <div class="mt-6">
                                <a href="{{ route('organizations.competitions.futsal.teams.create', [$organization, $competition]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Dodaj Tim
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($teams as $team)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <!-- Team Logo and Name -->
                                <div class="flex items-center mb-4">
                                    @if($team->logo_url)
                                        <img src="{{ $team->logo_url }}" alt="{{ $team->name }}" class="w-16 h-16 rounded-full object-cover mr-4">
                                    @else
                                        <div class="w-16 h-16 rounded-full mr-4 flex items-center justify-center text-3xl"
                                             style="background-color: {{ $team->primary_color ?? '#3B82F6' }}">
                                            ⚽
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-gray-900">{{ $team->name }}</h3>
                                        @if($team->short_name)
                                            <p class="text-sm text-gray-500">{{ $team->short_name }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Team Info -->
                                <div class="space-y-2 mb-4">
                                    @if($team->coach_name)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Trener: {{ $team->coach_name }}
                                        </div>
                                    @endif
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Igrači: {{ $team->activePlayers->count() }}
                                    </div>
                                </div>

                                <!-- Team Colors -->
                                @if($team->primary_color || $team->secondary_color)
                                    <div class="flex items-center mb-4">
                                        <span class="text-sm text-gray-600 mr-2">Boje:</span>
                                        @if($team->primary_color)
                                            <div class="w-6 h-6 rounded border border-gray-300 mr-1"
                                                 style="background-color: {{ $team->primary_color }}"></div>
                                        @endif
                                        @if($team->secondary_color)
                                            <div class="w-6 h-6 rounded border border-gray-300"
                                                 style="background-color: {{ $team->secondary_color }}"></div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Action Button -->
                                <div class="mt-4">
                                    <a href="{{ route('organizations.competitions.futsal.teams.show', [$organization, $competition, $team]) }}"
                                       class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        Detalji Tima
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
