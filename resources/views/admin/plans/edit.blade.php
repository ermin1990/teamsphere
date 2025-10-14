<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">
                    Edit Plan: {{ $plan->name }}
                </h2>
                <p class="text-gray-400 mt-1">Modify plan details and limits</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.plans') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    ← Back to Plans
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-8 border border-gray-700/50">
                <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Plan Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $plan->name) }}"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-300 mb-2">Slug</label>
                                <input type="text" name="slug" id="slug" value="{{ old('slug', $plan->slug) }}"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       required>
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">{{ old('description', $plan->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6">Pricing</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Price ({{ $plan->currency }})</label>
                                <input type="number" name="price" id="price" value="{{ old('price', $plan->price) }}" step="0.01" min="0"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       required>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-300 mb-2">Currency</label>
                                <select name="currency" id="currency"
                                        class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="USD" {{ old('currency', $plan->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ old('currency', $plan->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="BAM" {{ old('currency', $plan->currency) == 'BAM' ? 'selected' : '' }}>BAM</option>
                                </select>
                                @error('currency')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Limits -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6">Limits & Restrictions</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="max_organizations" class="block text-sm font-medium text-gray-300 mb-2">Max Organizations</label>
                                <input type="number" name="max_organizations" id="max_organizations" value="{{ old('max_organizations', $plan->max_organizations) }}" min="0"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       required>
                                @error('max_organizations')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_leagues_per_organization" class="block text-sm font-medium text-gray-300 mb-2">Max Leagues per Organization</label>
                                <input type="number" name="max_leagues_per_organization" id="max_leagues_per_organization" value="{{ old('max_leagues_per_organization', $plan->max_leagues_per_organization) }}" min="0"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       required>
                                @error('max_leagues_per_organization')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_teams_per_league" class="block text-sm font-medium text-gray-300 mb-2">Max Teams per League</label>
                                <input type="number" name="max_teams_per_league" id="max_teams_per_league" value="{{ old('max_teams_per_league', $plan->max_teams_per_league) }}" min="0"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       required>
                                @error('max_teams_per_league')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_players_per_team" class="block text-sm font-medium text-gray-300 mb-2">Max Players per Team</label>
                                <input type="number" name="max_players_per_team" id="max_players_per_team" value="{{ old('max_players_per_team', $plan->max_players_per_team) }}" min="0"
                                       class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       required>
                                @error('max_players_per_team')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6">Status</h3>
                        <div class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-300">
                                Plan is active and available for purchase
                            </label>
                        </div>
                    </div>

                    <!-- Features -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6">Features (JSON)</h3>
                        <label for="features" class="block text-sm font-medium text-gray-300 mb-2">Features Array (leave empty for none)</label>
                        <textarea name="features" id="features" rows="4" placeholder='["Feature 1", "Feature 2", "Feature 3"]'
                                  class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 font-mono text-sm">{{ old('features', $plan->features ? json_encode($plan->features, JSON_PRETTY_PRINT) : '') }}</textarea>
                        <p class="mt-1 text-sm text-gray-400">Enter features as JSON array, e.g., ["Unlimited leagues", "Priority support"]</p>
                        @error('features')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end pt-6 border-t border-gray-700">
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Update Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>