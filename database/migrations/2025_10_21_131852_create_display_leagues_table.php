<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('display_leagues', function (Blueprint $table) {
            $table->id();
            // create as unsignedBigInteger first, add FK later if target table exists
            $table->unsignedBigInteger('league_id');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // Add foreign key only if target table exists. Prefer 'leagues', fallback to 'competitions'.
        if (Schema::hasTable('leagues')) {
            Schema::table('display_leagues', function (Blueprint $table) {
                $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
            });
        } elseif (Schema::hasTable('competitions')) {
            Schema::table('display_leagues', function (Blueprint $table) {
                $table->foreign('league_id')->references('id')->on('competitions')->onDelete('cascade');
            });
        } else {
            // No target table present; leave column without FK to avoid migration failure.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('display_leagues')) {
            try {
                Schema::table('display_leagues', function (Blueprint $table) {
                    $table->dropForeign(['league_id']);
                });
            } catch (\Exception $e) {
                // ignore if foreign key doesn't exist
            }
            Schema::dropIfExists('display_leagues');
        }
    }
};
