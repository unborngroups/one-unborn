@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
	<div class="card shadow border-0">
		<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
			<h5 class="mb-0"><i class="bi bi-download me-2"></i>Purchase Excel Download</h5>
			<button id="downloadExcelBtn" class="btn btn-success btn-sm d-none">
				<i class="bi bi-file-earmark-excel"></i> Download Excel
			</button>
		</div>
		<div class="card-body">
			<form method="GET" class="d-flex align-items-center gap-2 mb-3 w-100">
				<label for="entriesSelect" class="mb-0">Show</label>
				<select id="entriesSelect" name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
					<option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
					<option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
					<option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
					<option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
				</select>
				<input type="text" name="search" id="tableSearch" class="form-control form-control-sm w-25 ms-2" placeholder="Search..." value="{{ request('search') ?? '' }}" oninput="this.form.submit()">
			</form>
			@if(isset($purchases) && $purchases->count() > 0)
				<form id="excelDownloadForm" method="POST" action="{{ route('finance.purchases.exportExcel') }}">
					@csrf
					<div class="table-responsive">
						<table class="table table-bordered table-hover align-middle text-center">
							<thead class="table-dark">
								<tr>
									<th><input type="checkbox" id="selectAll"></th>
									<th>#</th>
									<th>PO Number</th>
									<th>Vendor Name</th>
									<th>Invoice Number</th>
									<th>Invoice Date</th>
									<th>Amount</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								@foreach($purchases as $index => $purchase)
								<tr>
									<td><input type="checkbox" name="selected[]" value="{{ $purchase->id }}" class="rowCheckbox"></td>
									<td>{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $loop->iteration }}</td>
									<td>{{ $purchase->po_number ?? 'N/A' }}</td>
									<td>{{ $purchase->vendor_name ?? ($purchase->vendor->vendor_name ?? 'N/A') }}</td>
									<td>{{ $purchase->invoice_number ?? 'N/A' }}</td>
									<td>{{ $purchase->invoice_date ?? 'N/A' }}</td>
									<td>{{ $purchase->amount ?? 'N/A' }}</td>
									<td>{{ $purchase->status ?? 'N/A' }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
						<div class="text-muted small">
							Showing
							{{ $purchases->firstItem() ?? 0 }}
							to
							{{ $purchases->lastItem() ?? 0 }}
							of
							{{ number_format($purchases->total()) }} entries
						</div>
						<div class="ms-auto">
							{{ $purchases->appends(request()->except('page'))->links() }}
						</div>
					</div>
				</form>
			@else
				<div class="text-center py-5">
					<i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
					<h5 class="text-muted mt-3">No Purchases Found</h5>
				</div>
			@endif
		</div>
	</div>
</div>

@push('scripts')
<script>
	const selectAll = document.getElementById('selectAll');
	const checkboxes = document.querySelectorAll('.rowCheckbox');
	const downloadBtn = document.getElementById('downloadExcelBtn');
	const form = document.getElementById('excelDownloadForm');

	if (selectAll) {
		selectAll.addEventListener('change', function() {
			checkboxes.forEach(cb => cb.checked = selectAll.checked);
			toggleDownloadBtn();
		});
	}
	checkboxes.forEach(cb => {
		cb.addEventListener('change', toggleDownloadBtn);
	});
	function toggleDownloadBtn() {
		const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
		if (anyChecked) {
			downloadBtn.classList.remove('d-none');
		} else {
			downloadBtn.classList.add('d-none');
		}
	}
	if (downloadBtn) {
		downloadBtn.addEventListener('click', function(e) {
			e.preventDefault();
			form.submit();
		});
	}
</script>
@endpush
@endsection
