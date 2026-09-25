<x-layouts.site :title="__('About')">
    <x-site.page-hero :eyebrow="__('Who we are')" title="Gekrafs | Gerakan Ekonomi Kreatif Nasional" :image="asset('images/members1.png')">
        @if (app()->getLocale() !== 'id')
            <em>{{ __('organization.gekrafs') }}</em> &middot;
        @endif
        <span class="font-semibold text-accent-400">#EkrafBangkitIndonesiaMaju</span>
    </x-site.page-hero>

    <section class="py-20 sm:py-24">
        <div class="container-site grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
            <div>
                <p class="text-2xl leading-relaxed font-medium text-brand-950 sm:text-3xl sm:leading-snug">{{ __('organization.gekrafs definition') }}</p>
            </div>
            <div class="relative">
                <img src="{{ asset('images/members1.png') }}" alt="GEKRAFS" class="photo-frame w-full rotate-2">
                <div class="absolute -right-4 -bottom-4 -z-10 h-full w-full rounded-2xl bg-accent-400"></div>
            </div>
        </div>
    </section>

    <x-site.stats />

    <section id="mission" class="scroll-mt-24 py-20 sm:py-24">
        <div class="container-site grid items-center gap-12 lg:grid-cols-5 lg:gap-16">
            <img src="{{ asset('images/members2.png') }}" alt="" class="photo-frame w-full -rotate-1 lg:col-span-3">
            <div class="lg:col-span-2">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-600 text-white"><x-icon name="flag" class="h-6 w-6" /></div>
                <h2 class="section-title mt-5 text-brand-950">{{ __('organization.mission title') }}</h2>
                <p class="mt-5 text-lg leading-relaxed text-gray-600">{{ __('organization.mission') }}</p>
            </div>
        </div>
    </section>

    <section id="sectors" class="bg-pattern relative scroll-mt-24 overflow-hidden py-20 text-white sm:py-24">
        <div class="absolute inset-0 bg-brand-950/80"></div>
        <div class="container-site relative grid gap-12 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-accent-500 text-brand-950"><x-icon name="sparkles" class="h-6 w-6" /></div>
                <h2 class="section-title mt-5">{{ __('organization.sectors title') }}</h2>
                <p class="mt-5 max-w-3xl text-lg leading-relaxed text-brand-100">{{ __('organization.sectors') }}</p>

                <ul class="mt-10 flex flex-wrap gap-2.5">
                    @foreach (__('organization.sector list') as $i => $sector)
                        <li class="flex items-center gap-2 rounded-full bg-white/10 py-1.5 pr-4 pl-1.5 text-sm font-semibold ring-1 ring-white/20 backdrop-blur">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-accent-500 text-xs font-bold text-brand-950">{{ $i + 1 }}</span>
                            {{ $sector }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <img src="{{ asset('images/members3.png') }}" alt="" class="photo-frame mx-auto hidden max-h-144 w-auto rotate-2 lg:block">
        </div>
    </section>

    <section id="vision" class="scroll-mt-24 py-20 sm:py-24">
        <div class="container-site grid items-center gap-12 lg:grid-cols-5 lg:gap-16">
            <div class="lg:col-span-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-600 text-white"><x-icon name="eye" class="h-6 w-6" /></div>
                <h2 class="section-title mt-5 text-brand-950">{{ __('organization.vision title') }}</h2>
                <p class="mt-5 text-lg leading-relaxed text-gray-600">{{ __('organization.vision') }}</p>
                <blockquote class="mt-8 border-l-4 border-accent-500 bg-accent-300/20 py-4 pr-4 pl-6 text-lg leading-relaxed font-medium text-brand-900 italic">
                    {{ __('organization.vision closing') }}
                </blockquote>
            </div>
            <img src="{{ asset('images/members4.png') }}" alt="" class="photo-frame mx-auto max-h-144 w-auto rotate-2 lg:col-span-2">
        </div>
    </section>

    @php
        $board = config('gekrafs.board');
        $chair = array_shift($board);
        $initials = fn (string $name) => collect(explode(' ', $name))
            ->pipe(fn ($words) => mb_substr($words->first(), 0, 1).mb_substr($words->last(), 0, 1));
    @endphp

    <section id="board" class="bg-pattern relative scroll-mt-24 overflow-hidden py-20 text-white sm:py-24">
        <div class="absolute inset-0 bg-brand-950/85"></div>
        <div class="container-site relative">
            <div class="text-center">
                <p class="eyebrow justify-center text-accent-400"><span class="h-0.5 w-8 bg-current"></span>{{ __('Our team') }}<span class="h-0.5 w-8 bg-current"></span></p>
                <h2 class="section-title mt-2">{{ __('Board of DPLN GEKRAFS Netherlands') }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-brand-100">{{ __('The people who represent GEKRAFS in the Netherlands.') }}</p>
            </div>

            <div class="mx-auto mt-12 flex max-w-md flex-col items-center rounded-3xl bg-white/10 px-6 py-8 text-center ring-1 ring-white/20 backdrop-blur">
                @if (! empty($chair['photo']))
                    <img src="{{ asset($chair['photo']) }}" alt="{{ $chair['name'] }}" class="h-36 w-36 rounded-full bg-accent-500 object-cover ring-4 ring-accent-400 ring-offset-4 ring-offset-brand-900">
                @else
                    <div class="flex h-36 w-36 items-center justify-center rounded-full bg-accent-500 font-display text-5xl tracking-wide text-brand-950 ring-4 ring-accent-300/50">
                        {{ $initials($chair['name']) }}
                    </div>
                @endif
                <p class="mt-5 text-xs font-bold tracking-[0.2em] text-accent-400 uppercase">{{ __($chair['role']) }}</p>
                <h3 class="mt-1 font-display text-4xl leading-none tracking-wide">{{ $chair['name'] }}</h3>
            </div>

            <ul class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($board as $member)
                    <li class="flex flex-col items-center rounded-2xl bg-white/5 px-4 py-6 text-center ring-1 ring-white/10 backdrop-blur">
                        @if (! empty($member['photo']))
                            <img src="{{ asset($member['photo']) }}" alt="{{ $member['name'] }}" loading="lazy" class="h-28 w-28 rounded-full bg-brand-600 object-cover ring-4 ring-white/15">
                        @else
                            <div class="flex h-28 w-28 items-center justify-center rounded-full bg-brand-600 font-display text-3xl tracking-wide ring-4 ring-white/10">
                                {{ $initials($member['name']) }}
                            </div>
                        @endif
                        <p class="mt-4 text-xs font-bold tracking-[0.2em] text-accent-400 uppercase">{{ __($member['role']) }}</p>
                        <h3 class="mt-1 font-semibold leading-snug">{{ $member['name'] }}</h3>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="bg-gray-50 py-20">
        <div class="container-site text-center">
            <p class="eyebrow justify-center text-brand-600">DPP GEKRAFS</p>
            <h2 class="section-title mt-2 text-brand-950">{{ __('Central Board') }}</h2>
            <div class="bg-pattern mt-10 overflow-hidden rounded-3xl px-4 pt-8">
                <img src="{{ asset('images/dpp.png') }}" alt="Dewan Pimpinan Pusat GEKRAFS" class="mx-auto w-full max-w-5xl">
            </div>
        </div>
    </section>
</x-layouts.site>
