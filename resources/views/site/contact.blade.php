<x-layouts.site :title="__('Contact')">
    <x-site.page-hero :eyebrow="__('Contact')" :title="__('Get in touch')" :image="asset('images/boards.jpg')">
        {{ __('Questions about GEKRAFS in the Netherlands? Our representatives are happy to help.') }}
    </x-site.page-hero>

    <section class="py-20 sm:py-24">
        <div class="container-site grid gap-8 lg:grid-cols-5">
            <div class="space-y-8 lg:col-span-2">
                <div class="rounded-3xl bg-brand-950 p-8 text-white">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/nl.png') }}" alt="" class="h-8 w-8 rounded-full">
                        <h2 class="font-display text-3xl tracking-wide">{{ __('Representative in the netherlands') }}</h2>
                    </div>
                    <ul class="mt-6 divide-y divide-white/10">
                        @foreach (config('gekrafs.representatives') as $rep)
                            <li class="py-4 first:pt-0 last:pb-0">
                                <p class="font-semibold">{{ $rep['name'] }}</p>
                                <a href="tel:{{ str_replace(' ', '', $rep['phone']) }}" class="mt-2 inline-flex items-center gap-2 rounded-full bg-accent-500 px-4 py-1.5 text-sm font-semibold text-brand-950 transition hover:bg-accent-400">
                                    <x-icon name="phone" class="h-4 w-4" /> {{ $rep['phone'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-3xl bg-gray-50 p-8 ring-1 ring-gray-200">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/id.png') }}" alt="" class="h-8 w-8 rounded-full">
                        <h2 class="font-display text-3xl tracking-wide text-brand-950">{{ __('Headquarter') }}</h2>
                    </div>
                    <address class="mt-5 flex gap-3 leading-relaxed text-gray-600 not-italic">
                        <x-icon name="map-pin" class="mt-1 h-5 w-5 shrink-0 text-brand-600" />
                        <span>{!! implode('<br>', array_map('e', config('gekrafs.headquarter'))) !!}</span>
                    </address>
                    <a href="{{ config('gekrafs.map_link') }}" target="_blank" rel="noopener" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-800">
                        {{ __('View on Google Maps') }} <x-icon name="external" class="h-4 w-4" />
                    </a>
                </div>
            </div>

            <div class="overflow-hidden rounded-3xl shadow-xl ring-1 ring-gray-200 lg:col-span-3">
                <iframe src="{{ config('gekrafs.map_embed') }}" class="h-full min-h-[28rem] w-full" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="GEKRAFS Headquarter"></iframe>
            </div>
        </div>
    </section>
</x-layouts.site>
