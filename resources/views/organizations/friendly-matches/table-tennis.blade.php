<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
                    🏓 {{ __('Table Tennis Friendly Match') }}
                </h2>
                <p class="text-gray-400 mt-1">{{ $organization->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('organizations.friendly-matches.index', $organization) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200">
                    ← {{ __('Back to Friendly Matches') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('table-tennis-friendly', ['organization' => $organization, 'matchType' => request('type', 'individual')])
        </div>
    </div>
</x-app-layout>