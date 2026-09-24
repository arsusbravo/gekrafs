@props(['title' => null])

@php
    $locales = config('app.locales');
    $locale = app()->getLocale();
    $links = [
        ['route' => 'home', 'label' => __('Home'), 'active' => 'home'],
        ['route' => 'about', 'label' => __('About'), 'active' => 'about'],
        ['route' => 'events.index', 'label' => __('Events'), 'active' => 'events.*'],
        ['route' => 'blog.index', 'label' => __('News'), 'active' => 'blog.*'],
        ['route' => 'contact', 'label' => __('Contact'), 'active' => 'contact'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}GEKRAFS {{ __('The Netherlands') }}</title>
    <meta name="description" content="{{ __('organization.gekrafs definition') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col overflow-x-clip bg-white font-sans text-gray-900 antialiased">
    <header class="sticky top-0 z-40 border-b border-gray-100 bg-white/90 backdrop-blur">
        <div class="container-site flex h-20 items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="GEKRAFS" class="h-12 w-12">
                <span class="leading-none">
                    <span class="block font-display text-3xl tracking-wider text-brand-800">Gekrafs</span>
                    <span class="block text-[11px] font-semibold tracking-wide text-gray-500 uppercase">DPLN {{ __('The Netherlands') }}</span>
                </span>
            </a>

            <nav class="hidden items-center gap-1 lg:flex">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}"
                       @class([
                           'rounded-full px-4 py-2 text-sm font-semibold transition',
                           'bg-brand-50 text-brand-700' => request()->routeIs($link['active']),
                           'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => ! request()->routeIs($link['active']),
                       ])>{{ $link['label'] }}</a>
                @endforeach
            </nav>

            <div class="hidden items-center gap-3 lg:flex">
                <x-site.language-switcher :locales="$locales" :locale="$locale" />

                @auth
                    @if (auth()->user()->is_admin)
                        <a href="/admin" class="btn btn-secondary py-2">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary py-2" title="{{ auth()->user()->name }}">{{ __('Log out') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary py-2">{{ __('Log in') }}</a>
                @endauth
            </div>

            <button type="button" class="rounded-full p-2 text-gray-700 hover:bg-gray-100 lg:hidden" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu">
                <span class="sr-only">{{ __('Menu') }}</span>
                <x-icon name="menu" class="h-6 w-6" data-menu-open />
                <x-icon name="x" class="hidden h-6 w-6" data-menu-close />
            </button>
        </div>

        <div id="mobile-menu" class="hidden border-t border-gray-100 bg-white lg:hidden" data-menu>
            <div class="container-site space-y-1 py-4">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}"
                       @class([
                           'block rounded-lg px-3 py-2.5 text-base font-semibold',
                           'bg-brand-50 text-brand-700' => request()->routeIs($link['active']),
                           'text-gray-700 hover:bg-gray-50' => ! request()->routeIs($link['active']),
                       ])>{{ $link['label'] }}</a>
                @endforeach

                <div class="flex items-center gap-2 px-3 pt-3">
                    @foreach ($locales as $code => $name)
                        <a href="{{ route('locale', $code) }}" @class(['flex items-center gap-2 rounded-full border px-3 py-1.5 text-sm font-medium', 'border-brand-600 bg-brand-50 text-brand-700' => $code === $locale, 'border-gray-200 text-gray-600' => $code !== $locale])>
                            <img src="{{ asset('images/'.$code.'.png') }}" alt="" class="h-5 w-5 rounded-full">
                            {{ strtoupper($code) }}
                        </a>
                    @endforeach
                </div>

                <div class="flex gap-3 border-t border-gray-100 px-3 pt-4 mt-3">
                    @auth
                        @if (auth()->user()->is_admin)
                            <a href="/admin" class="btn btn-secondary flex-1">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="btn btn-primary w-full">{{ __('Log out') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary flex-1">{{ __('Log in') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @if (session('status'))
        <div class="bg-accent-400">
            <div class="container-site py-3 text-sm font-semibold text-brand-950">{{ session('status') }}</div>
        </div>
    @endif

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-site.footer :links="$links" />
</body>
</html>
