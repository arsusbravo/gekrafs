@php
    $icons = ['users', 'map', 'building', 'globe'];
@endphp

<section class="relative bg-accent-500">
    <div class="container-site grid grid-cols-2 gap-x-6 gap-y-10 py-12 lg:grid-cols-4">
        @foreach (config('gekrafs.stats') as $i => $stat)
            <div class="flex items-start gap-4">
                <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-950 text-accent-400 sm:flex">
                    <x-icon :name="$icons[$i]" class="h-6 w-6" />
                </div>
                <div>
                    <p class="font-display text-5xl leading-none text-brand-950 sm:text-6xl">
                        <span data-count="{{ $stat['value'] }}">{{ number_format($stat['value'], 0, ',', '.') }}</span>{{ $stat['suffix'] }}
                    </p>
                    <p class="mt-1 text-sm font-semibold text-brand-950/80">{{ __($stat['label']) }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>
