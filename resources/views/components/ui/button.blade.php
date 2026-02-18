<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes()]) }}>
    @if($icon)
        <x-ui.icon :name="$icon" class="fs-4 {{ $iconOnly ? '' : 'me-2' }}" />
    @endif
    @unless($iconOnly)
        {{ $slot }}
    @endunless
</button>
