<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

/**
 * One-time setup of "Tuzlanska liga" and all its 2026/02 leagues. Safe to
 * run on every deploy (called unconditionally from entrypoint.sh) - it
 * no-ops once the organization already exists, since re-running the
 * individual league seeders would duplicate every match row (LeagueMatch
 * rows aren't created with firstOrCreate, unlike the organization/season/
 * league/player records).
 */
class TuzlanskaLigaSeeder extends Seeder
{
    public function run(): void
    {
        if (Organization::where('name', 'Tuzlanska liga')->exists()) {
            $this->command?->info('Tuzlanska liga već postoji - preskačem inicijalno postavljanje.');
            return;
        }

        $this->call([
            LeagueSeason202602Seeder::class,
            League2ALigaSeason202602Seeder::class,
            League2BLigaSeason202602Seeder::class,
            League3ALigaSeason202602Seeder::class,
            League3BLigaSeason202602Seeder::class,
            League3CLigaSeason202602Seeder::class,
            League3DLigaSeason202602Seeder::class,
            League4ALigaSeason202602Seeder::class,
            League4BLigaSeason202602Seeder::class,
            League4CLigaSeason202602Seeder::class,
        ]);
    }
}
