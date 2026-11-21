<form method="POST" action="{{ $estimator ? route('admin.estimators.update', $estimator) : route('admin.estimators.store') }}">
@csrf
@if ($estimator) @method('PATCH') @endif
<div class="form-row">
<label class="label">Name <input class="input" name="name" value="{{ old('name', $estimator?->name) }}" required></label>
<label class="label">Email <input class="input" type="email" name="email" value="{{ old('email', $estimator?->email) }}" required></label>
<label class="label">Phone <input class="input" type="tel" name="phone" value="{{ old('phone', $estimator?->phone) }}" required></label>
<label class="label">Availability <select class="select" name="active"><option value="1" @selected(old('active', $estimator?->active ?? true))>Active</option><option value="0" @selected(!old('active', $estimator?->active ?? true))>Inactive</option></select></label>
</div>
@foreach (['name','email','phone','active'] as $field) @include('shared.auth-field-error', ['name' => $field]) @endforeach
<button class="btn btn--primary" data-loading-text="Saving estimator…">Save estimator</button>
</form>
