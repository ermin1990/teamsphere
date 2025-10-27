<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $organization->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ __('Add New Player') }}</p>
            </div>
        </div>
    </x-slot>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8 border border-white/20">
            <form action="{{ route('organizations.players.store', $organization) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Player Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-white mb-2">
                        {{ __('Player Name') }} <span class="text-red-400">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           placeholder="{{ __('Enter player name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email (Optional) -->
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">
                        {{ __('Email Address') }} <span class="text-white/50">({{ __('Optional - for registered users') }})</span>
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           placeholder="{{ __('Enter email to link with registered user') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-white/60">
                        {{ __('If you enter an email of a registered user, they will be linked to this player profile.') }}
                    </p>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-white mb-2">
                        {{ __('Date of Birth') }} <span class="text-white/50">({{ __('Optional') }})</span>
                    </label>
                    <input type="date"
                           id="date_of_birth"
                           name="date_of_birth"
                           value="{{ old('date_of_birth') }}"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Club/Team -->
                <div>
                    <label for="position" class="block text-sm font-medium text-white mb-2">
                        Klub <span class="text-white/50">(Opcionalno)</span>
                    </label>
                    <input type="text"
                           id="position"
                           name="position"
                           value="{{ old('position') }}"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           placeholder="Npr. ŠTK Maglaj, FK Željezničar, RK Borac">
                    @error('position')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jersey Number -->
                <div>
                    <label for="jersey_number" class="block text-sm font-medium text-white mb-2">
                        {{ __('Jersey Number') }} <span class="text-white/50">({{ __('Optional') }})</span>
                    </label>
                    <input type="number"
                           id="jersey_number"
                           name="jersey_number"
                           value="{{ old('jersey_number') }}"
                           min="1"
                           max="99"
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           placeholder="{{ __('1-99') }}">
                    @error('jersey_number')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-white/20">
                    <a href="{{ route('organizations.players.index', $organization) }}"
                       class="px-6 py-3 border border-white/20 text-white/70 hover:text-white hover:border-white/40 rounded-lg transition-all duration-200">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                            class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        {{ __('Add Player') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>