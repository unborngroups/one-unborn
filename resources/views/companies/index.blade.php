@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">Manage Companies</h3>
       {{-- Add --}}
        <a href="{{ route('companies.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add Company
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow border-0">
        <div class="card-header bg-light d-flex justify-content-between">
            <!-- <h5 class="mb-0 text-danger">MANAGE USER</h5> -->
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">
        </div>
        <div class="card-body table-responsive">
            <!-- <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search..."> -->
            <table class="table table-bordered table-hover align-middle" id="userTable">
                <thead class="table-primary">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>S.No</th>
                        <th>Action</th>
                        <th>Company Name</th>
                        <th>CIN / LLPIN</th>
                        <th>Contact No</th>
                        <th>Email</th>
                        <th>GST No</th>
                        <th>PAN No</th>
                        <!-- <th>TAN No</th>       
                        <th>Logo</th>
                        <th>Normal Sign</th>
                        <th>Digital Sign</th> -->
                        <th>Status</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $index => $company)
                        <tr>
                            <td><input type="checkbox" class="rowCheckbox" value="{{ $company->id }}"></td>
                            <td class="text-center">{{ $index+1 }}</td>
                             <td class="text-center d-flex justify-content-center gap-1">
                                {{-- Edit --}}
                                @if($permissions->can_edit)
                                <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif

                                 {{-- Delete --}}
                                 @if($permissions->can_delete)
                                <form action="{{ route('companies.destroy',$company) }}" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    @method('DELETE') 
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                 @endif

                                {{-- Toggle Status --}}
                                <form action="{{ route('companies.toggle-status', $company) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $company->status === 'Active' ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $company->status }}
                                    </button>
                                </form>
                                <a href="{{ route('companies.email.config', $company->id) }}" class="btn btn-sm btn-warning" title="Email Config">
                                    <i class="bi bi-envelope"></i>
                                </a>
                                 {{-- View --}}
                                   @if($permissions->can_view)
                                 <!-- view path -->
                                   <a href="{{ route('companies.view', $company->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-eye"></i>
                                    </a>
                                    @endif

                                
                            </td>
                            <!-- <td>{{ $index + 1 }}</td> -->
                            <td>{{ $company->company_name }}</td>
                            <td>{{ $company->cin_llpin }}</td>
                            <td>{{ $company->company_phone }}</td>
                            <td>
                                {{ $company->email_1 }}<br>
                                @if($company->email_2)<small>{{ $company->email_2 }}</small>@endif
                            </td>
                            <td>{{ $company->gst_no }}</td>
                            <td>{{ $company->pan_number }}</td>
                            <!-- <td>{{ $company->tan_number }}</td> -->
            
                            <!-- <td>
                                @if($company->billing_logo)
                                    <img src="{{ asset('images/logos/'.$company->billing_logo) }}" alt="Logo" class="rounded-circle border" width="40" height="40">
                                @else
                                    <span class="text-muted small">No logo</span>
                                @endif

                            </td>
                            <td>
                                 @if($company->billing_sign_normal)
                                    <img src="{{ asset('images/n_signs/'.$company->billing_sign_normal) }}" alt="Normal Sign" class="rounded-circle border" width="40" height="40">
                                @else
                                    <span class="text-muted small">No sign</span>
                                @endif
                            </td>
                            <td>
                                @if($company->billing_sign_digital)
                                    <img src="{{ asset('images/d_signs/'.$company->billing_sign_digital) }}" alt="Digital Sign" class="rounded-circle border" width="40" height="40">
                                @else
                                    <span class="text-muted small">No sign</span>
                                @endif
                            </td> -->
                            <td>
                                <span class="badge bg-{{ $company->status === 'Active' ? 'success' : 'danger' }}">
                                    {{ $company->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center">No Companies Found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>  
</div>
<script>
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    document.querySelectorAll('#userTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
    });
});


document.getElementById('selectAll').addEventListener('change', function(){
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = isChecked);
});


</script>
@endsection
