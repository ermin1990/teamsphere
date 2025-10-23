<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Stolovi') }} - {{ $organization->name }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('organizations.tables.schedule', $organization) }}" 
                   class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    📅 Raspored Stolova
                </a>
                <a href="{{ route('organizations.tables.create', $organization) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Dodaj Sto
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <a href="{{ route('organizations.show', $organization) }}" 
                           class="text-blue-400 hover:text-blue-300">
                            ← Nazad na organizaciju
                        </a>
                    </div>

                    @if($tables->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($tables as $table)
                                <div class="bg-gray-700/50 rounded-lg p-6 border border-gray-600/50">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-xl font-bold text-white mb-2">
                                                {{ $table->name }}
                                            </h3>
                                            @if($table->description)
                                                <p class="text-gray-400 text-sm">
                                                    {{ $table->description }}
                                                </p>
                                            @endif
                                        </div>
                                        <div>
                                            @if($table->is_active)
                                                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">
                                                    Aktivan
                                                </span>
                                            @else
                                                <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded">
                                                    Neaktivan
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex space-x-2 mt-4">
                                        <a href="{{ route('organizations.tables.edit', [$organization, $table]) }}" 
                                           class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded text-center text-sm">
                                            Uredi
                                        </a>
                                        <form action="{{ route('organizations.tables.destroy', [$organization, $table]) }}" 
                                              method="POST" 
                                              class="flex-1"
                                              onsubmit="return confirm('Da li ste sigurni da želite obrisati ovaj sto?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded text-sm">
                                                Obriši
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-300">Nema stolova</h3>
                            <p class="mt-1 text-sm text-gray-400">Počnite kreiranjem prvog stola za vašu organizaciju.</p>
                            <div class="mt-6">
                                <a href="{{ route('organizations.tables.create', $organization) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded">
                                    + Dodaj Sto
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
