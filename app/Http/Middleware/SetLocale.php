<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (! array_key_exists($locale ?? '', config('app.locales'))) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
