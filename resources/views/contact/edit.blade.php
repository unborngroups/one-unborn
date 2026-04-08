@extends('layouts.app')

@section('title', 'Edit ' . ucfirst($type) . ' Contact')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit {{ ucfirst($type) }} Contact</h4>
        <a href="{{ route('contacts.' . $type . '.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('contacts.update', [$type, $contact->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $contact->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Area</label>
                        <input type="text" name="area" class="form-control" value="{{ old('area', $contact->area) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $contact->state) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact1</label>
                        <input type="text" name="contact1" class="form-control" value="{{ old('contact1', $contact->contact1) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact2</label>
                        <input type="text" name="contact2" class="form-control" value="{{ old('contact2', $contact->contact2) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', strtolower($contact->status)) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', strtolower($contact->status)) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
