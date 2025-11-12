<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Uredi Tim - {{ $team->name }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                <a href="{{ route('organizations.show', $organization) }}" class="hover:underline">{{ $organization->name }}</a>
                / <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" class="hover:underline">{{ $competition->name }}</a>
                / <a href="{{ route('organizations.competitions.futsal.teams.index', [$organization, $competition]) }}" class="hover:underline">Timovi</a>
                / <a href="{{ route('organizations.competitions.futsal.teams.show', [$organization, $competition, $team]) }}" class="hover:underline">{{ $team->name }}</a>
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('organizations.competitions.futsal.teams.update', [$organization, $competition, $team]) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Team Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Naziv Tima <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $team->name) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Short Name -->
                        <div class="mb-4">
                            <label for="short_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Kratak Naziv (Skraćenica)
                            </label>
                            <input type="text" id="short_name" name="short_name" value="{{ old('short_name', $team->short_name) }}" maxlength="50"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('short_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Logo -->
                        @if($team->logo_url)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Trenutni Logo</label>
                                <img src="{{ $team->logo_url }}" alt="{{ $team->name }}" class="w-24 h-24 rounded-full object-cover">
                            </div>
                        @endif

                        <!-- Logo Upload -->
                        <div class="mb-4">
                            <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $team->logo_url ? 'Promijeni Logo' : 'Logo Tima' }}
                            </label>
                            <input type="file" id="logo" name="logo" accept="image/*"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Maksimalna veličina: 2MB</p>
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Colors -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-2">
                                    Primarna Boja
                                </label>
                                <input type="color" id="primary_color" name="primary_color" value="{{ old('primary_color', $team->primary_color ?? '#3B82F6') }}"
                                       class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('primary_color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-2">
                                    Sekundarna Boja
                                </label>
                                <input type="color" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $team->secondary_color ?? '#1E40AF') }}"
                                       class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('secondary_color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Captain Name -->
                        <div class="mb-4">
                            <label for="captain_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Kapiten Tima
                            </label>
                            <input type="text" id="captain_name" name="captain_name" value="{{ old('captain_name', $team->captain_name) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('captain_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Coach Name -->
                        <div class="mb-4">
                            <label for="coach_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Trener
                            </label>
                            <input type="text" id="coach_name" name="coach_name" value="{{ old('coach_name', $team->coach_name) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('coach_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Home Venue -->
                        <div class="mb-4">
                            <label for="home_venue" class="block text-sm font-medium text-gray-700 mb-2">
                                Domaća Dvorana
                            </label>
                            <input type="text" id="home_venue" name="home_venue" value="{{ old('home_venue', $team->home_venue) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('home_venue')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Is Active -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $team->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Tim je aktivan</span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('organizations.competitions.futsal.teams.show', [$organization, $competition, $team]) }}"
                                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                    Odustani
                                </a>
                            </div>
                            <div class="flex space-x-3">
                                <form action="{{ route('organizations.competitions.futsal.teams.destroy', [$organization, $competition, $team]) }}"
                                      method="POST" onsubmit="return confirm('Da li ste sigurni da želite obrisati ovaj tim? Ova akcija se ne može poništiti.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                        Obriši Tim
                                    </button>
                                </form>
                                <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Sačuvaj Izmjene
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
