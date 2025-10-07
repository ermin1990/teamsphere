<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-red-400 to-purple-400 bg-clip-text text-transparent">
                    Admin Dashboard
                </h2>
                <p class="text-gray-400 mt-1">System overview and management</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-400 text-sm">Total Users</p>
                            <p class="text-white text-2xl font-bold">{{ $stats['total_users'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-400 text-sm">Organizations</p>
                            <p class="text-white text-2xl font-bold">{{ $stats['total_organizations'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-400 text-sm">Leagues</p>
                            <p class="text-white text-2xl font-bold">{{ $stats['total_leagues'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-400 text-sm">Paid Plans</p>
                            <p class="text-white text-2xl font-bold">{{ $stats['active_plans'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gray-500/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-400 text-sm">Free Users</p>
                            <p class="text-white text-2xl font-bold">{{ $stats['free_users'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Users -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                    <h3 class="text-xl font-bold text-white mb-4">Recent Users</h3>
                    <div class="space-y-3">
                        @forelse($recentUsers as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-700/30 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                        <span class="text-blue-400 text-sm font-medium">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-white text-sm font-medium">{{ $user->name }}</p>
                                        <p class="text-gray-400 text-xs">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full {{ $user->currentPlan ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                                    {{ $user->currentPlan ? $user->currentPlan->name : 'Free' }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm">No users yet</p>
                        @endforelse
                    </div>
                    <a href="{{ route('admin.users') }}" class="text-blue-400 hover:text-blue-300 text-sm mt-4 inline-block">View all users →</a>
                </div>

                <!-- Recent Organizations -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                    <h3 class="text-xl font-bold text-white mb-4">Recent Organizations</h3>
                    <div class="space-y-3">
                        @forelse($recentOrganizations as $org)
                            <div class="flex items-center justify-between p-3 bg-gray-700/30 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                                        <span class="text-green-400 text-sm font-medium">{{ substr($org->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-white text-sm font-medium">{{ $org->name }}</p>
                                        <p class="text-gray-400 text-xs">by {{ $org->user->name }}</p>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full bg-purple-500/20 text-purple-400">
                                    {{ $org->leagues->count() }} leagues
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm">No organizations yet</p>
                        @endforelse
                    </div>
                    <a href="{{ route('admin.organizations') }}" class="text-blue-400 hover:text-blue-300 text-sm mt-4 inline-block">View all organizations →</a>
                </div>

                <!-- Recent Leagues -->
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                    <h3 class="text-xl font-bold text-white mb-4">Recent Leagues</h3>
                    <div class="space-y-3">
                        @forelse($recentLeagues as $league)
                            <div class="flex items-center justify-between p-3 bg-gray-700/30 rounded-xl">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                        <span class="text-purple-400 text-sm font-medium">{{ substr($league->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-white text-sm font-medium">{{ $league->name }}</p>
                                        <p class="text-gray-400 text-xs">{{ $league->organization->name }}</p>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-500/20 text-blue-400">
                                    {{ $league->sport->name ?? 'Unknown' }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm">No leagues yet</p>
                        @endforelse
                    </div>
                    <a href="{{ route('admin.leagues') }}" class="text-blue-400 hover:text-blue-300 text-sm mt-4 inline-block">View all leagues →</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>