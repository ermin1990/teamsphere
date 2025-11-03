<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-white">
            Obrišite Nalog
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            Jednom kada obrišete svoj nalog, svi njegovi resursi i podaci će biti trajno obrisani. Prije brisanja naloga, molimo vas da preuzmete sve podatke ili informacije koje želite zadržati.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Obrišite Nalog</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-white">
                Da li ste sigurni da želite obrisati svoj nalog?
            </h2>

            <p class="mt-1 text-sm text-gray-400">
                Jednom kada obrišete svoj nalog, svi njegovi resursi i podaci će biti trajno obrisani. Molimo vas da unesete svoju lozinku da potvrdite da želite trajno obrisati svoj nalog.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Lozinka" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Lozinka"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Otkaži
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Obrišite Nalog
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
