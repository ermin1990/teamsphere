<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Uredi Organizaciju
                </h2>
                <p class="text-gray-400 mt-1">Ažurirajte detalje vaše organizacije</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('organizations.show', $organization) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    Otkaži
                </a>
                <a href="{{ route('dashboard') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    ← Nazad na Kontrolnu Tablu
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-8 border border-gray-700/50 shadow-2xl">
                <form method="POST" action="{{ route('organizations.update', $organization) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-white mb-2">
                            Naziv Organizacije
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $organization->name) }}"
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Unesite naziv organizacije"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-white mb-2">
                            URL Slug
                        </label>
                        <input type="text"
                               id="slug"
                               name="slug"
                               value="{{ old('slug', $organization->slug) }}"
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="organization-url-slug"
                               required>
                        <p class="mt-1 text-xs text-gray-400">
                            Ovo će biti korišteno u URL-u: {{ url('/organizations/') }}/<span id="slug-preview">{{ old('slug', $organization->slug) }}</span>
                        </p>
                        @error('slug')
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
                                  placeholder="Opišite svoju organizaciju...">{{ old('description', $organization->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Info -->
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4">
                        <h4 class="text-white font-medium mb-2">Trenutne Informacije</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Kreirano:</span>
                                <span class="text-white">{{ $organization->created_at->format('M j, Y \a\t H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Lige:</span>
                                <span class="text-white">{{ $organization->leagues->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Status:</span>
                                <span class="text-{{ $organization->is_active ? 'green' : 'red' }}-400">{{ $organization->is_active ? 'Aktivno' : 'Neaktivno' }}</span>
                            </div>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-500/10 border border-green-500/20 rounded-xl p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <p class="text-green-400">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

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
                        <a href="{{ route('organizations.show', $organization) }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl transition-all duration-200">
                            Otkaži
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25">
                            Ažuriraj Organizaciju
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