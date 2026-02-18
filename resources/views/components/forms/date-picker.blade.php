<div class="mb-5">
    <label class="form-label{{ $required ? ' required' : '' }}" for="{{ $inputId() }}">
        {{ $label }}
    </label>
    <div class="input-group">
        <span class="input-group-text">
            <x-ui.icon name="calendar" class="fs-4" />
        </span>
        <input
            type="text"
            id="{{ $inputId() }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder ?? $format }}"
            {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
            data-datepicker="true"
            data-datepicker-options="{{ $flatpickrOptions() }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            autocomplete="off"
        >
    </div>
    @if($hint)
        <div class="form-text text-muted">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
