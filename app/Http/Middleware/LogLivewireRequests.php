<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogLivewireRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('livewire/*')) {
            Log::info('Livewire Request', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
                'body' => $request->all(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ]);
        }

        $response = $next($request);

        if ($request->is('livewire/*')) {
            Log::info('Livewire Response', [
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
            ]);
        }

        return $response;
    }
}
