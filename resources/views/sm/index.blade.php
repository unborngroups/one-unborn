@extends('layouts.app') 

@section('content') 

<div class="container-fluid py-4">
    {{-- ✅ Full-width Bootstrap container with padding on top and bottom --}}
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">User Type</h3>
        {{-- Add --}}
        @if($permissions->can_add)
        <a href="{{ route('usertypetable.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New User
        </a>
         @endif
    </div>

    @if(session('success'))
        {{-- ✅ Show success message after adding/updating/deleting --}}
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-lg border-0">
        <div class="card-header bg-light d-flex justify-content-between">
            <h5 class="mb-0 text-danger">USER TYPE</h5>
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">
        </div>

        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0" id="userTable">
                <thead class="table-primary text-center">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        {{-- ✅ Checkbox to select all rows --}}
                        <th>S.No</th>
                        <th>Action</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    {{-- ✅ Table body: loop through user type records --}}
                    @foreach($usertypetable as $index => $usertypedata)
                        <tr>
                            <td><input type="checkbox" class="rowCheckbox" value="{{ $usertypedata->id }}"></td>
                            <td class="text-center">{{ $index+1 }}</td>

                            <td class="text-center d-flex justify-content-center gap-1">

                            {{-- Edit --}}
                                @if($permissions->can_edit)
                                {{-- ✏️ Edit button --}}
                                <a href="{{ route('usertypetable.edit', $usertypedata) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif

                                 {{-- Delete --}}
                                 @if($permissions->can_delete)

                                {{-- 🗑 Delete button --}}
                                <form action="{{ route('usertypetable.destroy', $usertypedata) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE') 
                                    {{-- ✅ Use DELETE HTTP verb --}}
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                 @endif

                                {{-- 🔁 Toggle Status button --}}
                                <form action="{{ route('usertypetable.toggle-status', $usertypedata->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    {{-- ✅ PATCH request to update status --}}
                                    <button type="submit" class="btn btn-sm {{ $usertypedata->status == 'Active' ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $usertypedata->status }}
                                    </button>
                                </form>

                            </td>

                            {{-- 🧾 Data columns --}}
                            <td>{{ $usertypedata->name }}</td>
                            <td>{{ $usertypedata->Description ?? '-'}}</td>
                            {{-- ✅ Show description or “-” if null --}}

                            <td class="text-center">
                                {{-- ✅ Display status badge --}}
                                <span class="badge {{ $usertypedata->status=='Active'?'bg-success':'bg-secondary' }}">
                                    {{ $usertypedata->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach

                    {{-- ⚠️ If no records found --}}
                    @if($usertypetable->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center text-muted">No Users Found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 🧠 Script for table search and select all functionality --}}
<script>
document.getElementById('tableSearch').addEventListener('keyup', function() {
    // ✅ Filter table rows by search value
    let value = this.value.toLowerCase();
    document.querySelectorAll('#userTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
    });
});

document.getElementById('selectAll').addEventListener('change', function(){
    // ✅ Select or deselect all checkboxes
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = isChecked);
});
</script>

@endsection 
{{-- ✅ Ends the content section --}}
