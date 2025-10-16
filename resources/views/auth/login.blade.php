<x-guest-layout>
    <!-- Session Status -->
    <div class="mb-6">
        <x-auth-session-status :status="session('status')" />
    </div>

    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
            Dobrodošli Nazad
        </h2>
        <p class="text-gray-400">Prijavite se na vaš Team Sphere nalog</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

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
                       autofocus
                       autocomplete="username"
                       placeholder="Unesite vaš email" />
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/20 to-purple-500/20 opacity-0 hover:opacity-100 transition-opacity duration-200 pointer-events-none"></div>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
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
                       autocomplete="current-password"
                       placeholder="Unesite vašu lozinku" />
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/20 to-purple-500/20 opacity-0 hover:opacity-100 transition-opacity duration-200 pointer-events-none"></div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center cursor-pointer group">
                <input id="remember_me"
                       type="checkbox"
                       class="w-4 h-4 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2 text-blue-500"
                       name="remember" />
                <span class="ml-2 text-sm text-gray-300 group-hover:text-white transition-colors">
                    Zapamti me
                </span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-400 hover:text-blue-300 transition-colors hover:underline"
                   href="{{ route('password.request') }}">
                    Zaboravili ste lozinku?
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div class="pt-2">
            <button type="submit"
                    id="login-button"
                    onclick="showLoginLoader()"
                    class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                <span id="login-text" class="flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Prijavi se</span>
                </span>
                <span id="login-loader" class="hidden flex items-center justify-center space-x-2">
                    <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Prijava u toku...</span>
                </span>
            </button>
        </div>

        <!-- Register Link -->
        <div class="text-center pt-4">
            <p class="text-gray-400 text-sm">
                Nemate nalog?
                <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 font-medium transition-colors hover:underline">
                    Kreirajte jedan
                </a>
            </p>
        </div>
    </form>

    <script>
        function showLoginLoader() {
            const loginText = document.getElementById('login-text');
            const loginLoader = document.getElementById('login-loader');
            const loginButton = document.getElementById('login-button');

            // Sakrij originalni tekst i prikaži loader
            loginText.classList.add('hidden');
            loginLoader.classList.remove('hidden');

            // Onemogući dugme da se ne može kliknuti više puta
            loginButton.disabled = true;
            loginButton.classList.add('opacity-75', 'cursor-not-allowed');
        }
    </script>
</x-guest-layout>
