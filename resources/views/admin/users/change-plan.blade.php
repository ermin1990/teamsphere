<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Change Plan: {{ $user->name }}
                </h2>
                <p class="text-gray-400 mt-1">Modify user's subscription plan</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.users.show', $user) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    ← Back to User
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-8 border border-gray-700/50">
                <form method="POST" action="{{ route('admin.users.update-plan', $user) }}" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <!-- Current Plan Info -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6">Current Plan</h3>
                        @if($currentPlan)
                            <div class="bg-gray-700/30 rounded-xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-semibold text-white">{{ $currentPlan->name }}</h4>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-500/20 text-blue-400">
                                        Active
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-400">Price:</span>
                                        <p class="text-white font-medium">{{ $currentPlan->formatted_price }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Organizations:</span>
                                        <p class="text-white font-medium">{{ $currentPlan->max_organizations }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Leagues/Org:</span>
                                        <p class="text-white font-medium">{{ $currentPlan->max_leagues_per_organization }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Expires:</span>
                                        <p class="text-white font-medium">{{ $user->userPlans()->active()->first()?->expires_at?->format('M j, Y') ?? 'Never' }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-700/30 rounded-xl p-6">
                                <h4 class="text-lg font-semibold text-white">Free Plan</h4>
                                <p class="text-gray-400">No active subscription</p>
                            </div>
                        @endif
                    </div>

                    <!-- Select New Plan -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6">Select New Plan</h3>
                        <div class="space-y-4">
                            <!-- Free Plan Option -->
                            <label class="block">
                                <input type="radio" name="plan_id" value="" {{ !$currentPlan ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="p-6 bg-gray-700/30 border-2 border-gray-600 rounded-xl cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-500/10 transition-all duration-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-lg font-semibold text-white">Free Plan</h4>
                                            <p class="text-gray-400">Cancel subscription and switch to free plan</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-2xl font-bold text-white">Free</p>
                                            <p class="text-gray-400 text-sm">Forever</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Paid Plans -->
                            @foreach($plans as $plan)
                                <label class="block">
                                    <input type="radio" name="plan_id" value="{{ $plan->id }}" {{ $currentPlan && $currentPlan->id === $plan->id ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="p-6 bg-gray-700/30 border-2 border-gray-600 rounded-xl cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-500/10 transition-all duration-200">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-lg font-semibold text-white">{{ $plan->name }}</h4>
                                                @if($plan->description)
                                                    <p class="text-gray-400">{{ $plan->description }}</p>
                                                @endif
                                                <div class="mt-3 grid grid-cols-3 gap-4 text-sm">
                                                    <div>
                                                        <span class="text-gray-400">Organizations:</span>
                                                        <p class="text-white font-medium">{{ $plan->max_organizations }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-400">Leagues/Org:</span>
                                                        <p class="text-white font-medium">{{ $plan->max_leagues_per_organization }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-400">Teams/League:</span>
                                                        <p class="text-white font-medium">{{ $plan->max_teams_per_league }}</p>
                                                    </div>
                                                </div>
                                                @if($plan->features && count($plan->features) > 0)
                                                    <div class="mt-3">
                                                        <p class="text-gray-400 text-sm mb-2">Features:</p>
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach($plan->features as $feature)
                                                                <span class="bg-gray-600/50 px-2 py-1 rounded text-xs text-gray-300">{{ $feature }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <p class="text-2xl font-bold text-white">{{ $plan->formatted_price }}</p>
                                                <p class="text-gray-400 text-sm">per month</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('plan_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expiration Date -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6">Subscription Settings</h3>
                        <div class="max-w-md">
                            <label for="expires_at" class="block text-sm font-medium text-gray-300 mb-2">
                                Expiration Date (Optional)
                            </label>
                            <input type="date" name="expires_at" id="expires_at"
                                   class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   min="{{ now()->addDay()->format('Y-m-d') }}">
                            <p class="mt-1 text-sm text-gray-400">Leave empty for lifetime subscription</p>
                            @error('expires_at')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end pt-6 border-t border-gray-700">
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Update User Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>