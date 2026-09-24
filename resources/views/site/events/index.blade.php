<x-layouts.site :title="__('Events')">
    <x-site.page-hero :eyebrow="__('Events')" :title="__('Upcoming events')" :image="asset('images/pelantikan.jpg')">
        {{ __('Join us at one of our events.') }}
    </x-site.page-hero>

    <section class="bg-gray-50 py-16 sm:py-20">
        <div class="container-site">
            @if ($upcoming->isEmpty())
                <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-white px-6 py-14 text-center">
                    <x-icon name="calendar" class="mx-auto h-10 w-10 text-brand-300" />
                    <p class="mt-3 text-gray-500">{{ __('There are no upcoming events right now.') }}</p>
                </div>
            @else
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($upcoming as $event)
                        <x-event-card :event="$event" />
                    @endforeach
                </div>
                <div class="mt-10">{{ $upcoming->links() }}</div>
            @endif
        </div>
    </section>

    @if ($past->isNotEmpty())
        <section class="py-16 sm:py-20">
            <div class="container-site">
                <x-section-heading :title="__('Past events')" />
                <ul class="divide-y divide-gray-100 overflow-hidden rounded-2xl bg-white ring-1 ring-gray-200">
                    @foreach ($past as $event)
                        <li>
                            <a href="{{ localized_route('events.show', $event) }}" class="group flex items-center gap-5 px-5 py-4 transition hover:bg-brand-50">
                                <div class="w-16 shrink-0 text-center">
                                    <div class="font-display text-3xl leading-none text-gray-400 group-hover:text-brand-600">{{ $event->starts_at->format('d') }}</div>
                                    <div class="text-[11px] font-bold tracking-wider text-gray-400 uppercase">{{ $event->starts_at->translatedFormat('M Y') }}</div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold text-gray-800 group-hover:text-brand-700">{{ $event->title }}</p>
                                    @if ($event->location)
                                        <p class="truncate text-sm text-gray-500">{{ $event->location }}</p>
                                    @endif
                                </div>
                                <x-icon name="arrow-right" class="h-5 w-5 shrink-0 text-gray-300 transition group-hover:translate-x-1 group-hover:text-brand-600" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif
</x-layouts.site>
