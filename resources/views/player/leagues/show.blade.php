<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-lg sm:text-xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent" style="font-family: 'Unbounded', ui-sans-serif, sans-serif; letter-spacing: -0.01em;">
                {{ $competition->name }}
            </h2>
            <p class="text-xs mt-0.5" style="color: var(--text-tertiary);">{{ $organization->name }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('player.partials.nav')
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('public.leagues.partials.content')
        </div>
    </div>
</x-app-layout>
