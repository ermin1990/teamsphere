<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->json('placements')->nullable()->after('link_url');
        });

        // Carry each existing banner's single placement over as a one-item
        // array so nothing already configured stops showing.
        DB::table('banners')->get(['id', 'placement'])->each(function ($banner) {
            DB::table('banners')->where('id', $banner->id)->update([
                'placements' => json_encode([$banner->placement]),
            ]);
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('placement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('placement')->nullable()->after('link_url');
        });

        DB::table('banners')->get(['id', 'placements'])->each(function ($banner) {
            $placements = json_decode($banner->placements ?? '[]', true);
            DB::table('banners')->where('id', $banner->id)->update([
                'placement' => $placements[0] ?? null,
            ]);
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('placements');
        });
    }
};
