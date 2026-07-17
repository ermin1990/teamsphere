<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Moj plan
            </h2>
            <p class="text-gray-400 mt-1">Trenutni plan i dostupne mogućnosti</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-500/10 border border-green-500/30 rounded-xl p-4 text-green-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-white">Vaš Plan i Ograničenja</h3>
                        <p class="text-gray-400 text-sm">Trenutni plan i dostupne mogućnosti</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Current Plan -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Trenutni Plan</span>
                            <span class="px-2 py-1 bg-green-600/20 text-green-400 text-xs rounded-full">{{ $currentPlan->name ?? 'Free' }}</span>
                        </div>
                        <div class="text-2xl font-bold text-white">
                            {{ $currentPlan->formatted_price ?? 'Besplatno' }}
                        </div>
                    </div>

                    <!-- Organizations Used -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Organizacije</span>
                            <span class="text-xs text-gray-500">{{ $usageStats['organizations_used'] }}/{{ $usageStats['max_organizations'] }}</span>
                        </div>
                        <div class="text-2xl font-bold text-white">{{ $usageStats['organizations_used'] }}</div>
                        <div class="text-xs text-gray-400">
                            {{ max(0, $usageStats['max_organizations'] - $usageStats['organizations_used']) }} preostalo
                        </div>
                    </div>

                    <!-- Competitions Used -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Turniri</span>
                            <span class="text-xs text-gray-500">{{ $usageStats['competitions_used'] }}/{{ $organizationsCount * $usageStats['max_competitions_per_organization'] }}</span>
                        </div>
                        <div class="text-2xl font-bold text-white">{{ $usageStats['competitions_used'] }}</div>
                        <div class="text-xs text-gray-400">
                            {{ max(0, ($organizationsCount * $usageStats['max_competitions_per_organization']) - $usageStats['competitions_used']) }} preostalo
                        </div>
                    </div>

                    <!-- Leagues Used -->
                    <div class="bg-gray-700/30 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-sm">Lige</span>
                            <span class="text-xs text-gray-500">{{ $usageStats['leagues_used'] }}/{{ $organizationsCount * $usageStats['max_leagues_per_organization'] }}</span>
                        </div>
                        <div class="text-2xl font-bold text-white">{{ $usageStats['leagues_used'] }}</div>
                        <div class="text-xs text-gray-400">
                            {{ max(0, ($organizationsCount * $usageStats['max_leagues_per_organization']) - $usageStats['leagues_used']) }} preostalo
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="button" onclick="document.getElementById('planUpgradeModal').classList.remove('hidden')"
                            class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-purple-500/25 inline-block">
                        <span class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>Zatraži veći plan</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Upgrade Modal -->
    <div id="planUpgradeModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-2xl p-6 max-w-md w-full border border-gray-700 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Zatraži veći plan</h3>
                <button type="button" onclick="document.getElementById('planUpgradeModal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('plan-upgrade.request') }}">
                @csrf
                <label for="plan_message" class="block text-sm font-medium text-gray-300 mb-2">Poruka (opcionalno)</label>
                <textarea name="message" id="plan_message" rows="3" maxlength="1000"
                          placeholder="Npr. koji plan te zanima ili koliko organizacija/takmičenja ti treba"
                          class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4"></textarea>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('planUpgradeModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Odustani
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Pošalji zahtjev
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
