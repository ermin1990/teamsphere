<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates the public API reference page (/api) behind a single shared
 * password via HTTP Basic Auth - this is a docs page for an external
 * developer, not tied to a real user account, so a full login isn't
 * warranted, just a browser-native password prompt.
 */
class ApiDocsPasswordGate
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.api_docs.password');

        if (!$expected) {
            abort(500, 'API docs password is not configured.');
        }

        if (hash_equals($expected, (string) $request->getPassword())) {
            return $next($request);
        }

        return response('Unauthorized', 401, [
            'WWW-Authenticate' => 'Basic realm="MojTurnir API Docs"',
        ]);
    }
}
