<div class="mb-5">
    @if($label)
    <label class="form-label{{ $required ? ' required' : '' }}" for="{{ $textareaId() }}">
        {{ $label }}
    </label>
    @endif
    <textarea
        id="{{ $textareaId() }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
    >{{ old($name, $value) }}</textarea>
    @if($hint)
        <div class="form-text text-muted">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
