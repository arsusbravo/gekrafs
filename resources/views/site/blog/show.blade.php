<x-layouts.site :title="$post->title">
    <article>
        <header class="bg-pattern relative overflow-hidden text-white">
            <div class="absolute inset-0 bg-linear-to-br from-brand-950/90 to-brand-800/60"></div>
            <div class="container-site relative max-w-4xl pt-12 pb-28 text-center sm:pt-16">
                <a href="{{ localized_route('blog.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-200 hover:text-white">
                    <x-icon name="arrow-left" class="h-4 w-4" /> {{ __('All news') }}
                </a>
                <p class="mt-6 text-sm font-semibold tracking-wide text-accent-400 uppercase">
                    {{ $post->published_at->translatedFormat('j F Y') }}
                    @if ($post->user) &middot; {{ __('by') }} {{ $post->user->name }} @endif
                </p>
                <h1 class="mt-3 font-display text-5xl leading-none tracking-wide sm:text-7xl">{{ $post->title }}</h1>
            </div>
        </header>

        <div class="container-site max-w-4xl pb-20">
            @if ($post->image_url)
                <img src="{{ $post->image_url }}" alt="" class="relative -mt-20 w-full rounded-3xl object-cover shadow-2xl ring-8 ring-white">
            @else
                <div class="-mt-20"></div>
            @endif

            <div class="mx-auto mt-12 max-w-3xl">
                @if ($post->excerpt)
                    <p class="mb-8 border-l-4 border-accent-500 pl-5 text-xl leading-relaxed font-medium text-brand-900">{{ $post->excerpt }}</p>
                @endif
                <div class="prose-gekrafs">{!! $post->body_html !!}</div>

                @if ($post->event)
                    <a href="{{ localized_route('events.show', $post->event) }}" class="group mt-12 flex items-center gap-5 rounded-2xl bg-brand-950 p-5 text-white transition hover:bg-brand-900">
                        <div class="w-16 shrink-0 rounded-xl bg-white py-2 text-center">
                            <div class="font-display text-3xl leading-none text-brand-700">{{ $post->event->starts_at->format('d') }}</div>
                            <div class="text-[11px] font-bold tracking-wider text-gray-500 uppercase">{{ $post->event->starts_at->translatedFormat('M Y') }}</div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold tracking-wider text-accent-400 uppercase">{{ __('A report of') }}</p>
                            <p class="mt-1 font-semibold">{{ $post->event->title }}</p>
                            @if ($post->event->location)
                                <p class="truncate text-sm text-brand-200">{{ $post->event->location }}</p>
                            @endif
                        </div>
                        <span class="hidden items-center gap-2 text-sm font-semibold text-accent-400 sm:inline-flex">{{ __('View event') }} <x-icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-1" /></span>
                    </a>
                @endif
            </div>
        </div>
    </article>
</x-layouts.site>
