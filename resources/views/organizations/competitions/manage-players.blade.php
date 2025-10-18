<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Upravljaj Igračima
                </h2>
                <p class="text-gray-400 mt-1">{{ $competition->name }} - {{ $organization->name }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="px-3 py-1 text-sm rounded-full bg-yellow-500/20 text-yellow-400">
                    Korak 1: Dodaj Igrače
                </span>
            </div>
        </div>
    </x-slot>

    @livewire('manage-players', ['organization' => $organization, 'competition' => $competition])
</x-app-layout>
