<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
            Zaboravili ste Lozinku?
        </h2>
        <p class="text-gray-400">Nema problema. Samo nam recite vašu email adresu i poslat ćemo vam link za resetovanje lozinke.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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
                       autocomplete="email"
                       placeholder="Unesite vaš email" />
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/20 to-purple-500/20 opacity-0 hover:opacity-100 transition-opacity duration-200 pointer-events-none"></div>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
        </div>

        <div class="flex items-center justify-center mt-6">
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 font-medium">
                Pošalji Link za Resetovanje Lozinke
            </button>
        </div>
    </form>
</x-guest-layout>
