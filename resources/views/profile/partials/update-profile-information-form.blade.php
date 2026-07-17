<section>
    <header>
        <h2 class="text-lg font-semibold" style="color: var(--text-primary);">
            Informacije o Profilu
        </h2>

        <p class="mt-1 text-sm" style="color: var(--text-tertiary);">
            Ažurirajte informacije o vašem profilu i email adresu.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="'Ime'" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="'Email'" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2" style="color: var(--text-secondary);">
                        Vaša email adresa nije verifikovana.

                        <button form="send-verification" class="underline text-sm focus:outline-none" style="color: var(--accent-blue);">
                            Kliknite ovdje da ponovo pošaljete email za verifikaciju.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm" style="color: #57f1db;">
                            Novi link za verifikaciju je poslan na vašu email adresu.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Spremi</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm"
                    style="color: #57f1db;"
                >Spremljeno.</p>
            @endif
        </div>
    </form>
</section>
