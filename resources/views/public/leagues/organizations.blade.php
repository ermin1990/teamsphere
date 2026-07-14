@extends('layouts.public')

@section('title', 'Organizacije - MojTurnir')

@section('content')
    <!-- Header -->
    <div class="backdrop-blur-xl rounded-2xl p-4 md:p-6 shadow-xl mb-6 md:mb-8 border" style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 25px var(--shadow-primary);">
        <h1 class="text-2xl md:text-3xl font-bold text-center bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
            🏢 Organizacije i Savezi
        </h1>
        <p class="text-center mt-2 text-sm md:text-base" style="color: var(--text-tertiary);">Izaberite organizaciju za pregled aktivnih liga i turnira</p>
    </div>

    @if($organizations->count() > 0)
        <!-- Organizations Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
            @foreach($organizations as $organization)
            <a href="{{ route('public.leagues.organization', $organization) }}"
               class="backdrop-blur-xl rounded-xl p-5 md:p-6 border transition-all duration-300 hover:scale-[1.03] hover:shadow-2xl group flex flex-col items-center text-center" 
               style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 20px var(--shadow-primary);">
                
                <!-- Logo / Icon -->
                <div class="mb-4 relative">
                    @if($organization->logo)
                        <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}" class="w-20 h-20 md:w-24 md:h-24 rounded-2xl object-cover shadow-lg border-2 border-transparent group-hover:border-blue-400 transition-all duration-300">
                    @else
                        <div class="w-20 h-20 md:w-24 md:h-24 bg-gradient-to-br from-blue-500/20 to-purple-600/20 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-600 group-hover:border-blue-400 transition-all duration-300">
                            <span class="text-3xl md:text-4xl">🏢</span>
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1">
                    <h2 class="text-lg md:text-xl font-bold group-hover:text-blue-400 transition-colors mb-2" style="color: var(--text-primary);">
                        {{ $organization->name }}
                    </h2>
                    @if($organization->description)
                        <p class="text-xs md:text-sm line-clamp-2 mb-3 px-2" style="color: var(--text-tertiary);">
                            {{ $organization->description }}
                        </p>
                    @endif
                </div>

                <!-- Footer Stats -->
                <div class="mt-4 pt-4 border-t w-full flex justify-between items-center" style="border-color: var(--border-secondary);">
                    <div class="flex items-center text-xs" style="color: var(--text-muted);">
                        <span class="mr-1">🏆</span>
                        {{ $organization->competitions_count }} Takmičenja
                    </div>
                    <span class="text-xs font-semibold group-hover:translate-x-1 transition-transform" style="color: var(--accent-blue);">
                        Pogledaj →
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 backdrop-blur-xl rounded-2xl border" style="background: var(--bg-card); border-color: var(--border-primary);">
            <div class="text-6xl mb-6">🏢</div>
            <h2 class="text-2xl font-bold mb-3" style="color: var(--text-tertiary);">Trenutno nema dostupnih organizacija</h2>
            <p class="max-w-md mx-auto" style="color: var(--text-muted);">Uskoro ćemo imati nove saveze i organizatore koji objavljuju svoja takmičenja.</p>
        </div>
    @endif

    <!-- Footer Info -->
    <div class="mt-12 text-center">
        <div class="inline-block px-4 py-2 rounded-full border text-xs" style="background: var(--bg-card); border-color: var(--border-primary); color: var(--text-tertiary);">
            Želite li dodati vašu ligu? <a href="{{ route('register') }}" class="font-bold hover:text-blue-400 transition-colors" style="color: var(--accent-blue);">Registrujte se ovdje</a>
        </div>
    </div>
@endsection
