@if($isOwner && isset($pendingJoinRequests) && $pendingJoinRequests->count() > 0)
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-yellow-500/30 shadow-xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-yellow-500/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-white">Zahtjevi za pridruživanje</h3>
                <p class="text-gray-400 text-sm">{{ $pendingJoinRequests->count() }} {{ $pendingJoinRequests->count() == 1 ? 'igrač čeka' : 'igrača čeka' }} tvoje odobrenje</p>
            </div>
        </div>

        <div class="space-y-3">
            @foreach($pendingJoinRequests as $joinRequest)
                <div class="flex items-center justify-between bg-gray-900/50 rounded-xl p-3 gap-3 border border-gray-700/30">
                    <div class="min-w-0 flex-1">
                        <p class="text-white font-medium truncate">{{ $joinRequest->user->name }}</p>
                        <p class="text-gray-400 text-xs truncate">{{ $joinRequest->user->email }}</p>
                        @if($joinRequest->message)
                            <p class="text-gray-500 text-xs mt-1 italic">"{{ $joinRequest->message }}"</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form method="POST" action="{{ route('organizations.competitions.join-requests.reject', [$organization, $competition, $joinRequest]) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-white text-xs font-medium rounded-lg transition-colors">
                                Odbij
                            </button>
                        </form>
                        <form method="POST" action="{{ route('organizations.competitions.join-requests.approve', [$organization, $competition, $joinRequest]) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition-colors">
                                Prihvati
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
