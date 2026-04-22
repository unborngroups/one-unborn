@extends('layouts.app')

@section('title', ucfirst($type) . ' Contact Details')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ ucfirst($type) }} Contact Details</h4>
        <a href="{{ route('contacts.' . $type . '.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted">Name</label>
                    <div class="fw-semibold">{{ $contact->name }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Area</label>
                    <div class="fw-semibold">{{ $contact->area ?: '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">State</label>
                    <div class="fw-semibold">{{ $contact->state ?: '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Contact1</label>
                    <div class="fw-semibold">{{ $contact->contact1 }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Contact2</label>
                    <div class="fw-semibold">{{ $contact->contact2 ?: '-' }}</div>
                </div>
                <div class="col-md-6">
                        <label for="form-label fw-semibold">Remarks</label>
                        <div class="fw-semibold">{{ $contact->remarks ?: '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Status</label>
                    <div>
                        <span class="badge {{ strtolower($contact->status) === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst(strtolower($contact->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
