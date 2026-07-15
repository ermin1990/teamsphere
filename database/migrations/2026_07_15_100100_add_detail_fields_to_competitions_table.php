<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->string('location')->nullable()->after('description');
            $table->string('organizer_contact')->nullable()->after('location');
            $table->string('entry_fee')->nullable()->after('organizer_contact');
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn(['location', 'organizer_contact', 'entry_fee']);
        });
    }
};
