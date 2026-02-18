<div class="mb-5">
    <div class="form-check form-check-custom form-check-solid">
        <input
            type="checkbox"
            id="{{ $checkboxId() }}"
            name="{{ $name }}"
            value="{{ $value }}"
            {{ $attributes->merge(['class' => 'form-check-input' . ($errors->has($name) ? ' is-invalid' : '')]) }}
            @if($isChecked()) checked @endif
            @if($disabled) disabled @endif
        >
        <label class="form-check-label" for="{{ $checkboxId() }}">
            {{ $label }}
        </label>
    </div>
    @if($hint)
        <div class="form-text text-muted ms-9">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
