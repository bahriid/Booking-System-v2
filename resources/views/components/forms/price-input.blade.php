<div class="mb-5">
    <label class="form-label{{ $required ? ' required' : '' }}" for="{{ $inputId() }}">
        {{ $label }}
    </label>
    <div class="input-group">
        <span class="input-group-text">{{ $currency }}</span>
        <input
            type="number"
            id="{{ $inputId() }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder ?? '0.00' }}"
            step="{{ $step }}"
            {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
            @if($min !== null) min="{{ $min }}" @endif
            @if($max !== null) max="{{ $max }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
        >
    </div>
    @if($hint)
        <div class="form-text text-muted">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
