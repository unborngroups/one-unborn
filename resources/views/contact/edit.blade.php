@extends('layouts.app')

@section('title', 'Edit ' . ucfirst($type) . ' Contact')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">Edit {{ ucfirst($type) }} Contact</h4>
        <a href="{{ route('contacts.' . $type . '.index') }}" class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('contacts.update', [$type, $contact->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="{{ old('status', strtolower($contact->status)) }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $contact->name) }}" placeholder="Enter name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Area</label>
                        <input type="text" name="area" class="form-control" value="{{ old('area', $contact->area) }}" placeholder="Enter area">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $contact->state) }}" placeholder="Enter state">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact1 <span class="text-danger">*</span></label>
                        <input type="text" name="contact1" class="form-control" value="{{ old('contact1', $contact->contact1) }}" placeholder="Enter primary number" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact2</label>
                        <input type="text" name="contact2" class="form-control" value="{{ old('contact2', $contact->contact2) }}" placeholder="Enter alternate number">
                    </div>
                    <div class="col-md-6">
                        <label for="form-label fw-semibold">Remarks</label>
                        <input type="text" name="remarks" class="form-control" value="{{ old('remarks', $contact->remarks) }}" placeholder="Enter remarks">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
