<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ $user->name }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $user->email }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users.change-plan', $user) }}" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2 rounded-xl transition-all duration-200 transform hover:scale-105">
                    Change Plan
                </a>
                <a href="{{ route('admin.users') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    ← Back to Users
                </a>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- User Info -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                    <h3 class="text-xl font-bold text-white mb-4">Account Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">User ID:</span>
                            <span class="text-white">{{ $user->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Email:</span>
                            <span class="text-white">{{ $user->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Joined:</span>
                            <span class="text-white">{{ $user->created_at->format('M j, Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Last Updated:</span>
                            <span class="text-white">{{ $user->updated_at->format('M j, Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                    <h3 class="text-xl font-bold text-white mb-4">Current Plan</h3>
                    @if($user->currentPlan)
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Plan Name:</span>
                                <span class="text-white">{{ $user->currentPlan->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Organizations:</span>
                                <span class="text-white">{{ $user->currentPlan->max_organizations }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Leagues per Org:</span>
                                <span class="text-white">{{ $user->currentPlan->max_leagues_per_organization }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Expires:</span>
                                <span class="text-white">{{ $user->userPlans()->active()->first()?->expires_at?->format('M j, Y') ?? 'Never' }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400">Free Plan</p>
                        <p class="text-sm text-gray-500">Unlimited organizations and leagues</p>
                    @endif
                </div>

                <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                    <h3 class="text-xl font-bold text-white mb-4">Statistics</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Organizations:</span>
                            <span class="text-white font-medium">{{ $user->organizations->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Leagues:</span>
                            <span class="text-white font-medium">{{ $user->leagues->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total Players:</span>
                            <span class="text-white font-medium">{{ $user->organizations->sum(fn($org) => $org->players->count()) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total Teams:</span>
                            <span class="text-white font-medium">{{ $user->organizations->sum(fn($org) => $org->leagues->sum(fn($league) => $league->teams->count())) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Active Matches:</span>
                            <span class="text-white font-medium">{{ $user->organizations->sum(fn($org) => $org->leagues->sum(fn($league) => $league->matches()->where('status', 'in_progress')->count())) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Completed Matches:</span>
                            <span class="text-white font-medium">{{ $user->organizations->sum(fn($org) => $org->leagues->sum(fn($league) => $league->matches()->where('status', 'completed')->count())) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organizations -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50 mb-8">
                <h3 class="text-xl font-bold text-white mb-4">Organizations ({{ $user->organizations->count() }})</h3>
                @if($user->organizations->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($user->organizations as $organization)
                            <div class="bg-gray-700/30 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-white font-medium">{{ $organization->name }}</h4>
                                    <a href="{{ route('admin.organizations.show', $organization) }}" class="text-blue-400 hover:text-blue-300 text-sm">
                                        View
                                    </a>
                                </div>
                                <p class="text-gray-400 text-sm mb-2">{{ $organization->description }}</p>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Leagues: {{ $organization->leagues->count() }}</span>
                                    <span class="text-gray-400">{{ $organization->created_at->format('M j, Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400">No organizations created yet</p>
                @endif
            </div>

            <!-- Leagues -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                <h3 class="text-xl font-bold text-white mb-4">Leagues ({{ $user->leagues->count() }})</h3>
                @if($user->leagues->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="text-left py-2 px-4 text-gray-400 font-medium">League</th>
                                    <th class="text-left py-2 px-4 text-gray-400 font-medium">Organization</th>
                                    <th class="text-left py-2 px-4 text-gray-400 font-medium">Sport</th>
                                    <th class="text-left py-2 px-4 text-gray-400 font-medium">Players</th>
                                    <th class="text-left py-2 px-4 text-gray-400 font-medium">Status</th>
                                    <th class="text-left py-2 px-4 text-gray-400 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->leagues as $league)
                                    <tr class="border-b border-gray-700/50">
                                        <td class="py-3 px-4">
                                            <div>
                                                <p class="text-white font-medium">{{ $league->name }}</p>
                                                <p class="text-gray-400 text-sm">{{ $league->organization->name }}</p>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-gray-400">{{ $league->organization->name }}</td>
                                        <td class="py-3 px-4 text-gray-400">{{ $league->sport->name ?? 'Unknown' }}</td>
                                        <td class="py-3 px-4 text-white">{{ $league->players->count() }}</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 rounded-full text-xs
                                                {{ $league->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                                {{ $league->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <a href="{{ route('admin.leagues.show', $league) }}" class="text-blue-400 hover:text-blue-300 text-sm">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-400">No leagues created yet</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>