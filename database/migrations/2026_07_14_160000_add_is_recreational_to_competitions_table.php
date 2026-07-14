<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Rekreativna liga" - opusteniji rezim za drustvo koje igra radi zabave:
     * dozvoljava rucno dodavanje ponovljenih mecceva (isti par vise puta) i
     * ranije zavrsavanje meca/seta bez ispunjenog standardnog uslova pobjede.
     * Namjerno odvojeno od standardnih liga da se ne pojavi kao opcija tamo
     * gdje bi samo zbunila organizatora.
     */
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->boolean('is_recreational')->default(false)->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('is_recreational');
        });
    }
};
