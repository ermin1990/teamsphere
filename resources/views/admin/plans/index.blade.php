<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">
                    Plan Management
                </h2>
                <p class="text-gray-400 mt-1">Overview of all subscription plans and their usage</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-white">{{ $plan->name }}</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                {{ $plan->name === 'Premium' ? 'bg-purple-500/20 text-purple-400' :
                                   ($plan->name === 'Pro' ? 'bg-blue-500/20 text-blue-400' : 'bg-green-500/20 text-green-400') }}">
                                {{ $plan->user_plans_count }} users
                            </span>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Max Organizations:</span>
                                <span class="text-white">{{ $plan->max_organizations }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Leagues per Org:</span>
                                <span class="text-white">{{ $plan->max_leagues_per_organization }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Price:</span>
                                <span class="text-white">${{ $plan->price }}/month</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-700">
                            <p class="text-gray-400 text-sm mb-2">Active Subscribers</p>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full"
                                     style="width: {{ $plan->user_plans_count > 0 ? min(100, ($plan->user_plans_count / 10) * 100) : 0 }}%"></div>
                            </div>
                            <p class="text-gray-400 text-xs mt-1">{{ $plan->user_plans_count }} active subscriptions</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Plan Usage Summary -->
            <div class="mt-8 bg-gray-800/50 backdrop-blur-xl rounded-3xl p-6 border border-gray-700/50">
                <h3 class="text-xl font-bold text-white mb-6">Plan Usage Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    @php
                        $totalUsers = $plans->sum('user_plans_count');
                        $freeUsers = \App\Models\User::whereDoesntHave('userPlans')->count();
                    @endphp

                    <div class="text-center">
                        <p class="text-3xl font-bold text-white">{{ $totalUsers }}</p>
                        <p class="text-gray-400">Paid Subscribers</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-white">{{ $freeUsers }}</p>
                        <p class="text-gray-400">Free Users</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-white">{{ $totalUsers + $freeUsers }}</p>
                        <p class="text-gray-400">Total Users</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-white">{{ $totalUsers > 0 ? round(($totalUsers / ($totalUsers + $freeUsers)) * 100, 1) : 0 }}%</p>
                        <p class="text-gray-400">Conversion Rate</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>