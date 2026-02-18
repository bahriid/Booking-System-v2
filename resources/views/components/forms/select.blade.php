<div class="mb-5">
    <label class="form-label{{ $required ? ' required' : '' }}" for="{{ $selectId() }}">
        {{ $label }}
    </label>
    <select
        id="{{ $selectId() }}"
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        {{ $attributes->merge(['class' => 'form-select' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($multiple) multiple @endif
        data-control="select2"
        data-placeholder="{{ $placeholder ?? 'Select an option' }}"
    >
        @if($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif
        @if(count($options) > 0)
            @foreach($options as $value => $optionLabel)
                <option value="{{ $value }}" @if($isSelected($value)) selected @endif>
                    {{ $optionLabel }}
                </option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>
    @if($hint)
        <div class="form-text text-muted">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
