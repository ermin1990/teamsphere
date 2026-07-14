<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for language parameter in URL
        if ($request->has('lang')) {
            $locale = $request->get('lang');

            // Validate locale
            if (in_array($locale, ['en', 'bs'])) {
                Session::put('locale', $locale);
                App::setLocale($locale);

                // Remove lang parameter from URL and redirect
                if ($request->get('lang')) {
                    $query = $request->query();
                    unset($query['lang']);
                    $url = $request->url();

                    if (!empty($query)) {
                        $url .= '?' . http_build_query($query);
                    }

                    return redirect($url);
                }
            }
        }

        // Set locale from session or default to Bosnian - resources/lang/en does not
        // exist, so defaulting to 'en' left every __('messages....') call unresolved
        // (printing the raw key) for any visitor who never explicitly picked a language.
        $locale = Session::get('locale', 'bs');

        // Ensure locale is valid
        if (!in_array($locale, ['en', 'bs'])) {
            $locale = 'bs';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
