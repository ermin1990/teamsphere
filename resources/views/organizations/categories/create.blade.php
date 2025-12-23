<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
            {{ __('Add Category') }} - {{ $organization->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6 sm:p-8">
                <form action="{{ route('organizations.categories.store', $organization) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Name')" class="text-gray-300" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-900/50 border-gray-700 text-white focus:border-blue-500 focus:ring-blue-500" :value="old('name')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" class="text-gray-300" />
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full bg-gray-900/50 border-gray-700 text-white focus:border-blue-500 focus:ring-blue-500 rounded-xl">{{ old('description') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <a href="{{ route('organizations.categories.index', $organization) }}" class="text-gray-400 hover:text-white transition-colors">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                            {{ __('Create Category') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
