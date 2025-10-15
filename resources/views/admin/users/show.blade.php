@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users.index') }}" class="p-2 bg-gray-700/50 hover:bg-gray-600/50 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $user->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $user->email }}</p>
            </div>
        </div>
        <div class="text-left sm:text-right">
            <p class="text-sm text-gray-400">Član od</p>
            <p class="text-lg font-semibold text-white">{{ $user->created_at->format('d.m.Y') }}</p>
        </div>
    </div>

    <!-- User Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $user->organizations->count() }}</p>
                    <p class="text-gray-400 text-sm">Organizacija</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $user->organizations->sum(function($org) { return $org->leagues->count(); }) }}</p>
                    <p class="text-gray-400 text-sm">Liga</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $user->organizations->sum(function($org) { return $org->leagues->sum(function($league) { return $league->matches->count(); }); }) }}</p>
                    <p class="text-gray-400 text-sm">Utakmica</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $user->currentPlan() ? $user->currentPlan()->name : 'Free' }}</p>
                    <p class="text-gray-400 text-sm">Plan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Organizations -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Organizacije</h3>
        </div>

        @if($user->organizations->count() > 0)
        <div class="divide-y divide-gray-700/50">
            @foreach($user->organizations as $organization)
            <div class="p-6 hover:bg-gray-700/20 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold">{{ substr($organization->name, 0, 2) }}</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">{{ $organization->name }}</h4>
                            <p class="text-gray-400 text-sm">{{ $organization->leagues->count() }} liga • {{ $organization->leagues->sum(function($league) { return $league->matches->count(); }) }} utakmica</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-400">{{ $organization->created_at->format('d.m.Y') }}</p>
                        </div>
                        <a href="{{ route('organizations.show', $organization) }}" class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-colors border border-blue-500/30">
                            Pregledaj
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h4 class="text-white font-semibold mb-2">Nema organizacija</h4>
            <p class="text-gray-400">Ovaj korisnik još nije kreirao nijednu organizaciju.</p>
        </div>
        @endif
    </div>

    <!-- User Plans History -->
    @if($user->userPlans->count() > 0)
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Historija Planova</h3>
        </div>

        <div class="divide-y divide-gray-700/50">
            @foreach($user->userPlans as $userPlan)
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-white font-semibold">{{ $userPlan->plan->name }}</h4>
                        <p class="text-gray-400 text-sm">{{ $userPlan->created_at->format('d.m.Y H:i') }} - {{ $userPlan->expires_at ? $userPlan->expires_at->format('d.m.Y H:i') : 'Trajno' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded-full text-sm {{ $userPlan->is_active ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                            {{ $userPlan->is_active ? 'Aktivan' : 'Neaktivan' }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection