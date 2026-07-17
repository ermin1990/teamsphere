{{-- Quick Actions Bar --}}
@if($isOwner)
<div class="mb-6 flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
    <a href="{{ route('organizations.competitions.manage-players', [$organization, $competition]) }}"
       class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-semibold text-sm sm:text-base">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        Upravljaj Igračima
    </a>

    <a href="{{ route('organizations.competitions.settings', [$organization, $competition]) }}"
       class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-semibold text-sm sm:text-base">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        Postavke
    </a>

    <a href="{{ route('organizations.show', $organization) }}"
       class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-semibold text-sm sm:text-base">
        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Nazad na Organizaciju
    </a>

    @if($competition->status === 'draft')
    <form action="{{ route('organizations.competitions.destroy', [$organization, $competition]) }}"
          method="POST"
          onsubmit="return confirm('Da li ste sigurni da želite obrisati ovo takmičenje? Ova akcija se ne može poništiti.')"
          class="w-full sm:w-auto sm:ml-auto">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-semibold text-sm sm:text-base">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Obriši
        </button>
    </form>
    @else        {{-- Complete competition button --}}
        @if($competition->status === 'active')
            <form method="POST" action="{{ route('organizations.competitions.complete', [$organization, $competition]) }}" onsubmit="return confirm('Da li ste sigurni da želite završiti ovo takmičenje?');" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-semibold text-sm sm:text-base">
                    🏆 Završi takmičenje
                </button>
            </form>
        @endif
        {{-- Reset competition to factory defaults --}}
        <form method="POST" action="{{ route('organizations.competitions.reset', [$organization, $competition]) }}" class="w-full sm:w-auto sm:ml-auto" onsubmit="return confirm('Resetovati takmičenje na fabrička podešavanja? Svi mečevi, grupe i rezultati biće obrisani.');">
            @csrf
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-semibold text-sm sm:text-base">
                ⟲ Resetuj takmičenje
            </button>
        </form>
    @endif
</div>
@endif
