<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    User Management
                </h2>
                <p class="text-gray-400 mt-1">Manage all registered users and their accounts</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-3xl border border-gray-700/50 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-white">All Users ({{ $users->total() }})</h3>
                        <div class="flex space-x-2">
                            <input type="text" placeholder="Search users..." class="px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-700">
                                    <th class="text-left py-3 px-4 text-gray-400 font-medium">User</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-medium">Email</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-medium">Admin</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-medium">Joined</th>
                                    <th class="text-left py-3 px-4 text-gray-400 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr class="border-b border-gray-700/50 hover:bg-gray-700/20">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center">
                                                    <span class="text-blue-400 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <p class="text-white font-medium">{{ $user->name }}</p>
                                                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="text-white">{{ $user->email }}</span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="px-2 py-1 rounded text-xs {{ $user->is_admin ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                                                {{ $user->is_admin ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="text-gray-400 text-sm">{{ $user->created_at->format('M j, Y') }}</span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.users.show', $user) }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                                    View Details
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 px-4 text-center text-gray-400">
                                            No users found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="px-6 py-4 border-t border-gray-700">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>