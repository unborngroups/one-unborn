@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">Manage Users</h3>
        {{-- Add User Button --}}
 {{-- Add --}}
        @if($permissions && $permissions->can_add)
    <a href="{{ route('users.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Add New User
    </a>
@endif
    </div>

    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ✅ Users Table --}}
    <div class="card shadow-lg border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-danger">MANAGE USER</h5>
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover table-bordered align-middle mb-0" id="userTable">
                <thead class="table-primary text-center">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>S.No</th>
                        <th>Action</th>
                        <th>Name</th>
                        <th>User Type</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Company</th>
                        <th>Date of Birth</th>
                        <th>Date of Join</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td><input type="checkbox" class="rowCheckbox" value="{{ $user->id }}"></td>
                            <td class="text-center">{{ $index + 1 }}</td>

                            {{-- ✅ Action Buttons --}}
                            <td class="text-center d-flex justify-content-center gap-1">
                                @php
                                $role = strtolower(auth()->user()->userType->name ?? '');
                                $canManage = in_array($role, ['superadmin', 'admin']);
                                @endphp

                                {{-- Edit --}}
                                @if($permissions && $permissions->can_edit)
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i>
                            </a>
                                @endif

                            {{-- Delete --}}
                                 @if($permissions && $permissions->can_delete)
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                 @csrf
                                 @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                            <i class="bi bi-trash"></i>
                            </button>
                            </form>
                                @endif          
                                
                                {{-- Toggle Status --}}
                                @php
                                $role = strtolower(auth()->user()->userType->name);
                                $canToggle = in_array($role, ['superadmin', 'admin']);
                                @endphp
                                <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $user->status === 'Active' ? 'btn-success' : 'btn-secondary' }}"   {{ !$canToggle ? 'disabled title=You don\'t have permission' : '' }}>
                                    
                                    {{ $user->status }}
                                    </button>
                                    </form>

                                    {{-- View --}}
                                   @if($permissions && $permissions->can_view)
                                   <a href="{{ route('users.view', $user->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-eye"></i>
                                    </a>
                                     @endif

                                     @php
                                     $role = strtolower(auth()->user()->userType->name ?? '');
                                     @endphp
                                     @if($permissions->can_edit && in_array($role, ['superadmin', 'admin']))

                                    <a href="{{ route('menus.editPrivileges', $user->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-gear"></i> 
                                    </a>
                                    @endif
                                     <ul class="sidebar-menu">
      



                            </td>

                            <td>{{ ucfirst($user->name) }}</td>

                            {{-- ✅ User Type Badge --}}
                            <td class="text-center">
                                <span class="badge {{ $user->userType->name === 'superadmin' ? 'bg-dark' : 'bg-info' }}">
                                    {{ ucfirst($user->userType->name ?? '-') }}
                                </span>
                            </td>

                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mobile ?? '-' }}</td>

                            {{-- ✅ Company Names (comma-separated) --}}
                            <td>
                                {{ $user->companies->pluck('company_name')->join(', ') ?: 'No Company Assigned' }}
                            </td>

                            {{-- ✅ Formatted Dates --}}
                            <td>{{ $user->Date_of_Birth ? \Carbon\Carbon::parse($user->Date_of_Birth)->format('d M Y') : '-' }}</td>
                            <td>{{ $user->Date_of_Joining ? \Carbon\Carbon::parse($user->Date_of_Joining)->format('d M Y') : '-' }}</td>

                            {{-- ✅ Status Badge --}}
                            <td class="text-center">
                                <span class="badge {{ $user->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $user->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">No Users Found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ✅ Search + Select-All --}}
<script>
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    document.querySelectorAll('#userTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
    });
});

document.getElementById('selectAll').addEventListener('change', function() {
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = isChecked);
});
</script>
@endsection
