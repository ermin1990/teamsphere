<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Display Admin - Selekcija Turnira
                </h2>
                <p class="text-gray-400 mt-1">Odaberite turnire koje želite prikazati na display ekranu</p>
            </div>
            <a href="{{ route('display.show') }}" target="_blank" class="bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg hover:shadow-green-500/25">
                <span class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>Otvori Display</span>
                </span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($leagues->count() > 0)
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-white mb-2">Aktivni Turniri sa Live Mečevima</h3>
                        <p class="text-gray-400 text-sm">Kliknite na turnir da ga dodate ili uklonite sa displaya</p>
                    </div>

                    <div class="space-y-4">
                        @foreach($leagues as $league)
                            <div class="bg-gray-700/30 rounded-xl p-6 hover:bg-gray-600/30 transition-all duration-200 border border-gray-600/20 hover:border-gray-500/30">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4 flex-1">
                                        <div class="text-4xl">{{ $league->sport->icon }}</div>
                                        <div class="flex-1">
                                            <h4 class="text-white font-bold text-lg">{{ $league->name }}</h4>
                                            <p class="text-gray-400 text-sm">{{ $league->organization->name }}</p>
                                            <div class="flex items-center space-x-2 mt-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                                    {{ $league->live_matches_count }} LIVE {{ $league->live_matches_count == 1 ? 'Meč' : 'Meča' }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $league->sport->name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button 
                                            onclick="toggleLeague({{ $league->id }}, this)"
                                            data-league-id="{{ $league->id }}"
                                            class="toggle-btn px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] {{ in_array($league->id, $selectedLeagueIds) ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}">
                                            <span class="flex items-center space-x-2">
                                                @if(in_array($league->id, $selectedLeagueIds))
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    <span>Ukloni</span>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    <span>Dodaj</span>
                                                @endif
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-12 border border-gray-700/50 shadow-xl text-center">
                    <div class="w-20 h-20 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white mb-3">Nema Aktivnih Turnira</h4>
                    <p class="text-gray-400">Trenutno nema turnira sa live mečevima.</p>
                </div>
            @endif

        </div>
    </div>

    <script>
        function toggleLeague(leagueId, button) {
            fetch(`/display/toggle/${leagueId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button
                    if (data.action === 'added') {
                        button.className = 'toggle-btn px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] bg-red-600 hover:bg-red-700 text-white';
                        button.innerHTML = `
                            <span class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>Ukloni</span>
                            </span>
                        `;
                    } else {
                        button.className = 'toggle-btn px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-[1.02] bg-green-600 hover:bg-green-700 text-white';
                        button.innerHTML = `
                            <span class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Dodaj</span>
                            </span>
                        `;
                    }

                    // Show notification
                    showNotification(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Greška prilikom ažuriranja', 'error');
            });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-lg z-50 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'} text-white`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</x-app-layout>
