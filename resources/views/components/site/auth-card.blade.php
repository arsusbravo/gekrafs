@props(['title', 'subtitle'])

<section class="bg-pattern relative overflow-hidden">
    <div class="absolute inset-0 bg-linear-to-br from-brand-950/85 to-brand-800/50"></div>
    <div class="container-site relative grid min-h-[calc(100vh-5rem)] items-center gap-12 py-16 lg:grid-cols-2">
        <div class="hidden text-white lg:block">
            <img src="{{ asset('storage/images/logo.png') }}" alt="GEKRAFS" class="h-24 w-24">
            <h2 class="mt-6 font-display text-7xl leading-[0.9] tracking-wide">Gerakan<br><span class="text-accent-400">Ekonomi Kreatif</span><br>Nasional</h2>
            <p class="mt-6 max-w-md text-brand-100">{{ __('Representative Council Abroad – The Netherlands') }}</p>
        </div>
        <div class="mx-auto w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl sm:p-10">
            <h1 class="font-display text-5xl tracking-wide text-brand-950">{{ $title }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>
            {{ $slot }}
        </div>
    </div>
</section>
