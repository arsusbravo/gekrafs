<?php

namespace App\Support;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;

/**
 * URL-based languages for the public website: English lives at the root (/events),
 * other languages under their code (/nl/events, /id/events).
 */
class Localization
{
    /**
     * The language served at the root URLs. Read from fallback_locale because
     * App::setLocale() overwrites app.locale during the request.
     */
    public static function default(): string
    {
        return config('app.fallback_locale');
    }

    /**
     * @return list<string>
     */
    public static function locales(): array
    {
        return array_keys(config('app.locales'));
    }

    public static function supports(?string $locale): bool
    {
        return in_array($locale, self::locales(), true);
    }

    /**
     * Route name for a locale: "events.show" becomes "nl.events.show" for Dutch.
     */
    public static function routeName(string $name, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === self::default() ? $name : "{$locale}.{$name}";
    }

    /**
     * The locale a route was registered for, or null for routes that are not localized (login, admin).
     */
    public static function localeOf(?Route $route): ?string
    {
        return $route?->getAction('locale');
    }

    /**
     * Route name without its language prefix: "nl.events.show" becomes "events.show".
     */
    public static function baseRouteName(?Route $route): ?string
    {
        $name = $route?->getName();
        $locale = self::localeOf($route);

        if ($name === null || $locale === null || $locale === self::default()) {
            return $name;
        }

        return Str::after($name, "{$locale}.");
    }

    /**
     * URL of the current page in another language. Pages that are not localized
     * fall back to switching the remembered language.
     */
    public static function currentUrlIn(string $locale): string
    {
        $route = request()->route();

        if (self::localeOf($route) === null) {
            return route('locale', $locale);
        }

        $url = route(self::routeName(self::baseRouteName($route), $locale), $route->parameters());
        $query = request()->getQueryString();

        return $query ? "{$url}?{$query}" : $url;
    }
}
