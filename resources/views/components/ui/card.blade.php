<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || isset($header) || isset($toolbar))
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                @if($icon)
                    <span class="me-2">
                        <x-ui.icon :name="$icon" class="fs-2 text-primary" />
                    </span>
                @endif
                @if($title)
                    {{ $title }}
                @endif
                {{ $header ?? '' }}
            </div>
            @if(isset($toolbar))
                <div class="card-toolbar">
                    {{ $toolbar }}
                </div>
            @endif
        </div>
    @endif
    <div class="card-body {{ $flush ? 'p-0' : 'py-4' }}">
        {{ $slot }}
    </div>
    @if(isset($footer))
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
