<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl sm:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Registruj Teren
                </h2>
                <p class="text-gray-400 text-sm mt-1">Dodajte svoj teren da bi bio vidljiv igračima i organizatorima</p>
                <p class="text-gray-500 text-xs mt-2">
                    Da li je vaš teren već dodan od strane administratora?
                    <a href="{{ route('venues.claim.index') }}" class="text-blue-400 hover:text-blue-300">Povežite ga ovdje</a> umjesto da kreirate novi.
                </p>
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
                <form method="POST" action="{{ route('venues.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-white mb-2">Naziv Terena</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="npr. Total Padel Tuzla" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="url_slug" class="block text-sm font-medium text-white mb-2">
                            URL Slug <span class="text-gray-400 text-xs">(opcionalno, bit će automatski generisano)</span>
                        </label>
                        <input type="text" id="url_slug" name="url_slug" value="{{ old('url_slug') }}"
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="total-padel-tuzla">
                        <p class="mt-1 text-xs text-gray-400">
                            Javna stranica će biti: {{ url('/tereni/') }}/<span id="slug-preview">{{ old('url_slug') ?: 'vas-slug' }}</span>
                        </p>
                        @error('url_slug')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-white mb-2">
                            Opis <span class="text-gray-400 text-xs">(opcionalno)</span>
                        </label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                  placeholder="Opišite svoj teren...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="city_id" class="block text-sm font-medium text-white mb-2">Grad</label>
                            <select id="city_id" name="city_id"
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">— nije odabran —</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ (string) old('city_id') === (string) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-white mb-2">Adresa</label>
                            <input type="text" id="address" name="address" value="{{ old('address') }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Armije RBiH, Tuzla">
                            @error('address')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-white mb-2">Kontakt email</label>
                            <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="info@teren.ba">
                            @error('contact_email')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-white mb-2">Telefon</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="+387 35 123 456">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="website" class="block text-sm font-medium text-white mb-2">Web stranica</label>
                            <input type="url" id="website" name="website" value="{{ old('website') }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="https://...">
                            @error('website')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700/50">
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl transition-all duration-200">
                            Otkaži
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25">
                            Registruj Teren
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('name').addEventListener('input', function () {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            document.getElementById('url_slug').value = slug;
            document.getElementById('slug-preview').textContent = slug || 'vas-slug';
        });
        document.getElementById('url_slug').addEventListener('input', function () {
            document.getElementById('slug-preview').textContent = this.value || 'vas-slug';
        });
    </script>
</x-app-layout>
