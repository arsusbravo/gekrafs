<x-layouts.site :title="__('News')">
    <x-site.page-hero :eyebrow="__('News')" :title="__('Latest news')" :image="asset('storage/images/members1.png')">
        {{ __('News, stories and updates from GEKRAFS in the Netherlands.') }}
    </x-site.page-hero>

    <section class="bg-gray-50 py-16 sm:py-20">
        <div class="container-site">
            @if ($posts->isEmpty())
                <p class="text-gray-500">{{ __('No news published yet.') }}</p>
            @else
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>
                <div class="mt-10">{{ $posts->links() }}</div>
            @endif
        </div>
    </section>
</x-layouts.site>
