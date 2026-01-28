<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Banneri i Logo
                </h2>
                <p class="text-gray-400 mt-1">Dodajte Youtube, Facebook linkove ili vanjski Logo URL za {{ $organization->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('organizations.show', $organization) }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Nazad
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Logo URL Section -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6">Logo Link (Header)</h3>
                <form action="{{ route('organizations.update', $organization) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $organization->name }}">
                    <input type="hidden" name="slug" value="{{ $organization->slug }}">
                    <input type="hidden" name="description" value="{{ $organization->description }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">URL Loga (https://...)</label>
                            <input type="url" name="logo_url" value="{{ $organization->logo_url }}" placeholder="https://example.com/logo.png" class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <p class="text-xs text-gray-400 mt-1">Ovaj logo će se prikazivati u header-u na javnoj stranici</p>
                        </div>
                        <div>
                            <button type="submit" class="w-full h-[42px] bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-all duration-200">
                                Sačuvaj Logo Link
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Add New Link -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6">Dodaj Banner Link</h3>
                <form action="{{ route('organizations.links.store', $organization) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Naslov (npr. Naš YouTube Kanal)</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">URL Link (https://...)</label>
                        <input type="url" name="url" required class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-xl text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <button type="submit" class="w-full h-[42px] bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all duration-200">
                            Dodaj Link
                        </button>
                    </div>
                </form>
            </div>

            <!-- Existing Links -->
            <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-6">Trenutni Linkovi (Banneri)</h3>
                @if($links->count() > 0)
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($links as $link)
                            @php
                                $isYoutube = str_contains(strtolower($link->url), 'youtube');
                                $isFacebook = str_contains(strtolower($link->url), 'facebook');
                                $isInstagram = str_contains(strtolower($link->url), 'instagram');
                            @endphp
                            <div class="flex items-center justify-between p-5 bg-gray-700/30 border-2 border-gray-600/50 rounded-xl hover:border-gray-500 transition-all">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white shadow-lg"
                                         style="
                                            @if($isYoutube)
                                                background: linear-gradient(135deg, #FF0000, #CC0000);
                                            @elseif($isFacebook)
                                                background: linear-gradient(135deg, #1877F2, #0C63D4);
                                            @elseif($isInstagram)
                                                background: linear-gradient(135deg, #E4405F, #C13584);
                                            @else
                                                background: linear-gradient(135deg, #6366F1, #4F46E5);
                                            @endif
                                         ">
                                        @if($isYoutube)
                                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                        @elseif($isFacebook)
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                        @elseif($isInstagram)
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold text-base">{{ $link->title }}</h4>
                                        <p class="text-xs text-gray-400 truncate max-w-xs md:max-w-md mt-0.5">{{ $link->url }}</p>
                                    </div>
                                </div>
                                <form action="{{ route('organizations.links.destroy', [$organization, $link]) }}" method="POST" onsubmit="return confirm('Sigurno želite obrisati ovaj link?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-400 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400 italic">
                        Nema dodanih linkova za ovu organizaciju.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
