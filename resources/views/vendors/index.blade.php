@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">Vendor Master</h3>
         {{-- ✅ Show “Add Vendor” button only if user has permission --}}
        @if($permissions->can_add)
        <a href="{{ route('vendors.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Vendor
        </a>
         @endif
    </div>

    {{-- ✅ Success message after create/update/delete --}}
    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

     {{-- 🧾 Vendor Table Section --}}
    <div class="card shadow border-0">
          {{-- 🔍 Search box --}}
        <div class="card-header bg-light d-flex justify-content-between">
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">
        </div>
        {{-- 📊 Table with vendor data --}}
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle" id="vendorTable">
                <thead class="table-primary">
                    <tr>
                        {{-- ✅ Checkbox for bulk select --}}
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>S.No</th>
                        <th>Action</th>
                        <th>Vendor Code</th>
                        <th>Vendor Name</th>
                        <th>Business Name</th>
                        <th>Contact Person</th>
                        <th>Contact Email</th>
                        <th>Contact Mobile</th>
                        <th>GSTIN</th>
                        <th>PAN No</th>
                        <th>bank_account_no</th>
                        <th>ifsc_code</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $key => $vendor)
                        <tr>
                            <td><input type="checkbox" class="rowCheckbox" value="{{ $vendor->id }}"></td>
                            <td class="text-center">{{ $key+1 }}</td>
                            <td class="text-center d-flex justify-content-center gap-1">
                                 {{-- ✏️ Edit button (only if permission allowed) --}}
                                @if($permissions->can_edit)
                                <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif

                               {{-- 🗑️ Delete button --}}
                                 @if($permissions->can_delete)
                                <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE') 
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Vendor?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif

                                {{-- 🔁 Toggle Active/Inactive --}}
                                <form action="{{ route('vendors.toggle-status', $vendor->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $vendor->status == 'Active' ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $vendor->status }}
                                    </button>
                                </form>
                                 {{-- View button --}}
                                   @if($permissions->can_view)
                                <!-- view path -->
                                   <a href="{{ route('vendors.view', $vendor->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-eye"></i>
                                    </a>
                                    @endif
                                
                            </td>
                            <td>{{ $vendor->vendor_code }}</td>
                            <td>{{ $vendor->vendor_name }}</td>
                            <td>{{ $vendor->business_display_name ?? '-' }}</td>
                            <td>{{ $vendor->contact_person_name ?? '-' }}</td>
                            <td>{{ $vendor->contact_person_email ?? '-' }}</td>
                            <td>{{ $vendor->contact_person_mobile ?? '-' }}</td>
                            <td>{{ $vendor->gstin ?? '-' }}</td>
                            <td>{{ $vendor->pan_no ?? '-' }}</td>
                            <td>{{ $vendor->bank_account_no ?? '-'}}</td>
                            <td>{{ $vendor->ifsc_code ?? '-'}}</td>
                            {{-- 🟢🔴 Status Badge --}}
                            <td>
                                <span class="badge {{ $vendor->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $vendor->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted">No vendors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
 {{-- 🔍 Table Search & Select All Script --}}
<script>
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    document.querySelectorAll('#vendorTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
    });
});

// ✅ Select / Deselect all checkboxes
document.getElementById('selectAll').addEventListener('change', function(){
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = isChecked);
});
</script>
@endsection
