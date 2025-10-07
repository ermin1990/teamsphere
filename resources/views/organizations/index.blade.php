<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    {{ __('My Organizations') }}
                </h2>
                <p class="text-gray-400 mt-1">{{ __('Manage your sports organizations') }}</p>
            </div>
            <a href="{{ route('organizations.create') }}"
               class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                {{ __('Create Organization') }}
            </a>
        </div>
    </x-slot>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($organizations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($organizations as $organization)
                    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-200 group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-xl mb-2">{{ $organization->name }}</h3>
                                @if($organization->description)
                                    <p class="text-gray-400 text-sm mb-3">{{ Str::limit($organization->description, 100) }}</p>
                                @endif
                                <div class="flex items-center text-sm text-gray-400">
                                    <span class="bg-gray-600/50 px-2 py-1 rounded text-xs">{{ $organization->url_slug }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-400">
                                <span>{{ $organization->leagues->count() }} {{ __('Leagues') }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $organization->players->count() }} {{ __('Players') }}</span>
                            </div>
                            <a href="{{ route('organizations.show', $organization) }}"
                               class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">
                                {{ __('View') }} →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gradient-to-r from-gray-600 to-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">{{ __('No organizations yet') }}</h3>
                <p class="text-gray-400 mb-6">{{ __('Create your first organization to start managing your sports teams and leagues.') }}</p>
                <a href="{{ route('organizations.create') }}"
                   class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl inline-block">
                    {{ __('Create Your First Organization') }}
                </a>
            </div>
        @endif
    </div>
</x-app-layout>