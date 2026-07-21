<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * One-off public endpoint to (re)run TuzlanskaLigaSeeder on the VPS without
 * SSH access. Guarded by SEED_TOKEN (a GitHub Actions secret written into
 * .env on deploy - see config/services.php) instead of auth, since this is
 * meant to be hit directly from a browser. Safe to call repeatedly: the
 * seeder itself skips any league that already has matches.
 */
class SeedController extends Controller
{
    public function tuzlanskaLiga(Request $request)
    {
        $expected = config('services.seed_token');

        if (!$expected || !hash_equals($expected, (string) $request->query('token'))) {
            abort(403, 'Neispravan ili nedostajući token.');
        }

        Artisan::call('db:seed', ['--class' => 'TuzlanskaLigaSeeder', '--force' => true]);

        return response('<pre>' . e(Artisan::output()) . '</pre>');
    }
}
