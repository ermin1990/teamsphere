<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogOrganizationAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $logFile = public_path('debug_organization.log');
        $timestamp = date('Y-m-d H:i:s');
        
        $logData = [
            'timestamp' => $timestamp,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'route_name' => $request->route() ? $request->route()->getName() : 'no route',
            'user_id' => auth()->id() ?? 'not authenticated',
            'user_email' => auth()->user()->email ?? 'not authenticated',
        ];
        
        file_put_contents($logFile, "=== MIDDLEWARE START ===\n" . print_r($logData, true) . "\n", FILE_APPEND);
        
        $response = $next($request);
        
        file_put_contents($logFile, "=== MIDDLEWARE END - Response Code: " . $response->getStatusCode() . " ===\n\n", FILE_APPEND);
        
        return $response;
    }
}
