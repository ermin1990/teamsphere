<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Novi Tim') }} - {{ $organization->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
                <form action="{{ route('organizations.teams.store', $organization) }}" method="POST" class="p-8">
                    @csrf
                    @if(isset($competitionId))
                        <input type="hidden" name="competition_id" value="{{ $competitionId }}">
                    @endif
                    
                    <div class="space-y-6">
                        <div>
                            <x-input-label for="name" :value="__('Naziv Tima')" class="text-gray-300" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-900/50 border-gray-700 text-white focus:border-emerald-500 focus:ring-emerald-500" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Opis (Opcionalno)')" class="text-gray-300" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full bg-gray-900/50 border-gray-700 text-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 space-x-4">
                        <a href="{{ route('organizations.teams.index', $organization) }}" class="text-gray-400 hover:text-white transition-colors">
                            Odustani
                        </a>
                        <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-500/20">
                            {{ __('Kreiraj Tim') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
