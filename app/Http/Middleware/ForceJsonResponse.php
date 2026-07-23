<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Makes every /api/* request "expect JSON" regardless of what Accept header
 * the client sent - otherwise Laravel's default exception handling (a
 * ValidationException redirects back with flashed errors, an
 * AuthenticationException redirects to /login, a 404 renders the HTML error
 * page) kicks in for any client that didn't explicitly send
 * Accept: application/json, silently swallowing the real error behind a
 * redirect that gets auto-followed (e.g. Postman/curl -L) to some unrelated
 * 200 HTML page.
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
