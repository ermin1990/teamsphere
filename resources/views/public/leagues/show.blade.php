@extends('layouts.public')

@section('title', $competition->name . ' - ' . $organization->name)

@section('content')
    <!-- Header -->
    <div class="backdrop-blur-xl rounded-2xl p-4 md:p-8 shadow-xl mb-8 border" style="background: var(--bg-card); border-color: var(--border-primary); box-shadow: 0 10px 25px var(--shadow-primary);">
        <!-- Mobile Layout -->
        <div class="block md:hidden">
            <div class="text-center">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2" style="font-family: 'Unbounded', ui-sans-serif, sans-serif; letter-spacing: -0.01em;">
                    {{ $competition->name }}
                </h1>
                <p class="text-sm mb-4" style="color: var(--text-tertiary);">
                    <a href="{{ route('public.leagues.organization', $organization) }}" class="hover:text-blue-400 transition-colors">
                        {{ $organization->name }}
                    </a>
                    • {{ $competition->sport->name }}
                </p>
                <div class="flex items-center justify-center gap-2">
                    <span class="px-3 py-1 text-xs rounded-full font-medium"
                         style="background: var(--accent-blue); color: var(--accent-blue-solid);">
                        @if($competition->status === 'completed')
                            Završeno
                        @elseif($competition->status === 'in_progress')
                            U tijeku
                        @else
                            Planirano
                        @endif
                    </span>
                    <span class="px-3 py-1 text-xs rounded-full font-medium"
                         style="background: var(--accent-purple); color: var(--accent-purple-solid);">
                        @if($competition->type === 'tournament')
                            Turnir
                        @else
                            Liga
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="hidden md:flex md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2" style="font-family: 'Unbounded', ui-sans-serif, sans-serif; letter-spacing: -0.01em;">
                    {{ $competition->name }}
                </h1>
                <p class="text-base" style="color: var(--text-tertiary);">
                    <a href="{{ route('public.leagues.organization', $organization) }}" class="hover:text-blue-400 transition-colors">
                        {{ $organization->name }}
                    </a>
                    • {{ $competition->sport->name }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                @if($competition->type === 'tournament')
                <a href="{{ route('public.leagues.tournament.pdf', $competition->slug) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors"
                   style="color: var(--accent-blue); background: var(--bg-tertiary); border: 1px solid var(--border-primary); display: none;">
                    📄 PDF Export
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
                <a href="{{ route('projector.display', ['ids' => $competition->id, 'resolution' => '1024x768', 'layout' => 'single']) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg transition-colors hover:opacity-80"
                   style="color: white; background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); border: 1px solid rgba(147, 51, 234, 0.3);">
                    📽️ Projektor (1024x768)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </a>
                @endif
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 text-sm rounded-full font-medium"
                         style="background: var(--accent-blue); color: var(--accent-blue-solid);">
                        @if($competition->status === 'completed')
                            Završeno
                        @elseif($competition->status === 'in_progress')
                            U tijeku
                        @else
                            Draft
                        @endif
                    </span>
                    <span class="px-3 py-1 text-sm rounded-full font-medium"
                         style="background: var(--accent-purple); color: var(--accent-purple-solid);">
                        @if($competition->type === 'tournament')
                            Turnir
                        @else
                            Liga
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

@include('public.leagues.partials.content')
@endsection
