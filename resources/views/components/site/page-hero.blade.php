@props(['title', 'eyebrow' => null, 'image' => null])

<section class="bg-pattern relative overflow-hidden text-white">
    <div class="absolute inset-0 bg-linear-to-r from-brand-950/90 via-brand-900/70 to-brand-700/30"></div>
    @if ($image)
        <img src="{{ $image }}" alt="" class="absolute inset-y-0 right-0 hidden h-full w-1/2 object-cover opacity-40 mix-blend-luminosity lg:block">
        <div class="absolute inset-y-0 right-0 hidden w-1/2 bg-linear-to-r from-brand-950/90 to-transparent lg:block"></div>
    @endif
    <div class="container-site relative py-16 sm:py-20">
        @if ($eyebrow)
            <p class="eyebrow text-accent-400">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-3 max-w-3xl font-display text-6xl leading-none tracking-wide sm:text-7xl">{{ $title }}</h1>
        @if ($slot->isNotEmpty())
            <div class="mt-5 max-w-2xl text-lg text-brand-100">{{ $slot }}</div>
        @endif
    </div>
</section>
