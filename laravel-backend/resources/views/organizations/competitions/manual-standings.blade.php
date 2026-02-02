@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('organizations.competitions.show', [$organization, $competition]) }}" 
               class="inline-flex items-center text-blue-400 hover:text-blue-300 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Nazad na takmičenje
            </a>
        </div>

        <livewire:manual-standings-adjustment :competition="$competition" :group="$group" />
    </div>
</div>
@endsection