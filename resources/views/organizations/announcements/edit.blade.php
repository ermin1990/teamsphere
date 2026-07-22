<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 border-b border-gray-700/50 px-8 py-6">
                    <h1 class="text-2xl font-bold text-white">Uredi Obavijest</h1>
                    <p class="text-gray-400 mt-1">{{ $organization->name }}</p>
                </div>

                <div class="p-8">
                    <form method="POST" action="{{ route('organizations.announcements.update', [$organization, $announcement]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="competition_id" class="block text-sm font-medium text-white mb-2">Cilj objave</label>
                            <select name="competition_id" id="competition_id" class="w-full bg-gray-700/50 border border-gray-600/50 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="" {{ old('competition_id', $announcement->competition_id) == null ? 'selected' : '' }}>Cijela organizacija (sve lige)</option>
                                @foreach($competitions as $comp)
                                    <option value="{{ $comp->id }}" {{ (int) old('competition_id', $announcement->competition_id) === $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
                                @endforeach
                            </select>
                            @error('competition_id')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-white mb-2">Naslov</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $announcement->title) }}"
                                   class="w-full bg-gray-700/50 border border-gray-600/50 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   required>
                            @error('title')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="body" class="block text-sm font-medium text-white mb-2">Tekst</label>
                            <textarea name="body" id="body" rows="8"
                                      class="w-full bg-gray-700/50 border border-gray-600/50 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                      required>{{ old('body', $announcement->body) }}</textarea>
                            @error('body')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 flex items-start gap-3 bg-gray-700/30 border border-gray-600/50 rounded-xl p-4">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $announcement->is_featured) ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                            <label for="is_featured" class="text-sm text-white">
                                Prikaži izdvojeno na stranici lige
                                <span class="block text-gray-400 text-xs mt-1">Pojavljuje se u istaknutom prostoru odmah ispod naslova takmičenja. Ako je izabrana "Cijela organizacija", pojavljuje se na svim ligama. Prikazuje se samo jedna izdvojena obavijest - najnovija koja ima ovo uključeno.</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-700/50">
                            <a href="{{ route('organizations.announcements.index', $organization) }}" class="text-gray-400 hover:text-white transition-colors duration-200">
                                ← Otkaži
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 font-semibold">
                                Sačuvaj
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
