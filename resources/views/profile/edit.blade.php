<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl sm:text-3xl bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent" style="font-family: 'Unbounded', ui-sans-serif, sans-serif; letter-spacing: -0.01em;">
            Profil
        </h2>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            <div class="p-5 sm:p-8 rounded-2xl backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="p-5 sm:p-8 rounded-2xl backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                @include('profile.partials.update-password-form')
            </div>

            <div class="p-5 sm:p-8 rounded-2xl backdrop-blur-xl" style="background: var(--bg-card); border: 1px solid var(--border-primary); box-shadow: 0 10px 30px var(--shadow-primary);">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
