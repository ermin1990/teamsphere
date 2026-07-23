<?php

use App\Models\Sport;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * A Padel "team" is just a pair of players playing one match together
     * (sets/games, exactly like Tennis) - not a table-tennis-style tie made
     * of several individual singles/doubles games. This flips the existing
     * Padel sport row (added before self-service registration existed, so a
     * fresh seeder run won't touch it) to the new single-match tie format.
     */
    public function up(): void
    {
        Sport::where('slug', 'padel')->get()->each(function (Sport $sport) {
            $rules = $sport->rules ?? [];
            $rules['team_tie_format'] = 'single_match';
            $sport->update(['rules' => $rules]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Sport::where('slug', 'padel')->get()->each(function (Sport $sport) {
            $rules = $sport->rules ?? [];
            unset($rules['team_tie_format']);
            $sport->update(['rules' => $rules]);
        });
    }
};
