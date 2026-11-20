@php
    $type = $type ?? 'text';
    $previousValue = old($name, $value ?? '');
@endphp
<div class="form-group" style="margin-bottom: 12px">
    <label class="label" for="{{ $name }}">{{ $label }}</label>
    <input class="input" id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        @if ($type !== 'password') value="{{ is_string($previousValue) ? $previousValue : '' }}" @endif
        @if ($required ?? true) required @endif
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>
    @error($name)
        <p id="{{ $name }}-error" role="alert" class="field-error is-visible">{{ $message }}</p>
    @enderror
</div>
