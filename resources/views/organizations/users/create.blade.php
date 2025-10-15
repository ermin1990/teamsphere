@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ $organization->name }}</h1>
                <p class="text-gray-600">Add a user to manage match results</p>
            </div>

            <form method="POST" action="{{ route('organizations.users.store', $organization) }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">User Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           placeholder="user@example.com" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Enter the email address of a registered user</p>
                </div>

                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="referee" {{ old('role') === 'referee' ? 'selected' : '' }}>Referee (Can edit match results)</option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Referees can only edit match results, nothing else</p>
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('organizations.users.index', $organization) }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection