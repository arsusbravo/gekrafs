<x-layouts.site>
    {{-- Hero --}}
    <section class="bg-pattern relative overflow-hidden text-white">
        <div class="absolute inset-0 bg-linear-to-br from-brand-950/85 via-brand-800/60 to-transparent"></div>
        <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-sky-brand/30 blur-3xl"></div>
        <div class="absolute bottom-10 left-1/3 h-4 w-4 rounded-full bg-accent-400"></div>
        <div class="absolute top-24 left-1/2 h-2.5 w-2.5 rounded-full bg-accent-400"></div>

        <div class="container-site relative grid items-end gap-10 pt-16 lg:grid-cols-2 lg:pt-20">
            <div class="pb-16 lg:pb-24">
                <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-sm font-semibold ring-1 ring-white/20 backdrop-blur">
                    <img src="{{ asset('storage/images/id.png') }}" alt="" class="h-4 w-4 rounded-full">
                    <img src="{{ asset('storage/images/nl.png') }}" alt="" class="-ml-3 h-4 w-4 rounded-full ring-2 ring-brand-700">
                    {{ __('Representative Council Abroad – The Netherlands') }}
                </p>
                <h1 class="mt-6 font-display text-7xl leading-[0.85] tracking-wide sm:text-8xl xl:text-9xl">
                    <span class="block">Gerakan</span>
                    <span class="block text-accent-400">Ekonomi Kreatif</span>
                    <span class="block">Nasional</span>
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-brand-100">{{ __("Empowering Indonesia's creative economy, from the Netherlands") }}.</p>
                <p class="mt-2 font-semibold text-accent-400">#EkrafBangkitIndonesiaMaju</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('about') }}" class="btn btn-accent px-6 py-3 text-base">{{ __('Discover our work') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                    <a href="{{ route('events.index') }}" class="btn btn-ghost-light px-6 py-3 text-base">{{ __('Events') }}</a>
                </div>
            </div>
            <div class="relative mx-auto w-full max-w-lg lg:max-w-none">
                <img src="{{ asset('storage/images/members.png') }}" alt="GEKRAFS DPLN {{ __('The Netherlands') }}" class="relative w-full drop-shadow-2xl">
            </div>
        </div>
    </section>

    <x-site.stats />

    {{-- Who we are --}}
    <section class="py-20 sm:py-28">
        <div class="container-site grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
            <div class="relative">
                <img src="{{ asset('storage/images/members1.png') }}" alt="GEKRAFS" class="photo-frame w-full -rotate-2">
                <img src="{{ asset('storage/images/pelantikan.jpg') }}" alt="" class="photo-frame absolute -right-4 -bottom-12 hidden w-1/2 rotate-3 sm:block">
                <div class="absolute -top-5 -left-5 -z-10 h-32 w-32 rounded-3xl bg-accent-400"></div>
            </div>
            <div class="sm:pt-10 lg:pt-0">
                <x-section-heading :eyebrow="__('Who we are')" title="Gekrafs" />
                <p class="-mt-6 mb-6 text-lg font-semibold text-brand-700">{{ __('organization.gekrafs') ?: 'Gerakan Ekonomi Kreatif Nasional' }}</p>
                <p class="text-lg leading-relaxed text-gray-600">{{ __('organization.gekrafs definition') }}</p>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    @foreach (['mission title' => 'flag', 'sectors title' => 'sparkles', 'vision title' => 'eye'] as $key => $icon)
                        <a href="{{ route('about') }}#{{ \Illuminate\Support\Str::before($key, ' ') }}" class="group rounded-2xl bg-brand-50 p-4 transition hover:bg-brand-600 hover:text-white">
                            <x-icon :name="$icon" class="h-6 w-6 text-brand-600 group-hover:text-accent-400" />
                            <p class="mt-3 text-sm leading-snug font-bold">{{ __('organization.'.$key) }}</p>
                        </a>
                    @endforeach
                </div>

                <a href="{{ route('about') }}" class="btn btn-primary mt-8">{{ __('Learn more about us') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
            </div>
        </div>
    </section>

    {{-- Latest news --}}
    <section class="bg-gray-50 py-20 sm:py-28">
        <div class="container-site">
            <x-section-heading :eyebrow="__('News')" :title="__('Latest news')" :link="route('blog.index')" :link-label="__('View all')" />
            @if ($posts->isEmpty())
                <p class="text-gray-500">{{ __('No news published yet.') }}</p>
            @else
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Upcoming events --}}
    <section class="py-20 sm:py-28">
        <div class="container-site">
            <x-section-heading :eyebrow="__('Events')" :title="__('Upcoming events')" :link="route('events.index')" :link-label="__('View all')" />
            @if ($events->isEmpty())
                <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 px-6 py-14 text-center">
                    <x-icon name="calendar" class="mx-auto h-10 w-10 text-brand-300" />
                    <p class="mt-3 text-gray-500">{{ __('No upcoming events yet. Check back soon.') }}</p>
                </div>
            @else
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($events as $event)
                        <x-event-card :event="$event" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Central board --}}
    <section class="bg-pattern relative overflow-hidden py-20 text-white sm:py-24">
        <div class="absolute inset-0 bg-linear-to-b from-brand-900/70 to-brand-700/40"></div>
        <div class="container-site relative text-center">
            <p class="eyebrow justify-center text-accent-400">DPP GEKRAFS</p>
            <h2 class="section-title mt-2">{{ __('Central Board') }}</h2>
            <p class="mx-auto mt-3 max-w-xl text-brand-100">{{ __('The central leadership of GEKRAFS Indonesia') }}</p>
            <img src="{{ asset('storage/images/dpp.png') }}" alt="Dewan Pimpinan Pusat GEKRAFS" class="mx-auto mt-10 w-full max-w-5xl">
        </div>
    </section>

    {{-- Inauguration gallery --}}
    <section class="py-20 sm:py-28">
        <div class="container-site">
            <x-section-heading eyebrow="DPLN Belanda" :title="__('programs.Inauguration of Representative Council in the Netherlands')" />
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:grid-rows-2">
                <img src="{{ asset('storage/images/boards.jpg') }}" alt="DPLN Belanda" class="h-full min-h-72 w-full rounded-2xl object-cover sm:col-span-2 lg:row-span-2">
                <img src="{{ asset('storage/images/members3.png') }}" alt="" class="h-full min-h-72 w-full rounded-2xl object-cover lg:row-span-2">
                <img src="{{ asset('storage/images/members2.png') }}" alt="" class="h-full min-h-48 w-full rounded-2xl object-cover">
                <img src="{{ asset('storage/images/members4.png') }}" alt="" class="h-full min-h-48 w-full rounded-2xl object-cover object-top">
            </div>
        </div>
    </section>

    {{-- Partners + CTA --}}
    <section class="py-20 sm:py-24">
        <div class="container-site">
            <div class="bg-pattern relative overflow-hidden rounded-3xl px-6 py-14 text-white sm:px-14">
                <div class="absolute inset-0 bg-linear-to-r from-brand-950/90 to-brand-800/50"></div>
                <div class="relative grid items-center gap-10 lg:grid-cols-2">
                    <div>
                        <h2 class="section-title">{{ __('Get in touch') }}</h2>
                        <p class="mt-4 max-w-lg text-brand-100">{{ __('Questions about GEKRAFS in the Netherlands? Our representatives are happy to help.') }}</p>
                        <a href="{{ route('contact') }}" class="btn btn-accent mt-8 px-6 py-3">{{ __('Contact') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                    </div>
                    <div class="lg:justify-self-end">
                        <p class="eyebrow text-accent-400">{{ __('Our partners') }}</p>
                        <div class="mt-4 flex flex-wrap gap-4">
                            @foreach (config('gekrafs.partners') as $partner)
                                <a href="{{ $partner['url'] }}" target="_blank" rel="noopener" class="flex h-24 items-center rounded-2xl bg-white px-6 shadow-lg transition hover:-translate-y-1" title="{{ $partner['name'] }}">
                                    <img src="{{ asset('storage/'.$partner['logo']) }}" alt="{{ $partner['name'] }}" class="max-h-16 w-auto">
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.site>
