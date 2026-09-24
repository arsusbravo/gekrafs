@props(['eyebrow' => null, 'title', 'link' => null, 'linkLabel' => null, 'dark' => false])

<div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        @if ($eyebrow)
            <p @class(['eyebrow', 'text-brand-600' => ! $dark, 'text-accent-400' => $dark])>
                <span class="h-0.5 w-8 bg-current"></span>{{ $eyebrow }}
            </p>
        @endif
        <h2 @class(['section-title mt-2', 'text-brand-950' => ! $dark, 'text-white' => $dark])>{{ $title }}</h2>
    </div>
    @if ($link)
        <a href="{{ $link }}" @class(['inline-flex items-center gap-2 text-sm font-semibold', 'text-brand-600 hover:text-brand-800' => ! $dark, 'text-accent-400 hover:text-accent-300' => $dark])>
            {{ $linkLabel }} <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    @endif
</div>
