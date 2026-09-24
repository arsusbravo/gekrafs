@props(['post'])

<a href="{{ route('blog.show', $post) }}" class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 transition hover:-translate-y-1 hover:shadow-xl hover:ring-brand-200">
    @if ($post->image_url)
        <img src="{{ $post->image_url }}" alt="" class="aspect-[16/10] w-full object-cover">
    @else
        <div class="bg-pattern flex aspect-[16/10] w-full items-center justify-center">
            <img src="{{ asset('storage/images/logo.png') }}" alt="" class="h-16 w-16 opacity-90">
        </div>
    @endif
    <div class="flex flex-1 flex-col gap-3 p-6">
        <p class="text-xs font-semibold tracking-wide text-brand-600 uppercase">{{ $post->published_at->translatedFormat('j F Y') }}</p>
        <h3 class="text-lg leading-snug font-bold text-gray-900 group-hover:text-brand-700">{{ $post->title }}</h3>
        @if ($post->excerpt)
            <p class="line-clamp-3 text-sm leading-relaxed text-gray-600">{{ $post->excerpt }}</p>
        @endif
        <span class="mt-auto inline-flex items-center gap-2 pt-2 text-sm font-semibold text-brand-600">
            {{ __('Read more') }} <x-icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-1" />
        </span>
    </div>
</a>
