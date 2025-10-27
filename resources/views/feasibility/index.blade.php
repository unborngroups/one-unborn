@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Feasibility List</h4>
        {{-- Add --}}
        @if($permissions->can_add)
        <a href="{{ route('feasibility.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add Feasibility
        </a>
         @endif
       
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between">
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped align-middle" id="feasibility">
                <thead class="table-primary">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>S.No</th>
                        <th>Action</th>
                        <th>feasibility Name</th>
                        <th>Type of Service</th>
                        <th>State</th>
                        <th>District</th>
                        <th>Vendor Type</th>
                        <th>Speed</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feasibilities as $key => $feasibility)
                        <tr>
                             <td><input type="checkbox" class="rowCheckbox" value="{{ $feasibility->id }}"></td>
                            <td class="text-center">{{ $key+1 }}</td>

                            <td class="text-center d-flex justify-content-center gap-1">
                                {{-- Edit --}}
                                @if($permissions->can_edit)
                               <a href="{{ route('feasibility.edit', $feasibility) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif

                                {{-- Delete --}}
                                 @if($permissions->can_delete)
                                 <form action="{{ route('feasibility.destroy',$feasibility) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE') 
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this feasibility?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                   @endif

                                   {{-- Toggle Status --}}
                                <form action="{{ route('feasibility.toggle-status', $feasibility->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                     <button type="submit" class="btn btn-sm {{ $feasibility->status == 'Active' ? 'btn-success' : 'btn-secondary' }}">
                                {{ $feasibility->status }}
                                    </button>
                                </form>

                                {{-- View --}}
                                   @if($permissions->can_view)
                                   <a href="{{ route('feasibility.view', $feasibility->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-eye"></i>
                                    </a>
                                     @endif
                            </td>
                             <td>
                                 <strong>{{ $feasibility->client->client_name ?? 'N/A' }}</strong><br>
                                 <!-- <small class="text-muted">{{ $feasibility->area }}, {{ $feasibility->district }}</small> -->
                             </td>
                            <td>{{ $feasibility->type_of_service }}</td>
                            <td>{{ $feasibility->state }}</td>
                            <td>{{ $feasibility->district }}</td>
                            <td>{{ $feasibility->vendor_type }}</td>
                            <td>{{ $feasibility->speed }}</td>
                            <td>
                                <span class="badge {{ $feasibility->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                {{ $feasibility->status }}
                                </span>
                            </td>
                            
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted">No records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    document.querySelectorAll('#feasibility tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
    });
});


document.getElementById('selectAll').addEventListener('change', function(){
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = isChecked);
});


</script>
@endsection
