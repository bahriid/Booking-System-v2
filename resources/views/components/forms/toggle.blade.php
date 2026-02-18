<div class="mb-5">
    <div class="{{ $switchClasses() }}">
        <input
            type="checkbox"
            id="{{ $toggleId() }}"
            name="{{ $name }}"
            value="1"
            {{ $attributes->merge(['class' => 'form-check-input' . ($errors->has($name) ? ' is-invalid' : '')]) }}
            @if($isChecked()) checked @endif
            @if($disabled) disabled @endif
        >
        <label class="form-check-label" for="{{ $toggleId() }}">
            {{ $label }}
        </label>
    </div>
    @if($hint)
        <div class="form-text text-muted ms-13">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
