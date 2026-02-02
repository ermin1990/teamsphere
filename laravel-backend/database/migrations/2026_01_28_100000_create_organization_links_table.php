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
        Schema::create('organization_links', function (Blueprint $col) {
            $col->id();
            $col->foreignId('organization_id')->constrained()->onDelete('cascade');
            $col->string('title');
            $col->string('url');
            $col->string('type')->nullable(); // e.g., 'youtube', 'facebook', 'other'
            $col->integer('sort_order')->default(0);
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_links');
    }
};
