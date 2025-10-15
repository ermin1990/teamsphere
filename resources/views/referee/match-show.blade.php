@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Match Details</h1>
                        <p class="mt-1 text-sm text-gray-500">{{ $league->name }} • {{ $league->sport->name }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('referee.league.matches', $league) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back to Matches
                        </a>
                        @if($match->status !== 'completed')
                            <a href="{{ route('referee.match.edit', [$league, $match]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Result
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Match Score Card -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <div class="text-center">
                    <!-- Teams -->
                    <div class="flex items-center justify-center space-x-8 mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 mb-2">{{ $match->homeTeam?->name ?? 'TBD' }}</div>
                            <div class="text-4xl">{{ $league->sport->icon }}</div>
                        </div>

                        <div class="text-center">
                            <div class="text-6xl font-black text-gray-900 mb-2">
                                @if($match->status === 'completed' || $match->status === 'in_progress')
                                    {{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}
                                @else
                                    VS
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 uppercase tracking-wide">
                                @if($match->status === 'in_progress')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 animate-pulse">
                                        LIVE
                                    </span>
                                @elseif($match->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        FINISHED
                                    </span>
                                @elseif($match->status === 'cancelled')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        CANCELLED
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ strtoupper($match->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 mb-2">{{ $match->awayTeam?->name ?? 'TBD' }}</div>
                            <div class="text-4xl">{{ $league->sport->icon }}</div>
                        </div>
                    </div>

                    <!-- Match Info -->
                    <div class="border-t border-gray-200 pt-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Scheduled Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($match->scheduled_at)
                                        {{ $match->scheduled_at->format('l, F d, Y') }}<br>
                                        {{ $match->scheduled_at->format('H:i') }}
                                    @else
                                        Not scheduled
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">League</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $league->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sport</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $league->sport->name }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Organization</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $league->organization->name }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if($match->forfeited_by)
                        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Match Forfeited</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>This match was forfeited by the {{ $match->forfeited_by }} team.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Match Actions -->
        @if($match->status !== 'completed')
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="flex flex-wrap gap-3">
                        @if($match->status !== 'in_progress')
                            <form method="POST" action="{{ route('referee.match.update', [$league, $match]) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l.707.707A1 1 0 0012.414 11H13m-4 4h1.586a1 1 0 01.707.293l.707.707A1 1 0 0012.414 15H13m-4-4v4"></path>
                                    </svg>
                                    Start Live Match
                                </button>
                            </form>
                        @endif

                        @if($match->status === 'in_progress')
                            <form method="POST" action="{{ route('referee.match.update', [$league, $match]) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    End Match
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('referee.match.update', [$league, $match]) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel Match
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection