@error($name)
    <div class="field-error is-visible" id="{{ $name }}-error" role="alert" data-error-field="{{ $name }}">{{ $message }}</div>
@enderror
