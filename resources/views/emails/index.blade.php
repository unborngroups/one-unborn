@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">Email Templates</h3>
         {{-- Add --}}
        @if($permissions->can_add)
        <a href="{{ route('emails.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> New Template
        </a>
        @endif
    </div>

    @if(session('success'))
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
                            <th>S.No</th>
                        <th>Action</th>
                        <th>Company Name</th>
                        <th>Subject</th>
                        <th>Status</th>          
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $index => $template)
                        <tr>
                                <td><input type="checkbox" class="rowCheckbox" value="{{ $template->id }}"></td>
                            <td class="text-center">{{ $index+1 }}</td>
                         
                            <td class="text-center d-flex justify-content-center gap-1">
                                {{-- Edit --}}
                                @if($permissions->can_edit)
                                <a href="{{ route('emails.edit', $template->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                 @endif

                                 {{-- Delete --}}
                                 @if($permissions->can_delete)
                                <form action="{{ route('emails.destroy', $template->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this template?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                 @endif

                                <form action="{{ route('templates.toggle-status', $template->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $template->status == 'Active' ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $template->status }}
                                    </button>
                                </form>
                            </td>
                             <td>{{ $template->company ? $template->company->company_name : '-' }}</td>
                            <td>{{ $template->subject }}</td>
                            <td class="text-center">
                                <span class="badge {{ $template->status=='Active'?'bg-success':'bg-secondary' }}">
                                    {{ $template->status }}
                                </span>
                            </td>
                         
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No templates found.</td></tr>
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
