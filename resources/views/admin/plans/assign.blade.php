@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users.show', $user) }}" class="p-2 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Assign Plan to {{ $user->name }}
                </h2>
                <p class="text-gray-400 mt-1">Choose a plan and set expiration date</p>
            </div>
        </div>
    </div>

    <!-- Current Plan Info -->
    @if($currentPlan)
    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-2xl p-6">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-yellow-400">Current Active Plan</h3>
                <p class="text-gray-300">
                    {{ $currentPlan->plan->name }} -
                    @if($currentPlan->expires_at)
                        Expires: {{ $currentPlan->expires_at->format('d.m.Y') }}
                    @else
                        No expiration
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Assign Form -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-xl">
        <form action="{{ route('admin.users.assign-plan.store', $user) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Plan Selection -->
            <div>
                <label for="plan_id" class="block text-sm font-medium text-gray-300 mb-4">Select Plan</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($plans as $plan)
                    <div class="relative">
                        <input type="radio" name="plan_id" id="plan_{{ $plan->id }}" value="{{ $plan->id }}"
                               class="sr-only peer" {{ old('plan_id', $currentPlan?->plan_id) == $plan->id ? 'checked' : '' }} required>
                        <label for="plan_{{ $plan->id }}"
                               class="block p-4 bg-gray-700/50 border border-gray-600 rounded-lg cursor-pointer hover:bg-gray-600/50 hover:border-gray-500 transition-colors peer-checked:border-blue-500 peer-checked:bg-blue-500/10">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-white">{{ $plan->name }}</h3>
                                <div class="text-right">
                                    <p class="text-xl font-bold text-blue-400">{{ $plan->price }} {{ $plan->currency }}</p>
                                </div>
                            </div>
                            <p class="text-gray-400 text-sm mb-3">{{ $plan->description }}</p>
                            <div class="space-y-1 text-xs text-gray-400">
                                <p>• Max Organizations: {{ $plan->max_organizations }}</p>
                                <p>• Max Leagues/Org: {{ $plan->max_leagues_per_organization }}</p>
                                <p>• Max Teams/League: {{ $plan->max_teams_per_league }}</p>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
                @error('plan_id')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Expiration Date -->
            <div>
                <label for="expires_at" class="block text-sm font-medium text-gray-300 mb-2">Expiration Date (Optional)</label>
                <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                       class="w-full md:w-1/2 px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                       min="{{ now()->addDay()->format('Y-m-d') }}">
                <p class="text-gray-400 text-sm mt-1">Leave empty for no expiration</p>
                @error('expires_at')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.users.show', $user) }}"
                   class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors">
                    Assign Plan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection