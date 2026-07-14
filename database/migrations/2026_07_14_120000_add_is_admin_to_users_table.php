<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        // Postojeci vlasnik/administrator aplikacije - jedina osoba kojoj je admin panel
        // do sada bio namijenjen (provjeravano hardkodovanim email-om po view-ovima).
        DB::table('users')
            ->where('email', 'ermin1990@gmail.com')
            ->update(['is_admin' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
