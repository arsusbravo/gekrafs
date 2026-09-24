@props(['links'])

<footer class="relative overflow-hidden bg-brand-950 text-brand-100">
    <div class="h-1.5 bg-linear-to-r from-brand-500 via-sky-brand to-accent-500"></div>

    <div class="container-site grid gap-12 py-16 md:grid-cols-2 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('storage/images/logo.png') }}" alt="GEKRAFS" class="h-16 w-16">
                <span class="leading-none">
                    <span class="block font-display text-4xl tracking-wider text-white">Gekrafs</span>
                    <span class="block text-xs font-semibold tracking-wide text-brand-300 uppercase">{{ __('organization.gekrafs') ?: 'Gerakan Ekonomi Kreatif Nasional' }}</span>
                </span>
            </a>
            <p class="mt-6 max-w-sm text-sm leading-relaxed text-brand-200">{{ __('Representative Council Abroad – The Netherlands') }}. <span class="font-semibold text-accent-400">#EkrafBangkitIndonesiaMaju</span></p>

            <div class="mt-6 flex flex-wrap gap-3">
                @foreach (config('gekrafs.partners') as $partner)
                    <a href="{{ $partner['url'] }}" target="_blank" rel="noopener" class="rounded-xl bg-white px-3 py-2 transition hover:scale-105" title="{{ $partner['name'] }}">
                        <img src="{{ asset('storage/'.$partner['logo']) }}" alt="{{ $partner['name'] }}" class="h-10 w-auto">
                    </a>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-2">
            <h3 class="font-display text-2xl tracking-wide text-white">{{ __('Pages') }}</h3>
            <ul class="mt-4 space-y-2 text-sm">
                @foreach ($links as $link)
                    <li><a href="{{ route($link['route']) }}" class="hover:text-white">{{ $link['label'] }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="lg:col-span-3">
            <h3 class="font-display text-2xl tracking-wide text-white">{{ __('Headquarter') }}</h3>
            <address class="mt-4 flex gap-3 text-sm leading-relaxed not-italic">
                <x-icon name="map-pin" class="mt-0.5 h-5 w-5 shrink-0 text-accent-400" />
                <span>{!! implode('<br>', array_map('e', config('gekrafs.headquarter'))) !!}</span>
            </address>
        </div>

        <div class="lg:col-span-3">
            <h3 class="font-display text-2xl tracking-wide text-white">{{ __('Representative in the netherlands') }}</h3>
            <ul class="mt-4 space-y-4 text-sm">
                @foreach (config('gekrafs.representatives') as $rep)
                    <li>
                        <p class="font-semibold text-white">{{ $rep['name'] }}</p>
                        <a href="tel:{{ str_replace(' ', '', $rep['phone']) }}" class="mt-0.5 inline-flex items-center gap-2 hover:text-white">
                            <x-icon name="phone" class="h-4 w-4 text-accent-400" /> {{ $rep['phone'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-site flex flex-col items-center justify-between gap-2 py-6 text-xs text-brand-300 sm:flex-row">
            <p>&copy; {{ date('Y') }} GEKRAFS &middot; DPLN {{ __('The Netherlands') }}. {{ __('All rights reserved.') }}</p>
            <p>Gerakan Ekonomi Kreatif Nasional</p>
        </div>
    </div>
</footer>
