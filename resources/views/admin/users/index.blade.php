@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Upravljanje Korisnicima
            </h2>
            <p class="text-gray-400 mt-2">Pregled i upravljanje svim korisnicima sistema</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-400">Ukupno korisnika</p>
            <p class="text-2xl font-bold text-white">{{ $users->total() }}</p>
        </div>
    </div>

    <!-- Users List -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Lista Korisnika</h3>
        </div>

        <div class="divide-y divide-gray-700/50">
            @foreach($users as $user)
            <div class="p-6 hover:bg-gray-700/20 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold">{{ substr($user->name, 0, 2) }}</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">{{ $user->name }}</h4>
                            <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                        <div class="flex space-x-6 sm:space-x-0 sm:flex-col sm:items-center">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-white">{{ $user->organizations->count() }}</p>
                                <p class="text-gray-400 text-xs">Organizacija</p>
                            </div>

                            <div class="text-center">
                                <p class="text-2xl font-bold text-white">{{ $user->organizations->sum(function($org) { return $org->leagues->count(); }) }}</p>
                                <p class="text-gray-400 text-xs">Liga</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-400">{{ $user->created_at->format('d.m.Y') }}</p>
                                <p class="text-gray-500 text-xs">{{ $user->created_at->diffForHumans() }}</p>
                            </div>

                            <a href="{{ route('admin.users.show', $user) }}" class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-colors border border-blue-500/30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="p-6 border-t border-gray-700/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection