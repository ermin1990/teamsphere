<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\Organization;
use Illuminate\Database\Seeder;

/**
 * One-time setup of "Tuzlanska liga" and all its 2026/02 leagues. Safe to
 * run on every deploy (called unconditionally from entrypoint.sh):
 * idempotency is checked PER LEAGUE (does it already have matches?), not
 * for the organization as a whole - re-running a sub-seeder for a league
 * that already has matches would duplicate every match row (LeagueMatch
 * rows aren't created with firstOrCreate), so each one is skipped once
 * it's actually done, and a failure in one league no longer blocks the
 * rest of the batch from running on the next deploy.
 */
class TuzlanskaLigaSeeder extends Seeder
{
    private const LEAGUE_SEEDERS = [
        '1. Liga - Saša Vilušić' => LeagueSeason202602Seeder::class,
        '2A. Liga' => League2ALigaSeason202602Seeder::class,
        '2B. Liga' => League2BLigaSeason202602Seeder::class,
        '3A. Liga' => League3ALigaSeason202602Seeder::class,
        '3B. Liga' => League3BLigaSeason202602Seeder::class,
        '3C. Liga' => League3CLigaSeason202602Seeder::class,
        '3D. Liga' => League3DLigaSeason202602Seeder::class,
        '4A. Liga' => League4ALigaSeason202602Seeder::class,
        '4B. Liga' => League4BLigaSeason202602Seeder::class,
        '4C. Liga' => League4CLigaSeason202602Seeder::class,
    ];

    public function run(): void
    {
        $organization = Organization::where('name', 'Tuzlanska liga')->first();

        foreach (self::LEAGUE_SEEDERS as $leagueName => $seederClass) {
            $league = $organization
                ? League::where('organization_id', $organization->id)->where('name', $leagueName)->first()
                : null;

            if ($league && $league->matches()->exists()) {
                $this->command?->info("✓ {$leagueName} već postoji sa mečevima - preskačem.");
                continue;
            }

            try {
                $this->call($seederClass);
            } catch (\Throwable $e) {
                $this->command?->error("✗ Greška pri seedanju {$leagueName}: " . $e->getMessage());
                \Log::error("TuzlanskaLigaSeeder: greška pri seedanju {$leagueName}", [
                    'seeder' => $seederClass,
                    'exception' => $e,
                ]);
                // Nastavi sa sljedećom ligom - jedna greška ne treba da blokira ostatak.
            }

            // Organizacija/sezona su kreirane pri prvom pozivu - dohvati je
            // ponovo za sljedeću iteraciju ako još nije bila postavljena.
            $organization ??= Organization::where('name', 'Tuzlanska liga')->first();
        }
    }
}
