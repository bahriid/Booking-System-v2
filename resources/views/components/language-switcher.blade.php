@props(['class' => ''])

@php
    $currentLocale = app()->getLocale();
    $locales = [
        'en' => ['name' => 'English', 'flag' => 'united-states'],
        'it' => ['name' => 'Italiano', 'flag' => 'italy'],
    ];
@endphp

<div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
    <a href="#" class="menu-link px-5">
        <span class="menu-title position-relative">
            {{ __('nav.language') }}
            <span class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
                {{ strtoupper($currentLocale) }}
                <img class="w-15px h-15px rounded-1 ms-2" src="{{ asset('assets/media/flags/' . $locales[$currentLocale]['flag'] . '.svg') }}" alt="">
            </span>
        </span>
    </a>
    <div class="menu-sub menu-sub-dropdown w-175px py-4">
        @foreach ($locales as $code => $locale)
            <div class="menu-item px-3">
                <a href="{{ route('language.switch', $code) }}" class="menu-link d-flex px-5 {{ $currentLocale === $code ? 'active' : '' }}">
                    <span class="symbol symbol-20px me-4">
                        <img class="rounded-1" src="{{ asset('assets/media/flags/' . $locale['flag'] . '.svg') }}" alt="">
                    </span>
                    {{ $locale['name'] }}
                </a>
            </div>
        @endforeach
    </div>
</div>
