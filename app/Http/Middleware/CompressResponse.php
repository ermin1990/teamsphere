<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Compress response if client supports gzip
        if ($this->shouldCompress($request, $response)) {
            $response->setContent(gzencode($response->getContent(), 9));
            $response->headers->set('Content-Encoding', 'gzip');
            $response->headers->set('Vary', 'Accept-Encoding');
        }

        return $response;
    }

    /**
     * Determine if response should be compressed.
     */
    private function shouldCompress(Request $request, Response $response): bool
    {
        // Only compress if client accepts gzip
        $acceptsGzip = strpos($request->header('Accept-Encoding', ''), 'gzip') !== false;

        // Only compress text-based responses
        $compressibleTypes = [
            'text/html',
            'text/plain',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'application/xml',
            'text/xml',
        ];

        $contentType = $response->headers->get('Content-Type', '');
        $isCompressible = false;

        foreach ($compressibleTypes as $type) {
            if (strpos($contentType, $type) === 0) {
                $isCompressible = true;
                break;
            }
        }

        // Don't compress if already compressed or too small
        $contentLength = strlen($response->getContent());
        $isSmall = $contentLength < 1024; // Don't compress under 1KB

        return $acceptsGzip && $isCompressible && !$isSmall;
    }
}
