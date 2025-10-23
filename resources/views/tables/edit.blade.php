<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Uredi Sto') }} - {{ $organization->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <a href="{{ route('organizations.tables.index', $organization) }}" 
                           class="text-blue-400 hover:text-blue-300">
                            ← Nazad na stolove
                        </a>
                    </div>

                    <form action="{{ route('organizations.tables.update', [$organization, $table]) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                                Naziv Stola *
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $table->name) }}"
                                   required
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                                   placeholder="npr. Sto 1, Glavni sto, Sto A">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                                Opis
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="3"
                                      class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                                      placeholder="Dodatne informacije o stolu (lokacija, karakteristike, itd.)">{{ old('description', $table->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $table->is_active) ? 'checked' : '' }}
                                       class="rounded bg-gray-700 border-gray-600 text-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-300">Sto je aktivan</span>
                            </label>
                            <p class="text-xs text-gray-400 mt-1">
                                Samo aktivni stolovi će biti dostupni za dodjeljivanje mečevima
                            </p>
                        </div>

                        <div class="flex space-x-4">
                            <button type="submit" 
                                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg">
                                Ažuriraj Sto
                            </button>
                            <a href="{{ route('organizations.tables.index', $organization) }}" 
                               class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg text-center">
                                Odustani
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
