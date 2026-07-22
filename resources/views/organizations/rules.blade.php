<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Pravila Organizacije</h1>
                        <p class="text-gray-300">{{ $organization->name }}</p>
                    </div>
                    <a href="{{ route('organizations.show', $organization) }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors text-center sm:w-auto w-full">
                        Nazad
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                <p class="text-green-400">{{ session('success') }}</p>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                <ul class="list-disc list-inside text-red-400">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Default rules form -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl mb-6">
                <h2 class="text-lg font-semibold text-white mb-1">Opća pravila (za sve lige)</h2>
                <p class="text-sm text-gray-400 mb-4">
                    Ovaj tekst se prikazuje na svakoj ligi/takmičenju ove organizacije, osim ako ta liga ima svoja
                    posebna pravila koja ga zamjenjuju (podesivo na stranici "Pravila" svake pojedinačne lige).
                </p>
                <form method="POST" action="{{ route('organizations.update-rules', $organization) }}">
                    @csrf
                    <textarea name="rules_text" rows="14"
                              class="w-full bg-gray-700/50 border border-gray-600/50 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                              placeholder="Opiši opća pravila koja važe za sve lige u ovoj organizaciji...">{{ old('rules_text', $organization->rules_text) }}</textarea>
                    @error('rules_text')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl transition-all duration-200 font-semibold">
                            Sačuvaj Pravila
                        </button>
                    </div>
                </form>
            </div>

            <!-- Per-league override status -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h2 class="text-lg font-semibold text-white mb-4">Status po ligama</h2>
                @if($competitions->isEmpty())
                    <p class="text-gray-400 italic">Ova organizacija još nema takmičenja.</p>
                @else
                    <div class="space-y-2">
                        @foreach($competitions as $comp)
                            <div class="flex items-center justify-between p-3 bg-gray-900/40 rounded-lg">
                                <span class="text-white">{{ $comp->name }}</span>
                                <div class="flex items-center gap-3">
                                    @if($comp->hasRulesOverride())
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">Ima posebna pravila</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-600/30 text-gray-300 border border-gray-600/50">Koristi opća pravila</span>
                                    @endif
                                    <a href="{{ route('organizations.competitions.rules', [$organization, $comp]) }}" class="text-blue-400 hover:underline text-sm">Uredi</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
