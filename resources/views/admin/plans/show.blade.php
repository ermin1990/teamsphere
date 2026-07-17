@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.plans.index') }}" class="p-2 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $plan->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $plan->description }}</p>
            </div>
        </div>
        <div class="text-right">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.plans.edit', $plan) }}"
                   class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg transition-colors text-sm font-medium">
                    Edit Plan
                </a>
            </div>
            <p class="text-sm text-gray-400 mt-2">Kreiran</p>
            <p class="text-lg font-semibold text-white">{{ $plan->created_at->format('d.m.Y') }}</p>
        </div>
    </div>

    <!-- Plan Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $plan->formatted_price }}</p>
                    <p class="text-gray-400 text-sm">Cijena</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $plan->userPlans->count() }}</p>
                    <p class="text-gray-400 text-sm">Aktivnih korisnika</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $plan->max_organizations }}</p>
                    <p class="text-gray-400 text-sm">Max organizacija</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    @if($plan->is_active)
                        <p class="text-2xl font-bold text-green-400">Aktivan</p>
                        <p class="text-gray-400 text-sm">Status</p>
                    @else
                        <p class="text-2xl font-bold text-red-400">Neaktivan</p>
                        <p class="text-gray-400 text-sm">Status</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Plan Limits -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
            <div class="p-6 border-b border-gray-700/50">
                <h3 class="text-xl font-bold text-white">Ograničenja Plana</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Max organizacija po korisniku:</span>
                    <span class="text-white font-semibold">{{ $plan->max_organizations }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Max liga po organizaciji:</span>
                    <span class="text-white font-semibold">{{ $plan->max_leagues_per_organization }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Max takmičenja po organizaciji:</span>
                    <span class="text-white font-semibold">{{ $plan->max_competitions_per_organization }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Max timova po ligi:</span>
                    <span class="text-white font-semibold">{{ $plan->max_teams_per_league }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Max igrača po timu:</span>
                    <span class="text-white font-semibold">{{ $plan->max_players_per_team }}</span>
                </div>
            </div>
        </div>

        <!-- Plan Features -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
            <div class="p-6 border-b border-gray-700/50">
                <h3 class="text-xl font-bold text-white">Značajke Plana</h3>
            </div>
            <div class="p-6">
                @if($plan->features && count($plan->features) > 0)
                    <ul class="space-y-2">
                        @foreach($plan->features as $feature)
                            <li class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-300">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-400">Nema definiranih značajki</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Users with this Plan -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Korisnici na ovom planu</h3>
        </div>

        <div class="divide-y divide-gray-700/50">
            @forelse($plan->userPlans as $userPlan)
                <div class="p-6 hover:bg-gray-700/20 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold">{{ substr($userPlan->user->name, 0, 2) }}</span>
                            </div>
                            <div>
                                <h4 class="text-white font-semibold">{{ $userPlan->user->name }}</h4>
                                <p class="text-gray-400 text-sm">{{ $userPlan->user->email }}</p>
                                @if($userPlan->user->organizations->isNotEmpty())
                                    <p class="text-gray-500 text-xs mt-1">
                                        Organizacije: {{ $userPlan->user->organizations->pluck('name')->implode(', ') }}
                                    </p>
                                @else
                                    <p class="text-gray-500 text-xs mt-1">Nema organizacija</p>
                                @endif
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="text-sm text-gray-400">Pretplaćen</p>
                            <p class="text-white">{{ $userPlan->created_at->format('d.m.Y') }}</p>
                            @if($userPlan->expires_at)
                                <p class="text-xs text-gray-500 mt-1">Ističe {{ $userPlan->expires_at->format('d.m.Y') }}</p>
                            @endif
                            @if(!$userPlan->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400 mt-1">Neaktivan</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-gray-400">Nema korisnika na ovom planu</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection