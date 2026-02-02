<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
            {{ __('Edit Category') }}: {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl p-6 sm:p-8">
                <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" :value="__('Name')" class="text-gray-300" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-900/50 border-gray-700 text-white focus:border-blue-500 focus:ring-blue-500" :value="old('name', $category->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" class="text-gray-300" />
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full bg-gray-900/50 border-gray-700 text-white focus:border-blue-500 focus:ring-blue-500 rounded-xl">{{ old('description', $category->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="rounded border-gray-700 bg-gray-900/50 text-blue-600 focus:ring-blue-500">
                        <x-input-label for="is_active" :value="__('Active')" class="text-gray-300" />
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <a href="{{ route('organizations.categories.index', $organization) }}" class="text-gray-400 hover:text-white transition-colors">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                            {{ __('Update Category') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
