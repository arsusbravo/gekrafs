@props(['locales', 'locale'])

<details class="group relative" data-dropdown>
    <summary class="flex cursor-pointer list-none items-center gap-2 rounded-full border border-gray-200 px-3 py-1.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 [&::-webkit-details-marker]:hidden">
        <img src="{{ asset('storage/images/'.$locale.'.png') }}" alt="" class="h-5 w-5 rounded-full">
        {{ strtoupper($locale) }}
        <x-icon name="chevron-down" class="h-4 w-4 transition group-open:rotate-180" />
    </summary>
    <div class="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-gray-100 bg-white py-1 shadow-lg">
        <p class="px-4 pt-2 pb-1 text-xs font-semibold tracking-wide text-gray-400 uppercase">{{ __('Language') }}</p>
        @foreach ($locales as $code => $name)
            <a href="{{ route('locale', $code) }}" @class(['flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-50', 'font-semibold text-brand-700' => $code === $locale, 'text-gray-700' => $code !== $locale])>
                <img src="{{ asset('storage/images/'.$code.'.png') }}" alt="" class="h-5 w-5 rounded-full">
                {{ $name }}
            </a>
        @endforeach
    </div>
</details>
