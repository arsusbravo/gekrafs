<?php

use App\Support\Localization;
use Illuminate\Support\Str;

if (! function_exists('localized_route')) {
    /**
     * Generate a URL to a public page in the current (or given) language.
     */
    function localized_route(string $name, mixed $parameters = [], ?string $locale = null): string
    {
        return route(Localization::routeName($name, $locale), $parameters);
    }
}

if (! function_exists('localized_route_is')) {
    /**
     * Check the current route name, ignoring its language prefix.
     */
    function localized_route_is(string ...$patterns): bool
    {
        $name = Localization::baseRouteName(request()->route());

        return $name !== null && Str::is($patterns, $name);
    }
}

if (! function_exists('current_url_in_locale')) {
    /**
     * URL of the current page in another language (used by the language switcher).
     */
    function current_url_in_locale(string $locale): string
    {
        return Localization::currentUrlIn($locale);
    }
}
