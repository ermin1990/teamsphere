<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Prijavi Bag / Predloži Funkciju
                </h2>
                <p class="text-gray-400 mt-1">Pomozite nam da poboljšamo MojTurnir</p>
            </div>
        </div>
    </x-slot>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 border border-white/20">
            @if(session('success'))
                <div class="mb-6 bg-green-500/20 border border-green-500/30 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-green-400 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('feedback.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Feedback Type -->
                <div>
                    <label class="block text-sm font-medium text-white mb-3">
                        Tip Povratne Informacije <span class="text-red-400">*</span>
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="radio"
                                   name="type"
                                   value="bug"
                                   {{ old('type') === 'bug' ? 'checked' : '' }}
                                   required
                                   class="text-purple-600 bg-white/10 border-white/20 focus:ring-purple-500 focus:border-purple-500">
                            <span class="ml-3 text-white">
                                <span class="font-medium">🐛 Prijavi Bag</span>
                                <span class="text-gray-400 block text-sm">Nešto ne radi kako se očekuje</span>
                            </span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio"
                                   name="type"
                                   value="feature"
                                   {{ old('type') === 'feature' ? 'checked' : '' }}
                                   required
                                   class="text-purple-600 bg-white/10 border-white/20 focus:ring-purple-500 focus:border-purple-500">
                            <span class="ml-3 text-white">
                                <span class="font-medium">💡 Predloži Funkciju</span>
                                <span class="text-gray-400 block text-sm">Imam ideju za novu funkciju</span>
                            </span>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-white mb-2">
                        Predmet <span class="text-red-400">*</span>
                    </label>
                    <input type="text"
                           id="subject"
                           name="subject"
                           value="{{ old('subject') }}"
                           required
                           maxlength="255"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           placeholder="Kratak opis problema ili funkcije">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-white mb-2">
                        Opis <span class="text-red-400">*</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="6"
                              required
                              maxlength="2000"
                              class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                              placeholder="Molimo vas da navedete detaljne informacije o bagu ili prijedlogu funkcije...">{{ old('description') }}</textarea>
                    <p class="mt-1 text-sm text-gray-400">Maksimalno 2000 karaktera</p>
                    @error('description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Information (Optional) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-white mb-2">
                            Vaše Ime <span class="text-white/50">(Opcionalno)</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               maxlength="255"
                               class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                               placeholder="Vaše ime">
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-white mb-2">
                            Vaš Email <span class="text-white/50">(Opcionalno)</span>
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               maxlength="255"
                               class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                               placeholder="vas.email@primjer.com">
                        <p class="mt-1 text-sm text-gray-400">Kontaktirat ćemo vas samo ako nam treba pojašnjenje</p>
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-6">
                    <button type="submit"
                            class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        Pošalji Povratnu Informaciju
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>