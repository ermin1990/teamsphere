<x-app-layout>

    @include('organizations.competitions.partials.header')

    @if(session('success'))
        @include('organizations.competitions.partials.success-message')
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

</x-app-layout>
