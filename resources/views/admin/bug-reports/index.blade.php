@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Bug Reports & Feature Requests
            </h2>
            <p class="text-gray-400 mt-2">Manage user feedback and reports</p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-400 text-sm">{{ __('Total Reports') }}</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-400 text-sm">{{ __('Pending') }}</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-400 text-sm">{{ __('In Review') }}</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['in_review'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
                <div class="flex items-center">
                    <div class="p-3 bg-green-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-400 text-sm">{{ __('Resolved') }}</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['resolved'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 overflow-hidden">
            <div class="px-6 py-4 border-b border-white/10">
                <h3 class="text-lg font-semibold text-white">{{ __('All Reports') }}</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Type') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Subject') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('User') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($reports as $report)
                            <tr class="hover:bg-white/5">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($report->type === 'bug')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                                            🐛 {{ __('Bug') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400">
                                            💡 {{ __('Feature') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-white">{{ Str::limit($report->subject, 50) }}</div>
                                    <div class="text-sm text-gray-400">{{ Str::limit($report->description, 80) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-white">{{ $report->user ? $report->user->name : ($report->name ?: 'Anonymous') }}</div>
                                    @if($report->email)
                                        <div class="text-sm text-gray-400">{{ $report->email }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($report->status)
                                        @case('pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400">
                                                {{ __('Pending') }}
                                            </span>
                                            @break
                                        @case('in_review')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-500/20 text-purple-400">
                                                {{ __('In Review') }}
                                            </span>
                                            @break
                                        @case('resolved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                                {{ __('Resolved') }}
                                            </span>
                                            @break
                                        @case('closed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">
                                                {{ __('Closed') }}
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $report->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.bug-reports.show', $report) }}" class="text-blue-400 hover:text-blue-300">
                                        {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="mt-2 text-sm">{{ __('No bug reports yet.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($reports->hasPages())
                <div class="px-6 py-4 border-t border-white/10">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection