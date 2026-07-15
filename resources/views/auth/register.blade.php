<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
            Kreirajte Nalog
        </h2>
        <p class="text-gray-400">Pridružite se MojTurnir-u i počnite upravljati vašim timovima</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-gray-300">
                Puno Ime
            </label>
            <div class="relative">
                <input id="name"
                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-200 backdrop-blur-sm"
                       type="text"
                       name="name"
                       :value="old('name')"
                       required
                       autofocus
                       autocomplete="name"
                       placeholder="Unesite vaše puno ime" />
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/20 to-purple-500/20 opacity-0 hover:opacity-100 transition-opacity duration-200 pointer-events-none"></div>
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-300">
                Email Adresa
            </label>
            <div class="relative">
                <input id="email"
                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-200 backdrop-blur-sm"
                       type="email"
                       name="email"
                       :value="old('email')"
                       required
                       autocomplete="username"
                       placeholder="Unesite vaš email" />
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/20 to-purple-500/20 opacity-0 hover:opacity-100 transition-opacity duration-200 pointer-events-none"></div>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Role -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-300">
                Registruj se kao
            </label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative cursor-pointer">
                    <input type="radio" name="role" value="organizer" class="peer sr-only" {{ old('role', 'organizer') === 'organizer' ? 'checked' : '' }} />
                    <div class="px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-xl text-center text-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 peer-checked:text-white transition-all duration-200">
                        <span class="block font-medium">Organizator</span>
                        <span class="block text-xs text-gray-400 mt-0.5">Vodim ligu/turnir</span>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="role" value="player" class="peer sr-only" {{ old('role') === 'player' ? 'checked' : '' }} />
                    <div class="px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-xl text-center text-gray-300 peer-checked:border-purple-500 peer-checked:bg-purple-500/10 peer-checked:text-white transition-all duration-200">
                        <span class="block font-medium">Igrač</span>
                        <span class="block text-xs text-gray-400 mt-0.5">Igram u ligama</span>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-gray-300">
                Lozinka
            </label>
            <div class="relative">
                <input id="password"
                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-200 backdrop-blur-sm"
                       type="password"
                       name="password"
                       required
                       autocomplete="new-password"
                       placeholder="Unesite vašu lozinku" />
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/20 to-purple-500/20 opacity-0 hover:opacity-100 transition-opacity duration-200 pointer-events-none"></div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-300">
                Potvrdite Lozinku
            </label>
            <div class="relative">
                <input id="password_confirmation"
                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-200 backdrop-blur-sm"
                       type="password"
                       name="password_confirmation"
                       required
                       autocomplete="new-password"
                       placeholder="Potvrdite vašu lozinku" />
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/20 to-purple-500/20 opacity-0 hover:opacity-100 transition-opacity duration-200 pointer-events-none"></div>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Register Button -->
        <div class="pt-2">
            <button type="submit"
                    class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                <span class="flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    <span>Kreirajte Nalog</span>
                </span>
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center pt-4">
            <p class="text-gray-400 text-sm">
                Već imate nalog?
                <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-medium transition-colors hover:underline">
                    Prijavite se
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
