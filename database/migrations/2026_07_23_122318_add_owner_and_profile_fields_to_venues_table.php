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
        Schema::table('venues', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->string('slug')->nullable()->unique()->after('name');
            $table->text('description')->nullable()->after('address');
            $table->string('logo')->nullable()->after('description');
            $table->string('logo_url')->nullable()->after('logo');
            $table->string('contact_email')->nullable()->after('logo_url');
            $table->string('phone')->nullable()->after('contact_email');
            $table->string('website')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['slug', 'description', 'logo', 'logo_url', 'contact_email', 'phone', 'website']);
        });
    }
};
