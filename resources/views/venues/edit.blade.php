<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Uredi Teren
                </h2>
                <p class="text-gray-400 mt-1">Ažurirajte profil i logo vašeg terena</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('venues.public.show', $venue) }}" target="_blank" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    Pogledaj javnu stranicu ↗
                </a>
                <a href="{{ route('dashboard') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    ← Nazad
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-8 border border-gray-700/50 shadow-2xl">
                <form method="POST" action="{{ route('venues.update', $venue) }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-xl bg-gray-700/50 border border-gray-600 overflow-hidden flex items-center justify-center shrink-0">
                            @if($venue->logoSrc())
                                <img src="{{ $venue->logoSrc() }}" alt="{{ $venue->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-gray-500 text-xs">Bez loga</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label for="logo" class="block text-sm font-medium text-white mb-2">Logo</label>
                            <input type="file" id="logo" name="logo" accept="image/*"
                                   class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                            @error('logo')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-white mb-2">Naziv Terena</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $venue->name) }}"
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-white mb-2">URL Slug</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $venue->slug) }}"
                               class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               required>
                        <p class="mt-1 text-xs text-gray-400">
                            Javna stranica: {{ url('/tereni/') }}/<span id="slug-preview">{{ old('slug', $venue->slug) }}</span>
                        </p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-white mb-2">
                            Opis <span class="text-gray-400 text-xs">(opcionalno)</span>
                        </label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none">{{ old('description', $venue->description) }}</textarea>
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
                                    <option value="{{ $city->id }}" {{ (string) old('city_id', $venue->city_id) === (string) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-white mb-2">Adresa</label>
                            <input type="text" id="address" name="address" value="{{ old('address', $venue->address) }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            @error('address')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-white mb-2">Kontakt email</label>
                            <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $venue->contact_email) }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            @error('contact_email')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-white mb-2">Telefon</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $venue->phone) }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="website" class="block text-sm font-medium text-white mb-2">Web stranica</label>
                            <input type="url" id="website" name="website" value="{{ old('website', $venue->website) }}"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            @error('website')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-500/10 border border-green-500/20 rounded-xl p-4">
                            <p class="text-green-400">{{ session('success') }}</p>
                        </div>
                    @endif

                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-700/50">
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl transition-all duration-200">
                            Otkaži
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25">
                            Ažuriraj Teren
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('slug').addEventListener('input', function () {
            document.getElementById('slug-preview').textContent = this.value || '';
        });
    </script>
</x-app-layout>
