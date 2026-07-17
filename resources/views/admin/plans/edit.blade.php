@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.plans.show', $plan) }}" class="p-2 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Edit Plan: {{ $plan->name }}
                </h2>
                <p class="text-gray-400 mt-1">Modify plan settings and limits</p>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
        <form action="{{ route('admin.plans.update', $plan) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Plan Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $plan->name) }}"
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Cijena</label>
                    <div class="flex">
                        <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $plan->price) }}"
                               class="flex-1 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                               required>
                        <select name="currency" class="px-3 py-3 bg-gray-700/50 border-y border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white">
                            <option value="BAM" {{ old('currency', $plan->currency) == 'BAM' ? 'selected' : '' }}>BAM (KM)</option>
                            <option value="EUR" {{ old('currency', $plan->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="USD" {{ old('currency', $plan->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                        <select name="billing_period" class="px-3 py-3 bg-gray-700/50 border border-l-0 border-gray-600 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white">
                            <option value="yearly" {{ old('billing_period', $plan->billing_period) == 'yearly' ? 'selected' : '' }}>godišnje</option>
                            <option value="monthly" {{ old('billing_period', $plan->billing_period) == 'monthly' ? 'selected' : '' }}>mjesečno</option>
                        </select>
                    </div>
                    @error('price')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    @error('billing_period')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                          required>{{ old('description', $plan->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Limits -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="max_organizations" class="block text-sm font-medium text-gray-300 mb-2">Max Organizations</label>
                    <input type="number" name="max_organizations" id="max_organizations" value="{{ old('max_organizations', $plan->max_organizations) }}"
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                           required>
                    @error('max_organizations')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_leagues_per_organization" class="block text-sm font-medium text-gray-300 mb-2">Max Leagues per Organization</label>
                    <input type="number" name="max_leagues_per_organization" id="max_leagues_per_organization" value="{{ old('max_leagues_per_organization', $plan->max_leagues_per_organization) }}"
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                           required>
                    @error('max_leagues_per_organization')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_competitions_per_organization" class="block text-sm font-medium text-gray-300 mb-2">Max Competitions per Organization</label>
                    <input type="number" name="max_competitions_per_organization" id="max_competitions_per_organization" value="{{ old('max_competitions_per_organization', $plan->max_competitions_per_organization) }}"
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                           required>
                    @error('max_competitions_per_organization')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_teams_per_league" class="block text-sm font-medium text-gray-300 mb-2">Max Teams per League</label>
                    <input type="number" name="max_teams_per_league" id="max_teams_per_league" value="{{ old('max_teams_per_league', $plan->max_teams_per_league) }}"
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                           required>
                    @error('max_teams_per_league')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_players_per_team" class="block text-sm font-medium text-gray-300 mb-2">Max Players per Team</label>
                    <input type="number" name="max_players_per_team" id="max_players_per_team" value="{{ old('max_players_per_team', $plan->max_players_per_team) }}"
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                           required>
                    @error('max_players_per_team')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Features -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Features (one per line)</label>
                <textarea name="features" id="features" rows="4"
                          class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                          placeholder="Enter each feature on a new line">{{ old('features', collect($plan->features)->implode("\n")) }}</textarea>
                @error('features')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="flex items-center space-x-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                       class="w-5 h-5 rounded bg-gray-700/50 border-gray-600 text-blue-500 focus:ring-blue-500">
                <label for="is_active" class="text-sm font-medium text-gray-300">Plan je aktivan (vidljiv i dodjeljiv korisnicima)</label>
            </div>

            <!-- Submit -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.plans.show', $plan) }}"
                   class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                    Update Plan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection