<?php

use App\Models\Venue;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Backfill slugs for venues created before self-service registration
     * existed (admin-created venues never had a slug) - needed so they get
     * a public /tereni/{slug} page too.
     */
    public function up(): void
    {
        Venue::whereNull('slug')->orderBy('id')->each(function (Venue $venue) {
            $base = Str::slug($venue->name) ?: 'teren-' . $venue->id;
            $slug = $base;
            $i = 1;

            while (Venue::where('slug', $slug)->where('id', '!=', $venue->id)->exists()) {
                $slug = $base . '-' . (++$i);
            }

            $venue->update(['slug' => $slug]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
