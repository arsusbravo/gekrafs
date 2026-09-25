<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('app.locales')), 404);

        $request->session()->put('locale', $locale);

        // url()->previous() trusts the Referer header, so only go back to pages on this site.
        $previous = url()->previous();
        $isSameSite = parse_url($previous, PHP_URL_HOST) === $request->getHost();

        return redirect($isSameSite ? $previous : localized_route('home', locale: $locale));
    }
}
