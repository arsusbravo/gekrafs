<?php

namespace App\Http\Middleware;

use App\Support\Localization;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Public pages take their language from the URL (/nl/..., /id/..., English at the root).
     * Other pages (login) use the language of the last public page visited.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Localization::localeOf($request->route());

        if ($locale !== null) {
            $request->session()->put('locale', $locale);
        } else {
            $locale = $request->session()->get('locale');
        }

        if (! Localization::supports($locale)) {
            $locale = Localization::default();
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
