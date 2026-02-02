<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl sm:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Upravljaj Igračima
                </h2>
                <p class="text-gray-400 mt-1">{{ $competition->name }} - {{ $organization->name }}</p>
            </div>
            <div class="flex justify-center sm:justify-end">
                <span class="px-3 py-1 text-sm rounded-full bg-yellow-500/20 text-yellow-400 text-center">
                    Korak 1: Dodaj Igrače
                </span>
            </div>
        </div>
    </x-slot>

    @livewire('manage-players', ['organization' => $organization, 'competition' => $competition])
</x-app-layout>
