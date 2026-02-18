<div {{ $attributes->merge(['class' => "notice d-flex bg-light-{$variant} rounded border-{$variant} border" . ($dashed ? ' border-dashed' : '') . ' p-6']) }}>
    <x-ui.icon :name="$icon ?? $defaultIcon()" class="fs-2tx text-{{ $variant }} me-4" />
    <div class="d-flex flex-stack flex-grow-1">
        <div class="fw-semibold">
            @if($title)
                <div class="fs-6 text-gray-800 fw-bold">{{ $title }}</div>
            @endif
            <div class="fs-6 text-gray-700">{{ $slot }}</div>
        </div>
    </div>
</div>
