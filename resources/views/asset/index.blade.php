@extends('layouts.app')
@php use Milon\Barcode\Facades\DNS1DFacade as DNS1D; @endphp
@section('content')

<div class="container">
<h3 class="mb-3">Asset List</h3>

    @if($permissions->can_add)

        <a href="{{ route('asset.create') }}" class="btn btn-success float-end mb-3">

            <i class="bi bi-plus-circle"></i> Add New Asset

        </a>

         @endif
{{-- 🔍 Search box --}}

        <div class="card-header bg-light d-flex justify-content-between">

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">

        </div>

<table class="table table-bordered" id="asset">
<thead class="table-dark-primary">
<tr>
    <th>S.No</th>
    <th>Action</th>
    <th>Asset ID / Barcode</th>
    <th>Brand</th>
    <th>Model</th>
    <th>Serial No</th>
    <th>Purchase Date</th>
    
</tr>
</thead>

<tbody>
@foreach($assets as $a)
<tr>
    <td>
        {{ $assets instanceof \Illuminate\Pagination\LengthAwarePaginator 
            ? $loop->iteration + ($assets->currentPage() - 1) * $assets->perPage() 
            : $loop->iteration }}
    </td>
    <td>
        <!-- edit action -->
    @if ($permissions->can_edit)
    <a href="{{ route('asset.edit', $a->id) }}" class="btn btn-sm btn-primary">Edit</a>
    @endif

        <!-- delete action -->
    @if ($permissions->can_delete)
            <form action="{{ route('asset.destroy', $a->id) }}" method="POST" style="display:inline-block;">
    @csrf
    @method('DELETE')
    <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
</form>
        @endif
        
 {{-- View --}}

                                   @if($permissions->can_view)

                                   <a href="{{ route('asset.view', $a->id) }}" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     @endif
           
    </td>

    <td>
        <p>{!! DNS1D::getBarcodeHTML($a->asset_id, 'C128', 1.4, 40) !!} 
        {{ $a->asset_id }}</p>
    </td>

    <td>{{ $a->brand }}</td>
    <td>{{ $a->model }}</td>
    <td>{{ $a->serial_no }}</td>
    <td>{{ $a->purchase_date }}</td>
    
</tr>
@endforeach
</tbody>
</table>
</div>
<script>
    
document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#asset tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});
</script>

@endsection
