@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-shield-check me-2"></i>Assurance
                    </h3>
                    @if($permissions && $permissions->can_add)
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add New
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Assurance Module</strong> - Quality assurance and compliance tracking system.
                    </div>
                    
                    @if($permissions && $permissions->can_view)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    @if($permissions->can_edit || $permissions->can_delete)
                                    <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="{{ $permissions->can_edit || $permissions->can_delete ? 5 : 4 }}" class="text-center text-muted">
                                        No records found. Add new records to get started.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        You do not have permission to view this content.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
