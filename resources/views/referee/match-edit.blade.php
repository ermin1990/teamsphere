@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Uredi Rezultat Meča</h1>
                        <p class="mt-1 text-sm text-gray-500">{{ $league->name }} • {{ $league->sport->name }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('referee.match.show', [$league, $match]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Nazad na Meč
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form method="POST" action="{{ route('referee.match.update', [$league, $match]) }}">
                    @csrf
                    @method('PUT')

                    <!-- Match Info -->
                    <div class="mb-8">
                        <div class="text-center">
                            <div class="flex items-center justify-center space-x-8 mb-6">
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900 mb-2">{{ $match->homeTeam?->name ?? 'TBD' }}</div>
                                    <div class="text-3xl">{{ $league->sport->icon }}</div>
                                </div>

                                <div class="text-center">
                                    <div class="text-2xl text-gray-500">VS</div>
                                </div>

                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900 mb-2">{{ $match->awayTeam?->name ?? 'TBD' }}</div>
                                    <div class="text-3xl">{{ $league->sport->icon }}</div>
                                </div>
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ $league->name }} • {{ $league->sport->name }}
                            </div>
                        </div>
                    </div>

                    <!-- Score Input -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Rezultat Meča</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="home_score" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $match->homeTeam?->name ?? 'Domaći Tim' }} Rezultat
                                </label>
                                <input type="number" name="home_score" id="home_score" min="0"
                                       value="{{ old('home_score', $match->home_score) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="0">
                                @error('home_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="away_score" name="away_score" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $match->awayTeam?->name ?? 'Gostujući Tim' }} Rezultat
                                </label>
                                <input type="number" name="away_score" id="away_score" min="0"
                                       value="{{ old('away_score', $match->away_score) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="0">
                                @error('away_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Match Status -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status Meča</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" id="status"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="scheduled" {{ old('status', $match->status) === 'scheduled' ? 'selected' : '' }}>Zakazani</option>
                                    <option value="in_progress" {{ old('status', $match->status) === 'in_progress' ? 'selected' : '' }}>Uživo</option>
                                    <option value="completed" {{ old('status', $match->status) === 'completed' ? 'selected' : '' }}>Završeno</option>
                                    <option value="cancelled" {{ old('status', $match->status) === 'cancelled' ? 'selected' : '' }}>Otkazano</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Forfeit Option -->
                            <div class="flex items-center">
                                <input type="checkbox" name="forfeited" id="forfeited" value="1"
                                       {{ old('forfeited', $match->forfeited_by ? true : false) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="forfeited" class="ml-2 block text-sm text-gray-900">
                                    Meč je bio otkazan
                                </label>
                            </div>

                            <div id="forfeit-details" class="{{ old('forfeited', $match->forfeited_by ? true : false) ? '' : 'hidden' }}">
                                <label for="forfeited_by" class="block text-sm font-medium text-gray-700 mb-2">Otkazao</label>
                                <select name="forfeited_by" id="forfeited_by"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Odaberi tim</option>
                                    <option value="home" {{ old('forfeited_by', $match->forfeited_by) === 'home' ? 'selected' : '' }}>
                                        {{ $match->homeTeam?->name ?? 'Domaći Tim' }}
                                    </option>
                                    <option value="away" {{ old('forfeited_by', $match->forfeited_by) === 'away' ? 'selected' : '' }}>
                                        {{ $match->awayTeam?->name ?? 'Gostujući Tim' }}
                                    </option>
                                </select>
                                @error('forfeited_by')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Scheduled Time -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Raspored</h3>
                        <div>
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">Zakazani Datum i Vrijeme</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                                   value="{{ old('scheduled_at', $match->scheduled_at ? $match->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('scheduled_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('referee.match.show', [$league, $match]) }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Otkaži
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Ažuriraj Meč
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('forfeited').addEventListener('change', function() {
    const forfeitDetails = document.getElementById('forfeit-details');
    if (this.checked) {
        forfeitDetails.classList.remove('hidden');
    } else {
        forfeitDetails.classList.add('hidden');
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const forfeitedCheckbox = document.getElementById('forfeited');
    const forfeitDetails = document.getElementById('forfeit-details');
    if (forfeitedCheckbox.checked) {
        forfeitDetails.classList.remove('hidden');
    }
});
</script>
@endsection