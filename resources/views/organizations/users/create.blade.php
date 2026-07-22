@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 border-b border-gray-700/50 px-8 py-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $organization->name }}</h1>
                    <p class="text-gray-400 mt-1">Add a user to manage match results</p>
                </div>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('organizations.users.store', $organization) }}">
                    @csrf

                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-white mb-2">User Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               class="w-full bg-gray-700/50 border border-gray-600/50 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="user@example.com" required>
                        @error('email')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-400">Enter the email address of a registered user</p>
                    </div>

                    <div class="mb-6">
                        <label for="role" class="block text-sm font-medium text-white mb-2">Role</label>
                        <select name="role" id="role" class="w-full bg-gray-700/50 border border-gray-600/50 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" required>
                            <option value="referee" {{ old('role') === 'referee' ? 'selected' : '' }}>Referee (Can edit match results)</option>
                            <option value="moderator" {{ old('role') === 'moderator' ? 'selected' : '' }}>Moderator (Može objavljivati obavijesti i pravila)</option>
                        </select>
                        <p class="mt-2 text-sm text-gray-400">Referees can only edit match results. Moderators can only publish announcements and rules.</p>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-700/50">
                        <a href="{{ route('organizations.users.index', $organization) }}" class="text-gray-400 hover:text-white transition-colors duration-200">
                            ← Cancel
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-blue-500/25 font-semibold">
                            Add User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection