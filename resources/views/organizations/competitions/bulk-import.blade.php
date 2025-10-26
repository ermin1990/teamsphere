@extends('layouts.app')

@section('title', __('Bulk Import Players'))

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $competition->name }}</h1>
                    <p class="text-gray-400 mt-2">{{ __('Bulk Import Players') }}</p>
                </div>
                <a href="{{ route('organizations.competitions.manage-players', [$organization, $competition]) }}"
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← {{ __('Back to Manage Players') }}
                </a>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-6 border border-gray-700/50 shadow-xl mb-6">
            <h2 class="text-xl font-semibold text-white mb-4">{{ __('Import Instructions') }}</h2>
            <div class="space-y-4">
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                    <h3 class="text-blue-400 font-medium mb-2">{{ __('Format Requirements') }}</h3>
                    <p class="text-gray-300 text-sm mb-2">{{ __('Enter each player on a new line in the format:') }}</p>
                    <code class="bg-gray-700 px-3 py-2 rounded text-sm text-gray-200 block font-mono">{{ __('Name Surname, Club Name;') }}</code>
                    <div class="mt-3 space-y-1">
                        <p class="text-gray-400 text-xs">{{ __('Examples:') }}</p>
                        <code class="bg-gray-700 px-2 py-1 rounded text-xs text-gray-200 block">{{ __('John Doe, Tennis Club A;') }}</code>
                        <code class="bg-gray-700 px-2 py-1 rounded text-xs text-gray-200 block">{{ __('Jane Smith, Table Tennis Club B;') }}</code>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                        <h4 class="text-green-400 font-medium mb-2">{{ __('What happens with existing players?') }}</h4>
                        <p class="text-gray-300 text-xs">{{ __('If a player with the same name already exists in your organization, they will be added to the competition. If they are already in the competition, they will be skipped.') }}</p>
                    </div>

                    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4">
                        <h4 class="text-yellow-400 font-medium mb-2">{{ __('What happens with new players?') }}</h4>
                        <p class="text-gray-300 text-xs">{{ __('New players will be created in your organization and automatically added to this competition.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Form -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-6 border border-gray-700/50 shadow-xl">
            <form method="POST" action="{{ route('organizations.competitions.bulk-import-players', [$organization, $competition]) }}">
                @csrf

                <div class="mb-6">
                    <label for="players_text" class="block text-sm font-medium text-white mb-2">
                        {{ __('Player Data') }}
                        <span class="text-gray-400 text-xs">({{ __('one player per line') }})</span>
                    </label>
                    <textarea name="players_text"
                              id="players_text"
                              rows="12"
                              required
                              class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm resize-vertical"
                              placeholder="{{ __('John Doe, Tennis Club A;') . PHP_EOL . __('Jane Smith, Table Tennis Club B;') . PHP_EOL . __('Bob Johnson, Badminton Club C;') }}"></textarea>
                    @error('players_text')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview Section (if we have data) -->
                @if(old('players_text'))
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-white mb-3">{{ __('Preview') }}</h3>
                        <div class="bg-gray-700/30 rounded-lg p-4 max-h-64 overflow-y-auto">
                            @php
                                $previewData = [];
                                $errors = [];
                                $lines = explode("\n", old('players_text'));

                                foreach ($lines as $lineNumber => $line) {
                                    $line = trim($line);
                                    if (empty($line)) continue;

                                    if (!str_ends_with($line, ';')) {
                                        $errors[] = "Linija " . ($lineNumber + 1) . ": Mora završavati sa ';'";
                                        continue;
                                    }

                                    $line = rtrim($line, ';');
                                    $parts = explode(',', $line, 2);

                                    if (count($parts) !== 2) {
                                        $errors[] = "Linija " . ($lineNumber + 1) . ": Format mora biti 'Ime i prezime, Klub'";
                                        continue;
                                    }

                                    $name = trim($parts[0]);
                                    $club = trim($parts[1]);

                                    if (empty($name)) {
                                        $errors[] = "Linija " . ($lineNumber + 1) . ": Ime igrača je obavezno";
                                        continue;
                                    }

                                    // Check if player exists
                                    $existingPlayer = $organization->players()->where('name', $name)->first();
                                    $status = 'new';
                                    if ($existingPlayer) {
                                        if ($competition->players->contains($existingPlayer->id)) {
                                            $status = 'exists_in_competition';
                                        } else {
                                            $status = 'exists_in_org';
                                        }
                                    }

                                    $previewData[] = [
                                        'name' => $name,
                                        'club' => $club,
                                        'status' => $status,
                                        'line' => $lineNumber + 1
                                    ];
                                }
                            @endphp

                            @if(!empty($errors))
                                <div class="mb-4 p-3 bg-red-500/20 border border-red-500/30 rounded">
                                    <h4 class="text-red-400 font-medium mb-2">{{ __('Errors Found') }}</h4>
                                    <ul class="text-red-300 text-sm space-y-1">
                                        @foreach($errors as $error)
                                            <li>• {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(!empty($previewData))
                                <div class="space-y-2">
                                    <p class="text-gray-300 text-sm mb-3">{{ __('Players to be imported') }} ({{ count($previewData) }}):</p>
                                    @foreach($previewData as $player)
                                        <div class="flex items-center justify-between p-2 rounded {{ isset($player['error']) ? 'bg-red-500/10 border border-red-500/20' : 'bg-gray-600/30' }}">
                                            <div class="flex-1">
                                                <div class="text-white text-sm">{{ $player['name'] }}</div>
                                                <div class="text-gray-400 text-xs">{{ $player['club'] }}</div>
                                            </div>
                                            <div class="text-xs">
                                                @if($player['status'] === 'new')
                                                    <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded">{{ __('New') }}</span>
                                                @elseif($player['status'] === 'exists_in_org')
                                                    <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded">{{ __('Existing') }}</span>
                                                @elseif($player['status'] === 'exists_in_competition')
                                                    <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded">{{ __('Already Added') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-400">
                        @if($competition->max_participants)
                            {{ __('Max participants') }}: {{ $competition->max_participants }}
                        @else
                            {{ __('No participant limit') }}
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('organizations.competitions.manage-players', [$organization, $competition]) }}"
                           class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __('Import Players') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="mt-6 p-4 bg-green-500/20 border border-green-500/50 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-green-400">{!! session('success') !!}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-400">{!! session('error') !!}</span>
                </div>
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="mt-6 p-4 bg-yellow-500/20 border border-yellow-500/50 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-yellow-400">{!! session('warning') !!}</span>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection