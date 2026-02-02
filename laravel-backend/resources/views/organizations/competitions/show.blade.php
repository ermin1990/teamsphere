<x-app-layout>

    @include('organizations.competitions.partials.header')

    @if(session('success'))
        @include('organizations.competitions.partials.success-message')
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
            <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                <p class="text-red-400">❌ {{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @include('organizations.competitions.partials.actions-bar')

            <div class="space-y-6">
                @include('organizations.competitions.partials.info-cards')
                
                @include('organizations.competitions.partials.setup-wizard')
                
                @if($competition->type === 'tournament')
                    @include('organizations.competitions.partials.tournament-content')
                @else
                    @include('organizations.competitions.partials.league-content')
                @endif

                @include('organizations.competitions.partials.match-rules')
            </div>

        </div>
    </div>

    @include('organizations.competitions.partials.modals')
    @include('organizations.competitions.partials.scripts')

    @if(session('scroll_position'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.scrollTo(0, {{ session('scroll_position') }});
            });
        </script>
        @php
            // Clear scroll position after using it so it doesn't persist
            session()->forget('scroll_position');
        @endphp
    @endif

</x-app-layout>
