<x-layouts.site :title="$event->title">
    <section class="bg-pattern relative overflow-hidden text-white">
        @if ($event->image_url)
            <img src="{{ $event->image_url }}" alt="" class="absolute inset-0 h-full w-full object-cover">
        @endif
        <div class="absolute inset-0 bg-linear-to-t from-brand-950 via-brand-950/80 to-brand-900/40"></div>
        <div class="container-site relative pt-12 pb-16 sm:pt-16 sm:pb-20">
            <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-200 hover:text-white">
                <x-icon name="arrow-left" class="h-4 w-4" /> {{ __('All events') }}
            </a>
            <h1 class="mt-6 max-w-4xl font-display text-6xl leading-none tracking-wide sm:text-7xl">{{ $event->title }}</h1>
        </div>
    </section>

    <section class="py-14 sm:py-20">
        <div class="container-site grid gap-12 lg:grid-cols-3">
            <aside class="lg:order-2">
                <dl class="sticky top-28 space-y-6 rounded-3xl bg-brand-950 p-8 text-white">
                    <div class="flex gap-4">
                        <x-icon name="calendar" class="h-6 w-6 shrink-0 text-accent-400" />
                        <div>
                            <dt class="text-xs font-bold tracking-wider text-brand-300 uppercase">{{ __('When') }}</dt>
                            <dd class="mt-1 font-semibold">{{ $event->starts_at->translatedFormat('l j F Y') }}</dd>
                            <dd class="text-brand-200">
                                {{ $event->starts_at->format('H:i') }}
                                @if ($event->ends_at)
                                    &ndash; {{ $event->ends_at->isSameDay($event->starts_at) ? $event->ends_at->format('H:i') : $event->ends_at->translatedFormat('j F Y, H:i') }}
                                @endif
                            </dd>
                        </div>
                    </div>
                    @if ($event->location)
                        <div class="flex gap-4">
                            <x-icon name="map-pin" class="h-6 w-6 shrink-0 text-accent-400" />
                            <div>
                                <dt class="text-xs font-bold tracking-wider text-brand-300 uppercase">{{ __('Where') }}</dt>
                                <dd class="mt-1 font-semibold">{{ $event->location }}</dd>
                                <dd><a href="https://maps.google.com/?q={{ urlencode($event->location) }}" target="_blank" rel="noopener" class="mt-1 inline-flex items-center gap-1 text-sm text-accent-400 hover:text-accent-300">{{ __('View on Google Maps') }} <x-icon name="external" class="h-3.5 w-3.5" /></a></dd>
                            </div>
                        </div>
                    @endif
                </dl>
            </aside>

            <div class="lg:order-1 lg:col-span-2">
                @if ($event->image_url)
                    <img src="{{ $event->image_url }}" alt="" class="mb-10 w-full rounded-3xl object-cover shadow-lg">
                @endif
                @if ($event->description_html)
                    <div class="prose-gekrafs">{!! $event->description_html !!}</div>
                @endif

                @if ($reports->isNotEmpty())
                    <div class="mt-14 border-t border-gray-200 pt-10">
                        <p class="eyebrow text-brand-600"><span class="h-0.5 w-8 bg-current"></span>{{ __('Event report') }}</p>
                        <div class="mt-6 grid gap-6 sm:grid-cols-2">
                            @foreach ($reports as $report)
                                <x-post-card :post="$report" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.site>
