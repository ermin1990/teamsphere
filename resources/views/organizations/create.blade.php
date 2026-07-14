<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl sm:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Kreiraj Organizaciju
                </h2>
                <p class="text-gray-400 text-sm mt-1">Postavite svoju sportsku organizaciju da počnete upravljati ligama i timovima</p>
            </div>
            <a href="{{ route('dashboard') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-xl transition-all duration-200 inline-flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="text-sm sm:text-base">Nazad na Kontrolnu Tablu</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-8 border border-gray-700/50 shadow-2xl">
                <form method="POST" action="{{ route('organizations.store') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-white mb-2">
                            Naziv Organizacije
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Unesite naziv organizacije"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sport -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            Sport
                        </label>
                        <p class="text-xs text-gray-400 mb-3">
                            Organizacija vodi jedan sport - sva takmičenja koja kreirate unutar nje će biti tog sporta.
                            Za drugi sport (npr. Padel pored Stonog tenisa), kreirajte posebnu organizaciju.
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($sports as $sport)
                                <label class="relative cursor-pointer">
                                    <input type="radio"
                                           name="sport_id"
                                           value="{{ $sport->id }}"
                                           class="peer sr-only"
                                           {{ old('sport_id') == $sport->id ? 'checked' : '' }}
                                           required>
                                    <div class="flex flex-col items-center justify-center gap-2 px-4 py-4 bg-gray-700/50 border-2 border-gray-600 rounded-xl text-center transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 hover:border-gray-500">
                                        <span class="text-2xl">{{ $sport->icon }}</span>
                                        <span class="text-sm font-medium text-white">{{ $sport->name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('sport_id')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-white mb-2">
                            URL Slug
                            <span class="text-gray-400 text-xs">(opcionalno, bit će automatski generisano)</span>
                        </label>
                        <input type="text"
                               id="slug"
                               name="url_slug"
                               value="{{ old('url_slug') }}"
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="organization-url-slug">
                        <p class="mt-1 text-xs text-gray-400">
                            Ovo će biti korišteno u URL-u: {{ url('/organizations/') }}/<span id="slug-preview">{{ old('url_slug') ?: 'your-slug' }}</span>
                        </p>
                        @error('url_slug')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-white mb-2">
                            Opis
                            <span class="text-gray-400 text-xs">(opcionalno)</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                  placeholder="Opišite svoju organizaciju...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Plan Info -->
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-white font-medium">Vaš Plan</h4>
                                <p class="text-gray-400 text-sm">
                                    Trenutni plan: <span class="text-blue-400 font-medium">{{ Auth::user()->currentPlan() ? Auth::user()->currentPlan()->name : 'Free' }}</span>
                                    |
                                    Organizacije: {{ Auth::user()->organizations->count() }}/{{ Auth::user()->currentPlan() ? Auth::user()->currentPlan()->max_organizations : 1 }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @error('error')
                        <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-red-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-red-400">{{ $message }}</p>
                            </div>
                        </div>
                    @enderror

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700/50">
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl transition-all duration-200">
                            Otkaži
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25">
                            Kreiraj Organizaciju
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            const slug = name.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');

            document.getElementById('slug').value = slug;
            document.getElementById('slug-preview').textContent = slug || 'your-slug';
        });

        // Update slug preview
        document.getElementById('slug').addEventListener('input', function() {
            document.getElementById('slug-preview').textContent = this.value || 'your-slug';
        });
    </script>
</x-app-layout>