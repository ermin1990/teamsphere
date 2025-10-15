@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Upravljanje Planovima
            </h2>
            <p class="text-gray-400 mt-2">Pregled i upravljanje pretplatničkim planovima</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-400">Ukupno planova</p>
            <p class="text-2xl font-bold text-white">{{ $plans->total() }}</p>
        </div>
    </div>

    <!-- Plans List -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Lista Planova</h3>
        </div>

        <div class="divide-y divide-gray-700/50">
            @foreach($plans as $plan)
            <div class="p-6 hover:bg-gray-700/20 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ substr($plan->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold">{{ $plan->name }}</h4>
                            <p class="text-gray-400 text-sm">{{ Str::limit($plan->description, 80) }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                        <div class="flex space-x-6 sm:space-x-0 sm:flex-col sm:items-center">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-white">{{ $plan->price }} {{ $plan->currency }}</p>
                                <p class="text-gray-400 text-xs">Cijena</p>
                            </div>

                            <div class="text-center">
                                <p class="text-2xl font-bold text-white">{{ $plan->userPlans->count() }}</p>
                                <p class="text-gray-400 text-xs">Korisnika</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                            <div class="text-center">
                                @if($plan->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                        Aktivan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                                        Neaktivan
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('admin.plans.show', $plan) }}" class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-colors border border-blue-500/30 text-center">
                                Pregledaj
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($plans->hasPages())
        <div class="p-6 border-t border-gray-700/50">
            {{ $plans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection