@extends('layouts.admin')

@section('admin-content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                Postavke Sistema
            </h2>
            <p class="text-gray-400 mt-2">Konfiguracija i postavke MojTurnir aplikacije</p>
        </div>
    </div>

    <!-- System Settings -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- General Settings -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
            <div class="p-6 border-b border-gray-700/50">
                <h3 class="text-xl font-bold text-white">Opće Postavke</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Naziv Aplikacije</label>
                    <input type="text" value="{{ config('app.name') }}" class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email za podršku</label>
                    <input type="email" value="support@mojturnir.com" class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Vremenska zona</label>
                    <select class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="Europe/Sarajevo" selected>Europe/Sarajevo</option>
                        <option value="UTC">UTC</option>
                        <option value="Europe/Zagreb">Europe/Zagreb</option>
                    </select>
                </div>

                <button class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02]">
                    Sačuvaj Postavke
                </button>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
            <div class="p-6 border-b border-gray-700/50">
                <h3 class="text-xl font-bold text-white">Email Postavke</h3>
                <p class="text-gray-400 text-sm mt-1">SMTP server i lozinka se podešavaju direktno na serveru (.env) - ovdje se podešava samo ko šalje i ko prima.</p>
            </div>
            <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6 space-y-6">
                @csrf
                <div>
                    <label for="mail_from_name" class="block text-sm font-medium text-gray-300 mb-2">Ime pošiljaoca</label>
                    <input type="text" name="mail_from_name" id="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}" required
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('mail_from_name')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="mail_from_address" class="block text-sm font-medium text-gray-300 mb-2">Email adresa sa koje se šalje</label>
                    <input type="email" name="mail_from_address" id="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" required
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('mail_from_address')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="notification_email" class="block text-sm font-medium text-gray-300 mb-2">Email na koji stižu obavještenja</label>
                    <input type="email" name="notification_email" id="notification_email" value="{{ old('notification_email', $settings['notification_email']) }}"
                           placeholder="ostavi prazno da idu svim administratorima"
                           class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600/50 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-gray-500 text-xs mt-1">Nove registracije, prijave bugova i zahtjevi za veći plan se šalju ovdje.</p>
                    @error('notification_email')<p class="mt-1 text-sm text-red-400">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02]">
                    Sačuvaj Email Postavke
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl p-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Maintenance Settings -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Održavanje Sistema</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <h4 class="text-white font-semibold mb-2">Cache</h4>
                    <p class="text-gray-400 text-sm mb-4">Očisti cache aplikacije</p>
                    <button class="px-6 py-2 bg-orange-500/20 hover:bg-orange-500/30 text-orange-400 rounded-lg transition-colors border border-orange-500/30">
                        Očisti Cache
                    </button>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                        </svg>
                    </div>
                    <h4 class="text-white font-semibold mb-2">Baza Podataka</h4>
                    <p class="text-gray-400 text-sm mb-4">Backup baze podataka</p>
                    <button class="px-6 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-colors border border-blue-500/30">
                        Kreiraj Backup
                    </button>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h4 class="text-white font-semibold mb-2">Maintenance Mode</h4>
                    <p class="text-gray-400 text-sm mb-4">Aktiviraj režim održavanja</p>
                    <button class="px-6 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-colors border border-red-500/30">
                        Aktiviraj
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl border border-gray-700/50 shadow-xl">
        <div class="p-6 border-b border-gray-700/50">
            <h3 class="text-xl font-bold text-white">Informacije o Sistemu</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-2">
                        {{ app()->version() }}
                    </div>
                    <p class="text-gray-400">Laravel verzija</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent mb-2">
                        {{ PHP_VERSION }}
                    </div>
                    <p class="text-gray-400">PHP verzija</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold bg-gradient-to-r from-orange-400 to-red-400 bg-clip-text text-transparent mb-2">
                        SQLite
                    </div>
                    <p class="text-gray-400">Baza podataka</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">
                        {{ now()->format('H:i') }}
                    </div>
                    <p class="text-gray-400">Server vrijeme</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection