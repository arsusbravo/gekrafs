<x-layouts.site :title="__('About')">
    <x-site.page-hero :eyebrow="__('Who we are')" title="Gekrafs | Gerakan Ekonomi Kreatif Nasional" :image="asset('storage/images/members1.png')">
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
                <img src="{{ asset('storage/images/members1.png') }}" alt="GEKRAFS" class="photo-frame w-full rotate-2">
                <div class="absolute -right-4 -bottom-4 -z-10 h-full w-full rounded-2xl bg-accent-400"></div>
            </div>
        </div>
    </section>

    <x-site.stats />

    <section id="mission" class="scroll-mt-24 py-20 sm:py-24">
        <div class="container-site grid items-center gap-12 lg:grid-cols-5 lg:gap-16">
            <img src="{{ asset('storage/images/members2.png') }}" alt="" class="photo-frame w-full -rotate-1 lg:col-span-3">
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
            <img src="{{ asset('storage/images/members3.png') }}" alt="" class="photo-frame mx-auto hidden max-h-[36rem] w-auto rotate-2 lg:block">
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
            <img src="{{ asset('storage/images/members4.png') }}" alt="" class="photo-frame mx-auto max-h-[36rem] w-auto rotate-2 lg:col-span-2">
        </div>
    </section>

    <section class="bg-gray-50 py-20">
        <div class="container-site text-center">
            <p class="eyebrow justify-center text-brand-600">DPP GEKRAFS</p>
            <h2 class="section-title mt-2 text-brand-950">{{ __('Central Board') }}</h2>
            <div class="bg-pattern mt-10 overflow-hidden rounded-3xl px-4 pt-8">
                <img src="{{ asset('storage/images/dpp.png') }}" alt="Dewan Pimpinan Pusat GEKRAFS" class="mx-auto w-full max-w-5xl">
            </div>
        </div>
    </section>
</x-layouts.site>
