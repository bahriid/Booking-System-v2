<div class="mb-5">
    <label class="form-label{{ $required ? ' required' : '' }}" for="{{ $inputId() }}">
        {{ $label }}
    </label>
    @if($icon)
        <div class="input-group">
            <span class="input-group-text">
                <x-ui.icon :name="$icon" class="fs-4" />
            </span>
            <input
                type="{{ $type }}"
                id="{{ $inputId() }}"
                name="{{ $name }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
            >
        </div>
    @else
        <input
            type="{{ $type }}"
            id="{{ $inputId() }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
        >
    @endif
    @if($hint)
        <div class="form-text text-muted">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
