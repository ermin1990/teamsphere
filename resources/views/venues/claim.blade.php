<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Poveži svoj Teren
            </h2>
            <p class="text-gray-400 mt-1">Ovdje su tereni koje je dodao administrator, a čiji kontakt email odgovara vašem nalogu</p>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 rounded-xl p-4">
                <p class="text-green-400">{{ session('success') }}</p>
            </div>
        @endif

        @if($venues->count() > 0)
            <div class="space-y-4">
                @foreach($venues as $venue)
                    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-white font-semibold text-lg">{{ $venue->name }}</h3>
                            <p class="text-gray-400 text-sm">
                                {{ $venue->city?->name }}{{ $venue->address ? ' • ' . $venue->address : '' }}
                            </p>
                            <p class="text-gray-500 text-xs mt-1">Kontakt: {{ $venue->contact_email }}</p>
                        </div>
                        <form method="POST" action="{{ route('venues.claim.store', $venue) }}">
                            @csrf
                            <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-5 py-2.5 rounded-xl transition-all duration-200 whitespace-nowrap">
                                Poveži sa mojim nalogom
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <h3 class="text-xl font-semibold text-white mb-2">Nema terena za povezivanje</h3>
                <p class="text-gray-400 mb-6">
                    Nismo pronašli nijedan admin-kreiran teren čiji kontakt email odgovara vašem ({{ auth()->user()->email }}).
                    Ako mislite da je to greška, kontaktirajte administratora.
                </p>
                <a href="{{ route('venues.create') }}"
                   class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-all duration-200 inline-block">
                    Registruj Novi Teren
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
