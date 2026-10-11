@props(['event', 'ribbon' => true])

<a href="{{ localized_route('events.show', $event) }}" class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 transition hover:-translate-y-1 hover:shadow-xl hover:ring-brand-200">
    @if ($ribbon && $event->is_upcoming)
        <div data-ribbon="upcoming" class="pointer-events-none absolute top-0 right-0 z-10 h-28 w-28 overflow-hidden" aria-hidden="true">
            <span class="absolute top-6 -right-9 w-40 rotate-45 bg-accent-500 py-1 text-center text-xs font-bold tracking-widest text-brand-950 uppercase shadow-md">{{ __('Upcoming') }}</span>
        </div>
    @endif
    <div class="relative h-56 shrink-0 overflow-hidden">
        @if ($event->image_url)
            <img src="{{ $event->image_url }}" alt="" class="h-full w-full object-cover object-center">
        @else
            <div class="bg-pattern h-full w-full"></div>
        @endif
        <div class="absolute top-4 left-4 rounded-xl bg-white px-3 py-2 text-center shadow-md">
            <div class="font-display text-3xl leading-none text-brand-700">{{ $event->starts_at->format('d') }}</div>
            <div class="text-[11px] font-bold tracking-wider text-gray-500 uppercase">{{ $event->starts_at->translatedFormat('M Y') }}</div>
        </div>
    </div>
    <div class="flex flex-1 flex-col gap-3 p-6">
        <h3 class="text-lg leading-snug font-bold text-gray-900 group-hover:text-brand-700">{{ $event->title }}</h3>
        <div class="mt-auto space-y-1.5 text-sm text-gray-500">
            <p class="flex items-center gap-2"><x-icon name="clock" class="h-4 w-4 text-brand-500" /> {{ $event->starts_at->translatedFormat('l, H:i') }}</p>
            @if ($event->location)
                <p class="flex items-center gap-2"><x-icon name="map-pin" class="h-4 w-4 shrink-0 text-brand-500" /> <span class="truncate">{{ $event->location }}</span></p>
            @endif
        </div>
    </div>
</a>
