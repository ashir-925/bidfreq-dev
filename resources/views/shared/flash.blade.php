@if (session('status'))
    <p role="status" data-feedback-kind="success">{{ session('status') }}</p>
@endif
@if (session('error'))
    <p role="alert" data-feedback-kind="danger">{{ session('error') }}</p>
@endif
@if ($errors->any())
    <p role="alert" data-feedback-kind="danger" data-feedback-title="Check the form">{{ $errors->first() }}</p>
@endif
